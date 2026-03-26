<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginWithPhoneRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->middleware('throttle:3,1')->only('login');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginWithPhoneRequest $request)
    {
        $phone = $this->normalizePhone($request->phone);

        $user = User::where('phone', $phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Nomor HP atau kata sandi salah.'],
            ]);
        }

        if (!$user->status) {
            throw ValidationException::withMessages([
                'phone' => ['Akses akun Anda dinonaktifkan.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect($this->redirectTo($user));
    }


    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone);
        return '+62' . $phone; // Karena UI +62 ditampilkan
    }

    protected function redirectTo($user)
    {
        if ($user->hasRole(['admin', 'moderator', 'employee'])) {
            return route('dashboard');
        }

        if ($user->hasRole('member')) {
            return route('home');
        }

        Auth::logout();
        abort(403, 'Role tidak dikenali.');
    }


    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function sendWhatsappLoginNotification($user)
    {
        $phone = $user->phone;
        $message = "Halo {$user->name}, Anda baru saja login.";
        // Integrasi API WhatsApp bisa dipanggil di sini
        // Example: WA::sendMessage($phone, $message);
    }
}
