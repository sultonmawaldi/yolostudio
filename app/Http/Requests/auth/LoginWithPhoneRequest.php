<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginWithPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Nomor HP 9–13 digit tanpa 0 di depan
            'phone' => ['required', 'string', 'regex:/^[1-9][0-9]{8,12}$/'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Nomor HP harus berisi 9–13 tanpa diawali angka 0, karena prefix +62 ditambahkan otomatis.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }
}
