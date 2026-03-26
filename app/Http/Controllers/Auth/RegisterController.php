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
     */
    protected $redirectTo = '/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Normalisasi nomor HP ke format +62xxxxxxxxx
     */
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
     * Validasi input pendaftaran
     */
    protected function validator(array $data)
    {
        // Normalisasi phone dulu sebelum validasi unique
        $normalizedPhone = $this->normalizePhone($data['phone'] ?? '');

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'regex:/^[0-9+\s\-()]+$/',
                function ($attribute, $value, $fail) use ($normalizedPhone) {
                    if (User::where('phone', $normalizedPhone)->exists()) {
                        $fail('Nomor HP ini sudah terdaftar.');
                    }
                },
            ],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Format nomor HP tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);
    }

    /**
     * Membuat user baru setelah validasi berhasil
     */
    protected function create(array $data)
    {
        $formattedPhone = $this->normalizePhone($data['phone']);

        // Format role UID profesional
        $roleUid = sprintf(
            'MBR-%s-%s',
            date('ymd'),
            strtoupper(Str::random(5))
        );

        $user = User::create([
            'role_uid' => $roleUid,
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
            'phone'    => $formattedPhone,
            'password' => Hash::make($data['password']),
            'status'   => 1,
        ]);

        // Assign role member (spatie)
        $user->assignRole('member');

        return $user;
    }
}
