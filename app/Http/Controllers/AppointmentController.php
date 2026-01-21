<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Addon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::orderBy('booking_date', 'asc')
            ->orderBy('booking_start_time', 'asc')
            ->with('addons') // pastikan eager load
            ->get();

        // Tambahkan addonData untuk setiap appointment
        $appointments->each(function ($appointment) {
            $appointment->addonData = $appointment->addons->map(function ($a) {
                return [
                    'name' => $a->name,
                    'qty' => $a->pivot->qty,
                    'price' => $a->pivot->price,
                    'subtotal' => $a->pivot->subtotal,
                ];
            });
        });

        return view('backend.appointment.index', compact('appointments'));
    }



    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_id' => 'required|exists:employees,id',
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'amount' => 'nullable',
            'booking_date' => 'required|date',
            'booking_start_time' => 'required',
            'booking_end_time' => 'required',
            'status' => 'required|in:Pending,Processing,Confirmed,Completed,Cancelled,Rescheduled',
            'payment_status' => 'nullable|in:Pending,DP,Paid,Failed',
            'people_count' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
            'midtrans_order_id' => 'nullable|string',
            'coupon_id' => 'nullable|exists:coupons,id',
            'background_id' => 'nullable|exists:service_backgrounds,id',
            'slot_group_id' => 'required|exists:slot_groups,id',
            'addons' => 'nullable|array',
            'addons.*.id' => 'required|exists:addons,id',
            'addons.*.price' => 'nullable',
            'addons.*.qty' => 'required|integer|min:1',
        ]);

        // Tentukan user_id
        if (auth()->check()) {
            $user = auth()->user();
            $validated['user_id'] = ($user->hasRole('admin') || $user->hasRole('moderator') || $user->hasRole('employee'))
                ? null
                : $user->id;
        } else {
            $validated['user_id'] = null;
        }

        // Generate booking_id & transaction_code
        $validated['booking_id'] = 'BK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        $transactionCode = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));

        // Cek bentrok slot sebelum simpan
        $current = \Carbon\Carbon::parse($validated['booking_start_time']);
        $slotEnd = \Carbon\Carbon::parse($validated['booking_end_time']);

        $isBooked = Appointment::where('employee_id', $validated['employee_id'])
            ->where('slot_group_id', $validated['slot_group_id'])
            ->where('booking_date', $validated['booking_date'])
            ->whereNotIn('status', ['Cancelled'])
            ->where(function ($q) use ($current, $slotEnd) {
                $q->where('booking_start_time', '<', $slotEnd->format('H:i:s'))
                    ->where('booking_end_time', '>', $current->format('H:i:s'));
            })
            ->exists();

        if ($isBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Slot sudah dibooking. Silakan pilih waktu lain.'
            ], 422);
        }


        // Simpan appointment
        $appointment = Appointment::create([
            'user_id' => $validated['user_id'],
            'employee_id' => $validated['employee_id'],
            'service_id' => $validated['service_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'notes' => $validated['notes'] ?? null,
            'amount' => $validated['amount'],
            'booking_date' => $validated['booking_date'],
            'booking_start_time' => $validated['booking_start_time'],
            'booking_end_time' => $validated['booking_end_time'],
            'status' => $validated['status'],
            'people_count' => $validated['people_count'],
            'booking_id' => $validated['booking_id'],
            'background_id' => $request->background_id,
            'slot_group_id' => $validated['slot_group_id'],
        ]);

        if ($request->filled('addons')) {
            foreach ($request->addons as $addon) {
                if (($addon['qty'] ?? 0) > 0) {
                    $addonModel = Addon::findOrFail($addon['id']);

                    $appointment->addons()->attach($addonModel->id, [
                        'price' => $addonModel->price,
                        'qty' => $addon['qty'],
                        'subtotal' => $addonModel->price * $addon['qty'],
                    ]);
                }
            }
        }


        // Simpan transaksi
        $transaction = Transaction::create([
            'user_id' => $validated['user_id'],
            'appointment_id' => $appointment->id,
            'transaction_code' => $transactionCode,
            'amount' => $validated['amount'],
            'total_amount' => $validated['total_amount'] ?? $validated['amount'],
            'payment_status' => $validated['payment_status'] ?? 'Pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'midtrans_order_id' => $validated['midtrans_order_id'] ?? null,
            'coupon_id' => $validated['coupon_id'] ?? null,
        ]);

        try {
            $qrName = $transactionCode . '.png';
            $qrFolder = public_path('qrcodes');

            if (!file_exists($qrFolder)) {
                mkdir($qrFolder, 0777, true);
            }

            $qrPath = $qrFolder . '/' . $qrName;
            $qrUrl  = url('qrcodes/' . $qrName);

            // Gunakan backend PNG tanpa imagick
            $pngData = QrCode::format('png')
                ->size(300)
                ->margin(1)
                ->generate($transactionCode);

            file_put_contents($qrPath, $pngData);

            $transaction->update([
                'qr_url' => $qrUrl,
            ]);
        } catch (\Exception $e) {
            \Log::error('QR Code generation error: ' . $e->getMessage());
        }



        // Kirim WhatsApp
        $this->sendWhatsappNotification($appointment);

        // Event
        event(new BookingCreated($appointment));

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully!',
            'booking_id' => $appointment->booking_id,
            'qr_url' => $transaction->qr_url ?? null,
            'transaction_code' => $transactionCode,
            'appointment' => $appointment
        ]);
    }


    private function sendWhatsappNotification(Appointment $appointment)
    {
        try {
            $token = env('FONNTE_TOKEN');
            if (!$token) return;

            $phone = preg_replace('/^0/', '62', $appointment->phone);

            $transaction = $appointment->transaction;
            $qrUrl = $transaction->qr_url ?? '-';
            $trxCode = $transaction->transaction_code ?? '-';

            $serviceName = optional($appointment->service)->title ?? 'Layanan';
            $total = number_format($appointment->amount, 0, ',', '.');

            $tanggal = \Carbon\Carbon::parse($appointment->booking_date)
                ->translatedFormat('l, d M Y');

            $start = date('H:i', strtotime($appointment->booking_start_time));
            $end   = date('H:i', strtotime($appointment->booking_end_time));

            $message =
                "🌸 *Booking Berhasil – Yolo Studio* 🌸\n\n" .
                "Halo *{$appointment->name}* ✨\n" .
                "Terima kasih sudah booking di *Yolo Studio*.\n\n" .
                "📅 *Tanggal:* {$tanggal}\n" .
                "⏰ *Jam:* {$start} – {$end} WIB\n" .
                "📸 *Layanan:* {$serviceName}\n" .
                "👥 *Jumlah Orang:* {$appointment->people_count}\n" .
                "💰 *Total:* Rp{$total}\n\n" .
                "🔳 *QR Code Check-in*\n{$qrUrl}\n\n" .
                "🔐 *Kode Booking:* {$trxCode}\n\n" .
                "📌 Tunjukkan QR atau kode ini saat check-in.\n" .
                "Jika QR tidak muncul, cukup sebutkan kodenya.\n\n" .
                "Sampai jumpa! 💕\n" .
                "*Tim Yolo Studio*";

            Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('WhatsApp error: ' . $e->getMessage());
        }
    }



    public function show(Appointment $appointment)
    {
        //
    }

    public function edit(Appointment $appointment)
    {
        //
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'people_count' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment
        ]);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending,Processing,Confirmed,Completed,Cancelled,Rescheduled',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new StatusUpdated($appointment));

        return redirect()->back()->with('success', 'Appointment status updated successfully.');
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'new_date' => 'required|date',
            'new_start_time' => 'required',
            'new_end_time' => 'required'
        ]);

        $appointment->update([
            'booking_date' => $request->new_date,
            'booking_start_time' => $request->new_start_time,
            'booking_end_time' => $request->new_end_time,
            'status' => 'Confirmed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil di-reschedule'
        ]);
    }





    public function destroy(Appointment $appointment)
    {
        //
    }
}
