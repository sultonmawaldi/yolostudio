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
use Illuminate\Support\Number;
use View;

class FrontendController extends Controller
{
    public function __construct()
    {
        $setting = Setting::firstOrFail();
        view()->share('setting', $setting);
    }

    public function booking()
    {
        $categories = Category::where('status', 1)->get();

        $employees = Employee::with('user')
            ->whereHas('user', fn($q) => $q->where('status', 1))
            ->get();

        return view('frontend.booking', compact('categories', 'employees'));
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

        try {
            // ---------------- Format waktu (helper)
            $formatTimeRange = function ($timeRange) {
                if (str_contains($timeRange, 'AM') || str_contains($timeRange, 'PM')) {
                    $timeRange = str_replace([' AM', ' PM', ' '], '', $timeRange);
                }
                $times = explode('-', $timeRange);
                $formattedTimes = array_map(function ($time) {
                    $parts = explode(':', $time);
                    $hours = str_pad(trim($parts[0]), 2, '0', STR_PAD_LEFT);
                    return $hours . ':' . $parts[1];
                }, $times);
                return implode('-', $formattedTimes);
            };

            // ---------------- Holiday exceptions
            $holidaysExceptions = $employee->holidays->mapWithKeys(function ($holiday) use ($formatTimeRange) {
                $hours = !empty($holiday->hours)
                    ? collect($holiday->hours)->map(fn($timeRange) => $formatTimeRange($timeRange))->toArray()
                    : [];
                return [$holiday->date => $hours];
            })->toArray();

            // ---------------- Opening hours
            $openingHours = OpeningHours::create(array_merge(
                $employee->days ?? [],
                ['exceptions' => $holidaysExceptions]
            ));

            $availableRanges = $openingHours->forDate($date);

            if ($availableRanges->isEmpty()) {
                return response()->json(['available_slots' => []]);
            }

            // ---------------- Ambil pivot berdasarkan service_id
            $serviceId = request()->get('service_id');
            $pivot = $serviceId
                ? $employee->services()->where('service_id', $serviceId)->first()?->pivot
                : null;

            // ---------------- Fallback durasi
            $slotDuration  = $pivot->duration ?? $employee->slot_duration ?? 15; // fallback default 15 menit
            $breakDuration = $pivot->break_duration ?? $employee->break_duration ?? 0;

            // ---------------- Generate slot pakai durasi pivot-aware
            $slots = $this->generateTimeSlots(
                $availableRanges,
                $slotDuration,
                $breakDuration,
                $date,
                $employee->id
            );

            return response()->json([
                'employee_id'     => $employee->id,
                'date'            => $date->toDateString(),
                'available_slots' => $slots,
                'slot_duration'   => $slotDuration,
                'break_duration'  => $breakDuration,
                'pivot_duration'  => $pivot->duration ?? null,
                'pivot_break'     => $pivot->break_duration ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing availability: ' . $e->getMessage()], 500);
        }
    }



    protected function generateTimeSlots($availableRanges, $slotDuration, $breakDuration, $date, $employeeId)
    {
        $slots = [];
        $now = now();
        $isToday = $date->isToday();

        // Ambil appointment dengan kolom start & end time baru
        $existingAppointments = Appointment::where('booking_date', $date->toDateString())
            ->where('employee_id', $employeeId)
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
}
