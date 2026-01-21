<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Appointment;
use Spatie\OpeningHours\OpeningHours;
use Carbon\Carbon;
use App\Models\Addon;
use Illuminate\Support\Number;
use View;

class FrontendController extends Controller
{
    public function __construct()
    {
        $setting = Setting::firstOrFail();
        view()->share('setting', $setting);
    }

    // frontendController.php

    public function booking(Request $request)
    {
        $serviceId = $request->query('service_id');

        // Load service + addons aktif khusus service ini
        $service = Service::with(['addons' => function ($q) {
            $q->where('is_active', true);
        }])->findOrFail($serviceId ?? 1);

        // Kategori aktif
        $categories = Category::where('status', 1)->get();

        return view('frontend.booking', [
            'service'    => $service,
            'addons'     => $service->addons, // otomatis hanya addon service ini
            'categories' => $categories,
        ]);
    }

    public function getServiceAddons(Service $service)
    {
        $addons = $service->addons()->get(); // cukup ini, filter aktif sudah di relasi
        return response()->json([
            'success' => true,
            'addons' => $addons
        ]);
    }





    public function getServicesByEmployeeAndCategory(
        Employee $employee,
        Category $category
    ) {
        $services = $employee->services()
            ->where('category_id', $category->id)
            ->where('status', 1)
            ->with('category')
            ->get();

        return response()->json([
            'success'  => true,
            'services' => $services
        ]);
    }



    public function getEmployees(Request $request, Service $service)
    {
        // Ambil employee dari relasi service -> employee (pivot sesuai konteks service)
        $employees = $service->employees()
            ->whereHas('user', fn($q) => $q->where('status', 1))
            ->with('user')
            ->get()
            ->transform(function ($employee) {

                // Pivot ini DIJAMIN hanya untuk service ini
                $pivot = $employee->pivot;

                $employee->pivot_data = [
                    'duration' => $pivot->duration ?? $employee->slot_duration,
                    'break_duration' => $pivot->break_duration ?? $employee->break_duration,
                ];

                return $employee;
            });

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No employees available for this service',
            ]);
        }

        return response()->json([
            'success' => true,
            'employees' => $employees,
            'service' => $service,
        ]);
    }


    public function getCategoriesByEmployee(Employee $employee)
    {
        $categories = Category::whereHas('services', function ($q) use ($employee) {
            $q->whereHas('employees', function ($e) use ($employee) {
                $e->where('employees.id', $employee->id);
            });
        })
            ->where('status', 1)
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }



    public function getEmployeeAvailability(Employee $employee, $date = null)
    {
        $date = $date ? Carbon::parse($date) : now();
        $serviceId = request()->get('service_id');

        if (!$serviceId) {
            return response()->json(['error' => 'Service ID is required'], 422);
        }

        try {
            // Ambil pivot untuk service agar tahu slot_group_id
            $pivot = $employee->services()->where('service_id', $serviceId)->first()?->pivot;
            $slotGroupId = $pivot->slot_group_id ?? null;

            if (!$slotGroupId) {
                return response()->json([
                    'employee_id' => $employee->id,
                    'available_slots' => [],
                    'message' => 'No slot group assigned for this service'
                ]);
            }

            // Ambil slot group
            $slotGroup = \App\Models\SlotGroup::find($slotGroupId);
            if (!$slotGroup) {
                return response()->json([
                    'employee_id' => $employee->id,
                    'available_slots' => [],
                    'message' => 'Slot group not found'
                ]);
            }

            $slotDuration  = $slotGroup->slot_duration ?? 15;
            $breakDuration = $slotGroup->break_duration ?? 0;

            // ---------------- Ambil working hours dari JSON
            $workingHours = $slotGroup->working_hours ?? [];
            $dayOfWeek = strtolower($date->format('l')); // monday, tuesday, etc
            $intervals = $workingHours[$dayOfWeek] ?? [];

            if (empty($intervals)) {
                // Hari libur
                return response()->json([
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'available_slots' => [],
                    'slot_duration' => $slotDuration,
                    'break_duration' => $breakDuration,
                    'slot_group_id' => $slotGroupId,
                ]);
            }

            $slots = [];
            $now = now();
            $isToday = $date->isToday();

            foreach ($intervals as $interval) {
                $current = Carbon::parse($date->toDateString() . ' ' . $interval['start']);
                $endTime = Carbon::parse($date->toDateString() . ' ' . $interval['end']);

                while ($current->copy()->addMinutes($slotDuration)->lte($endTime)) {
                    $slotEnd = $current->copy()->addMinutes($slotDuration);

                    // Skip slot yang sudah lewat
                    if ($isToday && $slotEnd->lte($now)) {
                        $current->addMinutes($slotDuration + $breakDuration);
                        continue;
                    }

                    // Cek bentrok dengan appointment di slot group yang sama
                    $isBooked = Appointment::where('employee_id', $employee->id)
                        ->where('slot_group_id', $slotGroupId)
                        ->where('booking_date', $date->toDateString())
                        ->whereNotIn('status', ['Cancelled'])
                        ->where(function ($q) use ($current, $slotEnd) {
                            $q->where('booking_start_time', '<', $slotEnd)
                                ->where('booking_end_time', '>', $current);
                        })
                        ->exists();

                    $slots[] = [
                        'start' => $current->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                        'display' => $current->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                        'slot_duration' => $slotDuration,
                        'break_duration' => $breakDuration,
                        'is_booked' => $isBooked,
                    ];

                    $current->addMinutes($slotDuration + $breakDuration);
                }
            }

            return response()->json([
                'employee_id' => $employee->id,
                'date' => $date->toDateString(),
                'available_slots' => $slots,
                'slot_duration' => $slotDuration,
                'break_duration' => $breakDuration,
                'slot_group_id' => $slotGroupId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing availability: ' . $e->getMessage()], 500);
        }
    }



    protected function generateTimeSlots($availableRanges, $slotDuration, $breakDuration, $date, $employeeId, $slotGroupId)
    {
        $slots = [];
        $now = now();
        $isToday = $date->isToday();

        // Ambil appointment dengan kolom start & end time baru
        $existingAppointments = Appointment::where('booking_date', $date->toDateString())
            ->where('employee_id', $employeeId)
            ->where('slot_group_id', $slotGroupId) // tambahkan filter slot group
            ->whereNotIn('status', ['Cancelled'])
            ->get(['booking_start_time', 'booking_end_time']);


        $bookedSlots = $existingAppointments->map(function ($appointment) {
            return [
                'start' => $appointment->booking_start_time,
                'end'   => $appointment->booking_end_time,
            ];
        })->toArray();

        foreach ($availableRanges as $range) {
            $start = Carbon::parse($date->toDateString() . ' ' . $range->start()->format('H:i'));
            $end = Carbon::parse($date->toDateString() . ' ' . $range->end()->format('H:i'));

            if ($isToday && $end->lte($now)) continue;

            $currentSlotStart = clone $start;

            if ($isToday && $currentSlotStart->lt($now)) {
                $currentSlotStart = clone $now;
                $minutes = $currentSlotStart->minute;
                $remainder = $minutes % $slotDuration;
                if ($remainder > 0) {
                    $currentSlotStart->addMinutes($slotDuration - $remainder)->second(0);
                }
            }

            while ($currentSlotStart->copy()->addMinutes($slotDuration)->lte($end)) {
                $slotEnd = $currentSlotStart->copy()->addMinutes($slotDuration);

                $isAvailable = true;
                foreach ($bookedSlots as $bookedSlot) {
                    $bookedStart = Carbon::parse($date->toDateString() . ' ' . $bookedSlot['start']);
                    $bookedEnd = Carbon::parse($date->toDateString() . ' ' . $bookedSlot['end']);

                    if ($currentSlotStart->lt($bookedEnd) && $slotEnd->gt($bookedStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable && (!$isToday || $slotEnd->gt($now))) {
                    $slots[] = [
                        'start' => $currentSlotStart->format('H:i'),
                        'end'   => $slotEnd->format('H:i'),
                        'display' => $currentSlotStart->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    ];
                }

                $currentSlotStart->addMinutes($slotDuration + $breakDuration);

                if ($currentSlotStart->copy()->addMinutes($slotDuration)->gt($end)) {
                    break;
                }
            }
        }

        return $slots;
    }

    public function getAvailabilityForReschedule(Appointment $appointment, Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : now();
        $employee = $appointment->employee;
        $service = $appointment->service;
        $slotGroupId = $appointment->slot_group_id;

        if (!$employee || !$service || !$slotGroupId) {
            return response()->json([
                'success' => false,
                'message' => 'Data appointment tidak lengkap'
            ], 422);
        }

        $slotGroup = \App\Models\SlotGroup::find($slotGroupId);
        $slotDuration  = $slotGroup->slot_duration ?? 15;
        $breakDuration = $slotGroup->break_duration ?? 0;

        // Ambil working hours
        $workingHours = $slotGroup->working_hours ?? [];
        $dayOfWeek = strtolower($date->format('l'));
        $intervals = $workingHours[$dayOfWeek] ?? [];

        if (empty($intervals)) {
            return response()->json([
                'employee_id' => $employee->id,
                'available_slots' => [],
                'message' => 'Studio tutup pada hari ini'
            ]);
        }

        $slots = [];
        $now = now();
        $isToday = $date->isToday();

        // Ambil semua appointment di slot group ini, kecuali appointment ini sendiri
        $existingAppointments = Appointment::where('employee_id', $employee->id)
            ->where('slot_group_id', $slotGroupId)
            ->where('booking_date', $date->toDateString())
            ->whereNotIn('status', ['Cancelled'])
            ->where('id', '!=', $appointment->id)
            ->get(['booking_start_time', 'booking_end_time']);

        foreach ($intervals as $interval) {
            $current = Carbon::parse($date->toDateString() . ' ' . $interval['start']);
            $endTime = Carbon::parse($date->toDateString() . ' ' . $interval['end']);

            while ($current->copy()->addMinutes($slotDuration)->lte($endTime)) {
                $slotEnd = $current->copy()->addMinutes($slotDuration);

                if ($isToday && $slotEnd->lte($now)) {
                    $current->addMinutes($slotDuration + $breakDuration);
                    continue;
                }

                $isBooked = $existingAppointments->contains(function ($a) use ($current, $slotEnd, $date) {
                    $start = Carbon::parse($date->toDateString() . ' ' . $a->booking_start_time);
                    $end = Carbon::parse($date->toDateString() . ' ' . $a->booking_end_time);
                    return $current->lt($end) && $slotEnd->gt($start);
                });

                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'display' => $current->format('g:i A') . ' - ' . $slotEnd->format('g:i A'),
                    'is_booked' => $isBooked,
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
}
