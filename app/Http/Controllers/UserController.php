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
use App\Models\SlotGroup;
use App\Models\Studio;
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
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $roles = Role::where('name', '!=', 'admin')->get();
        $services = Service::whereStatus(1)->get();
        $slotGroups = SlotGroup::all();

        // ✅ Tambahkan ini
        $studios = Studio::whereStatus(1)->get(); // ambil semua studio yang aktif

        return view('backend.user.create', compact(
            'roles',
            'services',
            'days',
            'slotGroups',
            'studios'
        ));
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
            'status' => 'nullable|in:0,1',
            'roles' => 'required|string|exists:roles,name',
            'service' => 'nullable|array',
            'days' => 'nullable|array',
            'slot_group_id' => 'nullable|array',
            'slot_group_id.*' => 'nullable|exists:slot_groups,id',
            'studio_id' => 'nullable|exists:studios,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // 🔥 TAMBAHAN (biar aman)
            'holidays.date.*' => 'nullable|date',
            'holidays.from_time.*' => 'nullable',
            'holidays.to_time.*' => 'nullable',
        ]);

        // ✅ aman dari array / null
        $role = $data['roles'] ?? 'USR';
        $role = is_array($role) ? $role[0] : $role;

        $roleName = strtoupper(substr($role, 0, 3));
        $roleUid = $roleName . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/images/profile/'), $imageName);
        }

        // ✅ Create new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $this->normalizePhone($data['phone'] ?? ''),
            'email_verified_at' => now(),
            'password' => \Hash::make($data['password']),
            'status' => $request->status ?? 1,
            'role_uid' => $roleUid,
            'image' => $imageName,
        ]);

        // ✅ Assign role
        $user->assignRole($data['roles']);

        // ✅ Jika role adalah employee, buat data employee otomatis
        if ($role === 'employee') {

            $data['days'] = !empty($data['days'])
                ? $this->transformOpeningHours($data['days'])
                : null;

            $employee = Employee::create([
                'user_id'   => $user->id,
                'studio_id' => $request->studio_id ?? null,
                'days'      => $data['days'],
            ]);

            // ✅ Attach pivot data (service + durasi + break + slot_group)
            $services = $request->input('service', []);
            $durations = $request->input('service_duration', []);
            $breaks = $request->input('service_break_duration', []);
            $slotGroups = $request->input('slot_group_id', []);

            $pivotData = [];
            foreach ($services as $serviceId) {
                $pivotData[$serviceId] = [
                    'duration'       => $durations[$serviceId] ?? 0,
                    'break_duration' => $breaks[$serviceId] ?? 0,
                    'slot_group_id'  => $slotGroups[$serviceId] ?? null,
                ];
            }

            if (!empty($pivotData)) {
                $employee->services()->attach($pivotData);
            }

            // =====================================================
            // 🔥 TAMBAHAN: SIMPAN HOLIDAYS (INI YANG KAMU BUTUH)
            // =====================================================
            if ($request->has('holidays.date') && is_array($request->input('holidays.date'))) {

                $dates = $request->input('holidays.date');
                $fromTimes = $request->input('holidays.from_time');
                $toTimes = $request->input('holidays.to_time');

                foreach ($dates as $index => $date) {

                    // skip kalau kosong
                    if (empty($date)) continue;

                    $hours = [];

                    // kalau isi jam → partial off
                    if (!empty($fromTimes[$index]) && !empty($toTimes[$index])) {
                        $hours[] = $fromTimes[$index] . '-' . $toTimes[$index];
                    }

                    Holiday::create([
                        'employee_id' => $employee->id,
                        'date'        => $date,
                        'hours'       => $hours, // array → json
                        'recurring'   => false,
                        'description' => null,
                    ]);
                }
            }
        }

        return redirect()->route('user.index')->with('success', 'Pengguna telah berhasil ditambahkan');
    }

    private function normalizePhone($phone)
    {
        // Hapus semua karakter selain angka
        $phone = preg_replace('/\D/', '', $phone);

        // Jika diawali 0 → ubah ke format Indonesia
        if (Str::startsWith($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // Jika diawali 62 → hapus 62
        if (Str::startsWith($phone, '62')) {
            $phone = substr($phone, 2);
        }

        return '+62' . $phone;
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

        // Role utama user
        $userRole = $user->roles->first()->name ?? null;

        // Employee days untuk edit
        $employeeDays = $user->employee->days ?? [];
        $employeeDays = $this->transformAvailabilitySlotsForEdit($employeeDays);

        // Semua roles, services, slotGroups, dan studios
        $roles = Role::all();
        $services = Service::whereStatus(1)->get();
        $slotGroups = SlotGroup::all();
        $studios = Studio::whereStatus(1)->get();

        // Services user untuk multi-select
        $userServices = $user->employee
            ? $user->employee->services->pluck('id')->toArray()
            : [];

        // Holidays user – aman dari error jika kosong
        $userHolidays = $user->employee
            ? $user->employee->holidays->map(function ($h) {
                $hours = $h->hours ?? [];

                $fromTime = null;
                $toTime = null;

                if (!empty($hours)) {
                    $parts = explode('-', $hours[0]);
                    $fromTime = $parts[0] ?? null;
                    $toTime   = $parts[1] ?? null;
                }

                return (object)[
                    'id' => $h->id,
                    'date' => $h->date,
                    'from_time' => $fromTime,
                    'to_time' => $toTime,
                ];
            })
            : collect();

        return view('backend.user.edit', compact(
            'user',
            'userRole',
            'roles',
            'services',
            'days',
            'steps',
            'breaks',
            'employeeDays',
            'slotGroups',
            'studios',
            'userServices',
            'userHolidays'
        ));
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
            'days' => 'nullable',
            'status' => 'nullable|numeric',
            'is_employee' => 'nullable',
            'holidays.date.*' => 'sometimes|required',
            'slot_group_id' => 'nullable|array',
            'slot_group_id.*' => 'nullable|exists:slot_groups,id',
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
                return redirect()->back()->withErrors(['roles' => 'Anda tidak dapat mengubah peran Anda sendiri.']);
            }
            if ($request->has('status') && $request->status != $user->status) {
                return redirect()->back()->withErrors(['status' => 'Anda tidak dapat mengubah status Anda sendiri.']);
            }
        }

        if ($user->id === 1 && (!in_array('admin', $request->roles ?? []))) {
            return redirect()->back()->withErrors(['roles' => 'Pengguna pertama harus selalu memiliki peran admin.']);
        }

        if ($user->hasRole('admin') && !in_array('admin', $request->roles ?? [])) {
            return redirect()->back()->withErrors(['roles' => 'Peran admin tidak dapat dihapus.']);
        }

        $status = $user->id === 1 ? 1 : ($request->status ?? 0);

        $phone = $request->phone ?? $user->phone;
        if (!empty($phone)) {
            $phone = $this->normalizePhone($phone);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $phone,
            'password' => $request->password ? \Hash::make($request->password) : $user->password,
            'status' => $status,
            'role_uid' => $user->role_uid,
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
            $slotGroups = $request->input('slot_group_id', []);

            $pivotData = [];
            foreach ($services as $serviceId) {
                $pivotData[$serviceId] = [
                    'duration' => $durations[$serviceId] ?? 0,
                    'break_duration' => $breaks[$serviceId] ?? 0,
                    'slot_group_id' => $slotGroups[$serviceId] ?? null, // 🔥 WAJIB
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

        return redirect()->route('user.index')->with('success', 'Profil telah berhasil diperbarui');
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
            return back()->withErrors('Pengguna admin pertama tidak dapat dihapus.');
        }

        if ($user->id === $request->user()->id) {
            return back()->withErrors('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Pengguna telah berhasil dipindahkan ke tempat sampah');
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
        return redirect()->back()->with("success", "Pengguna telah berhasil dipulihkan");
    }


    public function force_delete($id)
    {
        // Ambil user termasuk yang sudah soft deleted
        $user = User::withTrashed()->findOrFail($id);

        // ✅ Cek apakah user memiliki employee dan employee memiliki appointment aktif
        if ($user->employee && $user->employee->appointments()->count() > 0) {
            return back()->withErrors('Pengguna tidak dapat dihapus permanen karena masih memiliki booking aktif');
        }

        // ✅ Cek apakah user (member) memiliki appointment aktif
        if ($user->appointments()->count() > 0) {
            return back()->withErrors('Pengguna tidak dapat dihapus permanen karena masih memiliki booking aktif');
        }

        // ✅ Hapus holidays employee jika ada
        if ($user->employee) {
            $user->employee->holidays()->forceDelete();

            // ✅ Lepas semua service pivot employee
            $user->employee->services()->detach();

            // ✅ Hapus employee
            $user->employee->forceDelete();
        }

        // ✅ Hapus profile image jika ada
        if ($user->image) {
            $path = public_path('uploads/images/profile/' . $user->image);
            if (\File::exists($path)) {
                \File::delete($path);
            }
        }

        // ✅ Hapus user permanen
        $user->forceDelete();

        return back()->withSuccess('Pengguna beserta seluruh data terkait (karyawan, hari libur, janji temu, dan pemesanan) telah berhasil dihapus secara permanen');
    }



    public function password_update(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Kata sandi telah berhasil diperbarui');
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

        return back()->withSuccess('Gambar profil telah berhasil diperbarui');
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

        return back()->withSuccess('Gambar profil telah berhasil dihapus');
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

    /**
     * Update member password
     */
    public function memberPasswordUpdate(Request $request)
    {
        $user = auth()->user();

        // Validasi menggunakan validator manual untuk bisa pakai named error bag
        $validator = \Validator::make($request->all(), [
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        // Jika gagal validasi, kembalikan ke named bag 'password'
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator, 'password')->withInput();
        }

        // Cek apakah password lama sesuai
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai!'], 'password');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Flash message khusus untuk password
        return redirect()->back()->with('password_success', 'Password berhasil diperbarui!');
    }
}
