<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Coupon;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = Setting::firstOrFail();
        $user = auth()->user();

        /**
         * ===========================================================
         * ADMIN DASHBOARD
         * ===========================================================
         */
        if ($user->hasRole(['admin', 'moderator', 'employee'])) {

            $query = Appointment::with([
                'employee.user',
                'service',
                'user',
                'background' // tambahkan ini
            ])
                ->orderBy('booking_date', 'asc')
                ->orderBy('booking_start_time', 'asc');

            // 🔐 FILTER KHUSUS EMPLOYEE
            if ($user->hasRole('employee')) {

                if (!$user->employee) {
                    abort(403, 'Employee profile not found.');
                }

                $query->where('employee_id', $user->employee->id);
            }

            $appointments = $query->get();


            $appointments = $appointments->map(function ($appointment) {
                try {
                    $bookingDate = Carbon::parse($appointment->booking_date);

                    // Pastikan start dan end time ada
                    if (!$appointment->booking_start_time || !$appointment->booking_end_time) {
                        throw new \Exception("Missing start or end time");
                    }

                    // Parsing start & end datetime
                    $startDateTime = Carbon::parse($bookingDate->format('Y-m-d') . ' ' . $appointment->booking_start_time);
                    $endDateTime = Carbon::parse($bookingDate->format('Y-m-d') . ' ' . $appointment->booking_end_time);

                    // Jika end time lebih kecil dari start (lewat tengah malam)
                    if ($endDateTime->lt($startDateTime)) {
                        $endDateTime->addDay();
                    }

                    return [
                        'id' => $appointment->id,
                        'title' => sprintf(
                            '%s - %s',
                            $appointment->name,
                            $appointment->service->title ?? 'Service'
                        ),
                        'start' => $startDateTime->toIso8601String(),
                        'end' => $endDateTime->toIso8601String(),
                        'description' => $appointment->notes,
                        'email' => $appointment->email,
                        'phone' => $appointment->phone,
                        'status' => $appointment->status,
                        'employee' => $appointment->employee->user->name ?? 'Unassigned',
                        'color' => $this->getStatusColor($appointment->status),
                        'service_title' => $appointment->service->title ?? 'Service',
                        'name' => $appointment->name,
                        'notes' => $appointment->notes,

                        // ✅ TAMBAHAN BARU
                        'background_id' => $appointment->background_id,
                        'background_name' => $appointment->background->name ?? null,
                        'people_count' => $appointment->people_count,
                    ];
                } catch (\Exception $e) {
                    \Log::error("Format error for appointment {$appointment->id}: {$e->getMessage()}");
                    return null;
                }
            })->filter();

            return view('backend.dashboard.index', compact('appointments'));
        }

        /**
         * ===========================================================
         * MEMBER DASHBOARD
         * ===========================================================
         */


        $transactions = Transaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $coupons = Coupon::where('user_id', $user->id)
            ->where('active', 1)
            ->get();

        $usedCoupons = Coupon::where('user_id', $user->id)
            ->where('status', 'used')
            ->count();

        return view('frontend.member.dashboard', compact('transactions', 'coupons', 'usedCoupons'));
    }

    private function getStatusColor($status)
    {
        $colors = [
            'Pending'     => '#d68910', // lebih gelap dari #f39c12
            'Processing'  => '#1f6fa3', // lebih gelap dari #3498db
            'Confirmed'   => '#1e9e5a', // lebih gelap dari #2ecc71
            'Cancelled'   => '#c0392b', // lebih gelap dari #e74c3c
            'Completed'   => '#117a65', // lebih gelap dari #16a085
            'Rescheduled' => '#6c3483', // lebih gelap dari #9b59b6
            'On Hold'     => '#566573', // lebih gelap dari #7f8c8d
            'No Show'     => '#ba6f1a', // lebih gelap dari #e67e22
        ];

        return $colors[$status] ?? '#566573';
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending,Processing,Confirmed,Cancelled,Completed,Rescheduled,On Hold,No Show'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new \App\Events\StatusUpdated($appointment));

        return back()->with('success', 'Status berhasil diperbarui');
    }
}
