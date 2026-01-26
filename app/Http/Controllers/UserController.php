<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Service;
use App\Models\Holiday;
use App\Models\Employee;
use App\Models\Coupon;
use Hash;
use Session;

class UserController extends Controller
{


    public function index(Request $request)
    {
        // Get the role type from the request (either 'employee', 'customer', or 'moderator')
        $users = User::latest()->get();
        return view('backend.user.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $days = [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        ];

        $roles = Role::where('name', '!=', 'admin')->get();
        $services = Service::whereStatus(1)->get();

        return view('backend.user.create', compact('roles', 'services', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|exists:roles,name',
            'service' => 'nullable|array',
            'slot_duration' => 'nullable|numeric',
            'break_duration' => 'nullable|numeric',
            'days' => 'nullable|array',
            'is_employee' => 'nullable|boolean',
        ]);

        // ✅ Generate unique role_uid
        $roleUid = 'EMP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        // ✅ Create new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'email_verified_at' => now(),
            'password' => \Hash::make($data['password']),
            'role_uid' => $roleUid, // ✅ tambahkan di sini
        ]);

        // ✅ Assign role
        $user->assignRole($data['roles']);

        // ✅ Jika user adalah employee
        if (!empty($data['is_employee'])) {
            // Transformasi jam kerja (opening hours)
            $data['days'] = !empty($data['days'])
                ? $this->transformOpeningHours($data['days'])
                : null;

            $employee = Employee::create([
                'user_id'        => $user->id,
                'days'           => $data['days'],
                'slot_duration'  => $data['slot_duration'] ?: null,
                'break_duration' => $data['break_duration'] ?: null,
            ]);

            // ✅ Attach pivot data (service + durasi per layanan)
            $services = $request->input('service', []);
            $durations = $request->input('service_duration', []);
            $breaks = $request->input('service_break_duration', []);

            $pivotData = [];
            foreach ($services as $serviceId) {
                $pivotData[$serviceId] = [
                    'duration' => $durations[$serviceId] ?? $data['slot_duration'] ?? 0,
                    'break_duration' => $breaks[$serviceId] ?? $data['break_duration'] ?? 0,
                ];
            }

            if (!empty($pivotData)) {
                $employee->services()->attach($pivotData);
            }
        }

        return redirect()->route('user.index')->with('success', 'User has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $steps = ['5', '10', '15', '20', '30', '45', '60'];
        $breaks = ['5', '10', '15', '20', '25', '30'];

        $user = User::with('employee.holidays', 'employee.services')->findOrFail($id);
        $employeeDays = $user->employee->days ?? [];
        $employeeDays = $this->transformAvailabilitySlotsForEdit($employeeDays);

        $roles = Role::all();
        $services = Service::whereStatus(1)->get();

        return view('backend.user.edit', compact('user', 'roles', 'services', 'days', 'steps', 'breaks', 'employeeDays'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'social.*' => 'sometimes',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array|exists:roles,name',
            'service' => 'nullable|array',
            'slot_duration' => 'nullable|numeric',
            'break_duration' => 'nullable|numeric',
            'days' => 'nullable',
            'status' => 'nullable|numeric',
            'is_employee' => 'nullable',
            'holidays.date.*' => 'sometimes|required',
        ]);

        // ✅ Generate role_uid otomatis jika belum ada
        if (empty($user->role_uid)) {
            $roleName = $request->roles[0] ?? $user->roles->first()->name ?? 'USR';
            $prefix = strtoupper(substr($roleName, 0, 3));
            $unique = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
            $user->role_uid = "{$prefix}-" . now()->format('Ymd') . "-{$unique}";
        }

        // Validation for self-role/status changes
        if (\Auth::id() === $user->id) {
            if ($request->filled('roles') && !$user->hasAnyRole($request->roles)) {
                return redirect()->back()->withErrors(['roles' => 'You cannot change your own role.']);
            }
            if ($request->has('status') && $request->status != $user->status) {
                return redirect()->back()->withErrors(['status' => 'You cannot change your own status.']);
            }
        }

        if ($user->id === 1 && (!in_array('admin', $request->roles ?? []))) {
            return redirect()->back()->withErrors(['roles' => 'The first user must always have the admin role.']);
        }

        if ($user->hasRole('admin') && !in_array('admin', $request->roles ?? [])) {
            return redirect()->back()->withErrors(['roles' => 'The admin role cannot be removed.']);
        }

        $status = $user->id === 1 ? 1 : ($request->status ?? 0);

        // ✅ Update user data (termasuk role_uid)
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? $user->phone,
            'password' => $request->password ? \Hash::make($request->password) : $user->password,
            'status' => $status,
            'role_uid' => $user->role_uid, // pastikan tersimpan
        ]);

        // ✅ Sync roles
        if ($request->roles) {
            $roles = $request->roles;
            if ($user->id === 1 || $user->hasRole('admin')) {
                if (!in_array('admin', $roles)) {
                    $roles[] = 'admin';
                }
            }
            $user->syncRoles($roles);
        }

        // ✅ Employee update
        if (!empty($data['is_employee'])) {
            if (!empty($data['days'])) {
                $data['days'] = $this->transformOpeningHours($data['days']);
            }

            $employee = Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'days' => $data['days'] ?? null,
                    'slot_duration' => $data['slot_duration'] ?? null,
                    'break_duration' => $data['break_duration'] ?? null
                ]
            );

            // ✅ Update pivot table (service_id + duration + break_duration)
            $services = $request->input('service', []);
            $durations = $request->input('service_duration', []);
            $breaks = $request->input('service_break_duration', []);

            $pivotData = [];
            foreach ($services as $serviceId) {
                $pivotData[$serviceId] = [
                    'duration' => $durations[$serviceId] ?? $data['slot_duration'] ?? 0,
                    'break_duration' => $breaks[$serviceId] ?? $data['break_duration'] ?? 0,
                ];
            }

            $employee->services()->sync($pivotData);

            // ✅ Holidays (tidak diubah dari versi sebelumnya)
            if ($request->has('holidays.date') && is_array($request->input('holidays.date'))) {
                $existingHolidayIds = $user->employee->holidays->pluck('id')->toArray();
                $submittedHolidayIds = [];

                $dates = $request->input('holidays.date');
                $fromTimes = $request->input('holidays.from_time');
                $toTimes = $request->input('holidays.to_time');
                $recurring = $request->input('holidays.recurring');
                $holidayIds = $request->input('holidays.id', []);

                foreach ($dates as $index => $date) {
                    $holidayData = [
                        'employee_id' => $user->employee->id,
                        'hours' => isset($fromTimes[$index]) && isset($toTimes[$index])
                            ? [$fromTimes[$index] . '-' . $toTimes[$index]]
                            : [],
                        'recurring' => isset($recurring[$index]) && $recurring[$index] == 1,
                    ];

                    $holidayData['date'] = $holidayData['recurring']
                        ? \Carbon\Carbon::parse($date)->format('m-d')
                        : $date;

                    if (isset($holidayIds[$index])) {
                        $holiday = Holiday::find($holidayIds[$index]);
                        if ($holiday) {
                            $holiday->update($holidayData);
                            $submittedHolidayIds[] = $holiday->id;
                        }
                    } else {
                        $holiday = Holiday::create($holidayData);
                        $submittedHolidayIds[] = $holiday->id;
                    }
                }

                $holidaysToDelete = array_diff($existingHolidayIds, $submittedHolidayIds);
                if (!empty($holidaysToDelete)) {
                    Holiday::whereIn('id', $holidaysToDelete)->delete();
                }
            } else {
                if ($user->employee->holidays()->exists()) {
                    $user->employee->holidays()->delete();
                }
            }
        }

        return redirect()->route('user.index')->with('success', 'Profile has been updated successfully!');
    }




    // Custom method to log out a specific user
    protected function logoutUser(User $user)
    {
        // Check if the application is using the database session driver
        if (config('session.driver') === 'database') {
            // Delete all sessions for this user by matching the user ID
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request)
    {
        if ($user->id == 1) {
            return back()->withErrors('First admin user cannot be deleted.');
        }

        if ($user->id === $request->user()->id) {
            return back()->withErrors('You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User has been successfully trashed!');
    }


    public function trashView(Request $request)
    {
        $users = User::onlyTrashed()->latest()->get();
        return view('backend.user.trash', compact('users'));
    }

    // restore data
    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        if (!is_null($user)) {
            $user->restore();
        }
        return redirect()->back()->with("success", "User Restored Succesfully");
    }


    public function force_delete($id)
    {
        // Retrieve the trashed user with its associated employee, holidays, appointments, and bookings
        $user = User::withTrashed()->findOrFail($id);

        //for employee
        if ($user->employee->appointments->count()) {
            return back()->withErrors('User cannot be deleted permanently, already engaged in existing bookings!');
        }

        //for user
        if ($user->appointments->count()) {
            return back()->withErrors('User cannot be deleted permanently, already engaged in existing bookings!');
        }

        // Check if the user has an associated employee
        if ($user->employee) {
            // Delete all holidays related to the employee
            foreach ($user->employee->holidays as $holiday) {
                $holiday->forceDelete(); // Force delete each holiday
            }


            // Delete all appointments related to the employee
            // foreach ($user->employee->appointments as $appointment) {
            //     $appointment->forceDelete(); // Force delete each appointment
            // }

            // Detach all services related to the employee (many-to-many relationship)
            if ($user->employee->services()->exists()) {
                $user->employee->services()->detach(); // Detach the services from the employee
            }

            // Finally, delete the employee data
            $user->employee->forceDelete();
        }

        // Delete the user's profile image if exists
        if ($user->image) {
            $destination = public_path('uploads/images/profile/' . $user->image);
            if (\File::exists($destination)) {
                \File::delete($destination);
            }
        }

        // Permanently delete the user from the database
        $user->forceDelete();

        return back()->withSuccess('User and all related data (employee, holidays, appointments, bookings) have been deleted permanently!');
    }



    public function password_update(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match!']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password has been successfully Updated!');
    }

    public function updateProfileImage(Request $request, User $user)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:2048',
            'delete_image' => 'nullable'
        ]);

        //remove old image
        $destination = public_path('uploads/images/profile/' . $user->image);
        if (\File::exists($destination)) {
            \File::delete($destination);
        }

        $imageName = time() . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('uploads/images/profile/'), $imageName);
        $user->update([
            'image' => $imageName
        ]);

        return back()->withSuccess('Profile image has been updated successfully!');
    }


    // Hapus profile image (member)
    public function deleteProfileImage()
    {
        $user = auth()->user(); // ambil user yang login

        if ($user->image) {
            $destination = public_path('uploads/images/profile/' . $user->image);
            if (\File::exists($destination)) {
                \File::delete($destination);
            }

            $user->update(['image' => null]);
        }

        return back()->withSuccess('Profile image deleted!');
    }




    // Transform the data
    function transformOpeningHours($data)
    {
        $result = [];

        foreach ($data as $day => $times) {
            $dayHours = [];
            for ($i = 0; $i < count($times); $i += 2) {
                if (isset($times[$i + 1])) {
                    $dayHours[] = $times[$i] . '-' . $times[$i + 1];
                }
            }
            $result[$day] = $dayHours;
        }

        return $result;
    }



    protected function transformAvailabilitySlotsForEdit(array $employeeDays)
    {
        foreach ($employeeDays as $day => $slots) {
            $transformedSlots = [];
            foreach ($slots as $slot) {
                list($startTime, $endTime) = explode('-', $slot);
                $transformedSlots[] = $startTime;
                $transformedSlots[] = $endTime;
            }
            $employeeDays[$day] = $transformedSlots;
        }

        return $employeeDays;
    }

    public function dashboard()
    {
        $userId = auth()->id();

        $transactions = DB::table('transactions')
            ->join('appointments', 'transactions.appointment_id', '=', 'appointments.id')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->select(
                'transactions.id as transaction_id',
                'transactions.transaction_code',
                'transactions.payment_method',
                'transactions.amount',
                'transactions.total_amount',
                'transactions.payment_status',
                'appointments.booking_date',
                'appointments.booking_time',
                'appointments.status as appointment_status',
                'users.name',
                'users.email',
                'users.phone'
            )
            ->where('transactions.user_id', $userId)
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        // Kupon diambil dari observer otomatis
        $coupons = Coupon::where('user_id', $userId)->where('active', 1)->get();
        $usedCoupons = Coupon::where('user_id', $userId)->where('status', 'used')->count();

        return view('frontend.member.dashboard', compact('transactions', 'coupons', 'usedCoupons'));
    }

    // =======================
    // MEMBER PROFILE
    // =======================

    public function memberProfile()
    {
        return view('frontend.member.profile.index');
    }

    public function memberProfileEdit()
    {
        return view('frontend.member.profile.edit');
    }

    public function memberProfileUpdate(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // upload image (PAKAI STYLE YANG SUDAH ADA DI CONTROLLER KAMU)
        if ($request->hasFile('image')) {
            // hapus lama
            if ($user->image) {
                $old = public_path('uploads/images/profile/' . $user->image);
                if (\File::exists($old)) {
                    \File::delete($old);
                }
            }

            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/profile/'), $imageName);
            $data['image'] = $imageName;
        }

        $user->update([
            'name'  => $data['name'],
            'phone' => $data['phone'] ?? $user->phone,
            'image' => $data['image'] ?? $user->image,
        ]);

        return redirect()
            ->route('member.profile')
            ->with('success', 'Profile updated successfully');
    }
}
