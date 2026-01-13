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
        if ($user->hasRole('admin')) {
            $appointments = Appointment::with(['employee.user', 'service', 'user'])
                ->orderBy('booking_date', 'asc')
                ->orderBy('booking_start_time', 'asc')
                ->get();

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
                        'title' => sprintf('%s - %s',
                            $appointment->name,
                            $appointment->service->title ?? 'Service'
                        ),
                        'start' => $startDateTime->toIso8601String(),
                        'end' => $endDateTime->toIso8601String(),
                        'description' => $appointment->notes,
                        'email' => $appointment->email,
                        'phone' => $appointment->phone,
                        'amount' => $appointment->amount,
                        'status' => $appointment->status,
                        'staff' => $appointment->employee->user->name ?? 'Unassigned',
                        'color' => $this->getStatusColor($appointment->status),
                        'service_title' => $appointment->service->title ?? 'Service',
                        'name' => $appointment->name,
                        'notes' => $appointment->notes,
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

    // Helper warna status appointment
    private function getStatusColor($status)
    {
        $colors = [
            'Pending' => '#f39c12',
            'Processing' => '#3498db',
            'Confirmed' => '#2ecc71',
            'Cancelled' => '#ff0000',
            'Completed' => '#008000',
            'On Hold' => '#95a5a6',
            'Rescheduled' => '#f1c40f',
            'No Show' => '#e67e22',
        ];

        return $colors[$status] ?? '#7f8c8d';
    }

    // Update status appointment
    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending,Processing,Confirmed,Cancelled,Completed,On Hold,No Show'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new \App\Events\StatusUpdated($appointment));

        return back()->with('success', 'Status updated successfully');
    }
}
