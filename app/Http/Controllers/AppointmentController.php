<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Setting;
use App\Models\Employee;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Events\BookingCreated;
use App\Events\StatusUpdated;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Addon;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ambil appointments
        $query = Appointment::orderBy('booking_date', 'asc')
            ->orderBy('booking_start_time', 'asc')
            ->with(['addons', 'employee.user', 'service']);

        // 🔐 Jika employee → hanya lihat miliknya
        if ($user->hasRole('employee')) {

            if (!$user->employee) {
                abort(403, 'Employee profile not found.');
            }

            $query->where('employee_id', $user->employee->id);
        }

        $appointments = $query->get();

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

        // ===== Tambahkan employee untuk filter Karyawan =====
        if ($user->hasRole('admin') || $user->hasRole('moderator')) {
            $employees = \App\Models\Employee::whereHas('user', function ($q) {
                $q->role('employee'); // hanya user role employee
            })
                ->orderBy('id', 'asc') // urut dari id kecil ke besar
                ->get();
        } elseif ($user->hasRole('employee')) {
            $employees = collect([$user->employee]);
        } else {
            $employees = collect();
        }

        // ===== Tambahkan services untuk filter Layanan =====
        // Ambil semua service, urut dari id
        $services = \App\Models\Service::orderBy('id', 'asc')->get();

        return view('backend.appointment.index', compact('appointments', 'employees', 'services'));
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
            'status' => 'required|in:Pending,Processing,Confirmed,Completed,Cancelled,Rescheduled,On Hold,No Show',
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
            'dp_method' => 'nullable|string',
            'pelunasan_method' => 'nullable|string',
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
        $validated['booking_id'] = 'YS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        $transactionCode = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));

        $employee = \App\Models\Employee::with('studio')
            ->findOrFail($validated['employee_id']);

        if (!$employee->studio) {
            return response()->json([
                'success' => false,
                'message' => 'Employee belum terhubung dengan studio'
            ], 422);
        }

        $studio = $employee->studio;



        // Simpan appointment
        $appointment = Appointment::create([
            'user_id' => $validated['user_id'],
            'employee_id' => $validated['employee_id'],
            'service_id' => $validated['service_id'],
            'studio_id'   => $studio->id,
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

        // ✅ Tambahkan kode Google Calendar DI SINI
        // ✅ Tambahkan event ke Google Calendar sesuai STUDIO
        try {
            // Ambil employee beserta studio
            $employee = \App\Models\Employee::with('studio')->find($appointment->employee_id);

            if (!$employee || !$employee->studio) {
                throw new \Exception('Employee atau Studio tidak ditemukan');
            }

            $studio = $employee->studio;

            // Pastikan studio punya calendar_id
            if (!$studio->calendar_id) {
                \Log::warning('Studio tidak punya calendar_id', [
                    'studio_id' => $studio->id,
                    'studio_name' => $studio->name,
                ]);
            } else {
                $calendarService = new GoogleCalendarService();

                $calendarEvent = $calendarService->createEvent(
                    $appointment,
                    $studio->calendar_id // 🎯 calendar cabang yang BENAR
                );

                // Simpan event ID ke appointment
                $appointment->update([
                    'google_calendar_event_id' => $calendarEvent->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Google Calendar error', [
                'appointment_id' => $appointment->id ?? null,
                'message' => $e->getMessage(),
            ]);
        }



        // 1️⃣ Buat transaction awal (Pending dulu boleh)
        $transaction = Transaction::create([
            'user_id' => $appointment->user_id,
            'appointment_id' => $appointment->id,
            'transaction_code' => $transactionCode,
            'amount' => $validated['amount'],
            'total_amount' => $validated['total_amount'] ?? $validated['amount'],
            'payment_status' => $validated['payment_status'] ?? 'Pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'midtrans_order_id' => $validated['midtrans_order_id'] ?? null,
            'coupon_id' => $validated['coupon_id'] ?? null,
            'dp_method' => $validated['dp_method'] ?? null,
            'pelunasan_method' => $validated['pelunasan_method'] ?? null,
        ]);

        // 2️⃣ Attach service ke transaction
        $service = $appointment->service;
        if ($service) {
            $transaction->services()->attach($service->id, [
                'price' => $service->price,
                'qty' => 1,
                'subtotal' => $service->price,
            ]);
        }

        // 3️⃣ Reload relasi services supaya observer bisa baca reward_points
        $transaction->load('services');

        // 4️⃣ Jika payment_status = Paid, trigger observer manual supaya reward points langsung jalan
        if (($validated['payment_status'] ?? null) === 'Paid') {
            $transaction->touch(); // triggers updated event
        }




        try {
            /*
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
    */
        } catch (\Exception $e) {
            \Log::error('QR Code generation error: ' . $e->getMessage());
        }




        // Kirim WhatsApp
        $this->sendWhatsappNotification($appointment);

        // Event
        event(new BookingCreated($appointment));

        return response()->json([
            'success' => true,
            'message' => 'Pemesanan janji temu berhasil',
            'booking_id' => $appointment->booking_id,
            'qr_url' => $transaction->qr_url ?? null,
            'transaction_code' => $transactionCode,
            'appointment' => $appointment
        ]);
    }


    private function sendWhatsappNotification(Appointment $appointment)
    {
        try {
            $studioName = optional($appointment->studio)->name ?? 'Yolo Studio';

            // Mapping studio → token (bisa ditambah di .env)
            $studioTokens = [
                'Cilegon'    => env('FONNTE_TOKEN'),  // token khusus Cilegon
                'Serang'     => env('FONNTE_TOKEN'),  // token khusus Serang
                'Pandeglang' => env('FONNTE_TOKEN'),  // token khusus Pandeglang
            ];

            // Pilih token sesuai studio, fallback ke default
            $token = $studioTokens[$studioName] ?? env('FONNTE_TOKEN');

            if (!$token) {
                \Log::warning("Token WA tidak ditemukan untuk studio: $studioName");
                return;
            }

            // Normalisasi nomor HP (08xxx → 628xxx)
            $phone = preg_replace('/^0/', '62', $appointment->phone);

            $serviceName = optional($appointment->service)->title ?? 'Layanan';
            $total = number_format($appointment->amount, 0, ',', '.');
            $tanggal = \Carbon\Carbon::parse($appointment->booking_date)
                ->translatedFormat('l, d M Y');
            $start = date('H:i', strtotime($appointment->booking_start_time));
            $end   = date('H:i', strtotime($appointment->booking_end_time));

            $message =
                "🌸 *Booking Berhasil – {$studioName}* 🌸\n\n" .
                "Halo *{$appointment->name}* ✨\n" .
                "Terima kasih telah melakukan booking di *{$studioName}*.\n\n" .
                "📅 *Tanggal :* {$tanggal}\n" .
                "⏰ *Jam :* {$start} – {$end} WIB\n" .
                "📸 *Layanan :* {$serviceName}\n";

            // ======================
            // Tambahkan layanan tambahan jika ada
            // ======================
            $addons = $appointment->addons; // relasi many-to-many
            if ($addons->isNotEmpty()) {
                $message .= "➕ *Layanan Tambahan :*\n";
                foreach ($addons as $addon) {
                    $qty = $addon->pivot->qty ?? 1;
                    $price = $addon->pivot->price ?? $addon->price;

                    $message .= "- {$addon->name} x {$qty}\n";
                }
            }

            $message .= "👥 *Jumlah Orang :* {$appointment->people_count}\n" .
                "💰 *Total Pembayaran :* Rp {$total}\n\n" .
                "📌 *Catatan :*\n" .
                "- Silakan datang 10 menit sebelum sesi dimulai untuk melakukan persiapan.\n" .
                "- Jika ada perubahan jadwal, segera hubungi admin studio.\n\n" .
                "Sampai jumpa! 💕\n" .
                "*Tim {$studioName}*";

            // Kirim WA
            Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
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
            'message' => 'Janji temu berhasil diperbarui',
            'appointment' => $appointment
        ]);
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending,Processing,Confirmed,Completed,Cancelled,Rescheduled,On Hold,No Show',
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new StatusUpdated($appointment));

        return redirect()->back()->with('success', 'Status janji temu berhasil diperbarui');
    }


    public function reschedule(Request $request, Appointment $appointment)
    {
        // ❌ Sudah reschedule
        if ($appointment->reschedule_count >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ulang hanya dapat dilakukan 1 kali'
            ], 403);
        }

        $bookingDate = Carbon::parse($appointment->booking_date)->startOfDay();

        // ❌ Booking hari ini / sudah lewat
        if (now()->startOfDay()->gte($bookingDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ulang hanya dapat dilakukan maksimal H-1 sebelum jadwal booking'
            ], 403);
        }

        $request->validate([
            'new_date' => 'required|date',
            'new_start_time' => 'required',
            'new_end_time' => 'required'
        ]);

        $appointment->update([
            'booking_date' => $request->new_date,
            'booking_start_time' => $request->new_start_time,
            'booking_end_time' => $request->new_end_time,
            'status' => 'Rescheduled',
            'reschedule_count' => $appointment->reschedule_count + 1
        ]);

        /*
    |--------------------------------------------------------------------------
    | TAMBAHAN: UPDATE GOOGLE CALENDAR SAAT RESCHEDULE
    |--------------------------------------------------------------------------
    */
        try {

            // Pastikan ada event Google Calendar
            if ($appointment->google_calendar_event_id) {

                $employee = \App\Models\Employee::with('studio')
                    ->find($appointment->employee_id);

                if ($employee && $employee->studio && $employee->studio->calendar_id) {

                    $calendarService = new \App\Services\GoogleCalendarService();

                    $calendarService->updateEvent(
                        $appointment,
                        $employee->studio->calendar_id,
                        $appointment->google_calendar_event_id
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Google Calendar jadwal ulang error', [
                'appointment_id' => $appointment->id,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Janji temu berhasil di jadwal ulang'
        ]);
    }

    public function getAvailabilityForReschedule(Request $request, Appointment $appointment)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : now();
        $employee = $appointment->employee;
        $service = $appointment->service;
        $slotGroupId = $appointment->slot_group_id;

        if (!$employee || !$service || !$slotGroupId) {
            return response()->json([
                'success' => false,
                'message' => 'Data janji temu tidak lengkap'
            ], 422);
        }

        $slotGroup = \App\Models\SlotGroup::find($slotGroupId);
        $slotDuration  = $slotGroup->slot_duration ?? 15;
        $breakDuration = $slotGroup->break_duration ?? 0;

        $workingHours = $slotGroup->working_hours ?? [];
        $dayOfWeek = strtolower($date->format('l'));
        $intervals = $workingHours[$dayOfWeek] ?? [];

        if (empty($intervals)) {
            return response()->json([
                'success' => true,
                'employee_id' => $employee->id,
                'available_slots' => [],
                'message' => 'Studio tutup pada hari ini'
            ]);
        }

        $slots = [];
        $now = now();
        $isToday = $date->isToday();

        // Ambil semua appointment lain (kecuali appointment ini)
        $existingAppointments = Appointment::where('employee_id', $employee->id)
            ->where('slot_group_id', $slotGroupId)
            ->where('booking_date', $date->toDateString())
            ->whereNotIn('status', ['Cancelled', 'No Show'])
            ->where('id', '!=', $appointment->id)
            ->get(['booking_start_time', 'booking_end_time']);

        $oldStart = Carbon::parse($appointment->booking_date . ' ' . $appointment->booking_start_time);
        $oldEnd   = Carbon::parse($appointment->booking_date . ' ' . $appointment->booking_end_time);

        foreach ($intervals as $interval) {
            $current = Carbon::parse($date->toDateString() . ' ' . $interval['start']);
            $endTime = Carbon::parse($date->toDateString() . ' ' . $interval['end']);

            while ($current->copy()->addMinutes($slotDuration)->lte($endTime)) {
                $slotEnd = $current->copy()->addMinutes($slotDuration);

                // Skip slot yang sudah lewat
                if ($isToday && $current->lte($now)) {
                    $current->addMinutes($slotDuration + $breakDuration);
                    continue;
                }

                // Cek bentrok dengan appointment lain
                $isBooked = $existingAppointments->contains(function ($a) use ($current, $slotEnd, $date) {
                    $start = Carbon::parse($date->toDateString() . ' ' . $a->booking_start_time);
                    $end = Carbon::parse($date->toDateString() . ' ' . $a->booking_end_time);
                    return $current->lt($end) && $slotEnd->gt($start);
                });

                // Cek slot lama appointment ini
                $isOld = $current->lt($oldEnd) && $slotEnd->gt($oldStart);

                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'display' => $current->format('H:i') . ' - ' . $slotEnd->format('H:i') . ' WIB',
                    'is_booked' => $isBooked || $isOld, // disable jika bentrok atau slot lama
                    'is_old' => $isOld,                 // tanda slot lama
                ];


                $current->addMinutes($slotDuration + $breakDuration);
            }
        }

        return response()->json([
            'success' => true,
            'appointment_id' => $appointment->id,
            'employee_id' => $employee->id,
            'available_slots' => $slots,
            'slot_group_id' => $slotGroupId,
        ]);
    }










    public function destroy(Appointment $appointment)
    {
        //
    }
}
