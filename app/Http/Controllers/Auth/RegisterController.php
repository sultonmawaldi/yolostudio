<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Redirect setelah register berhasil
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Konstruktor
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Validasi input pendaftaran
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            // ✅ Nomor HP tanpa awalan 0, hanya angka, panjang 9–13 digit
            'phone' => [
                'required',
                'regex:/^[1-9][0-9]{8,12}$/',
                'unique:users,phone',
            ],

            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'phone.regex' => 'Nomor HP harus berisi 9–13 digit angka dan tidak boleh diawali dengan 0.',
            'phone.unique' => 'Nomor HP ini sudah terdaftar.',
        ]);
    }

    /**
     * Membuat user baru setelah validasi berhasil.
     */
    protected function create(array $data)
    {
        // 🔹 Bersihkan nomor HP (hapus semua non-digit)
        $number = preg_replace('/\D/', '', $data['phone']);

        // Tidak perlu hapus nol di depan karena regex sudah memastikan tidak ada 0
        $formattedPhone = '+62' . $number;

        // 🔹 Format role_uid profesional, contoh: MBR-251028-A9T2F
        $rolePrefix = 'MBR';
        $roleUid = sprintf('%s-%s-%s', $rolePrefix, date('ymd'), strtoupper(Str::random(5)));

        // 🔹 Buat user baru
        $user = User::create([
            'role_uid' => $roleUid,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $formattedPhone,
            'password' => Hash::make($data['password']),
            'status'   => 1,
        ]);

        // 🔹 Beri role member
        $user->assignRole('member');

        return $user;
    }
}
