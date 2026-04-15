<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $user->load([
            'authentications' => function ($query) {
                $query->orderByDesc('login_at')
                    ->limit(100);
            }
        ]);

        return view('backend.profile.index', compact('user'));
    }

    public function profileUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // hanya admin yang boleh ubah email
        if ($request->email !== $user->email && !auth()->user()->hasRole('admin')) {
            return back()->withErrors([
                'email' => 'Hanya administrator yang dapat mengubah email'
            ]);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function employeeProfileUpdate(Request $request, Employee $employee)
    {
        // karena di HTML sekarang cuma bio + social
        $data = $request->validate([
            'bio' => 'nullable|string',
            'social.facebook'  => 'nullable|string',
            'social.instagram' => 'nullable|string',
            'social.x'   => 'nullable|string',
            'social.linkedin'  => 'nullable|string',
        ]);

        $employee->update([
            'bio'    => $data['bio'] ?? null,
            'social' => $data['social'] ?? [],
        ]);

        return back()->with('success', 'Bio berhasil diperbarui');
    }
}
