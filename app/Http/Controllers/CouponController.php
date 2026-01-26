<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    // ==========================
    // BACKEND / ADMIN METHODS
    // ==========================

    /**
     * Tampilkan daftar kupon (admin/backend)
     */
    public function index()
    {
        $coupons = Coupon::with('user')->orderByDesc('created_at')->get();
        return view('backend.coupons.index', compact('coupons'));
    }

    /**
     * Form tambah kupon (admin/backend)
     */
    public function create()
    {
        $users = User::all();
        return view('backend.coupons.create', compact('users'));
    }

    /**
     * Simpan kupon baru (admin/backend)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_cart_value' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'active' => 'required|in:0,1',
        ]);

        // Status selalu 'unused'
        $data['status'] = 'unused';

        Coupon::create($data);

        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil dibuat.');
    }

    /**
     * Form edit kupon (admin/backend)
     */
    public function edit(Coupon $coupon)
    {
        $users = User::all();
        return view('backend.coupons.edit', compact('coupon', 'users'));
    }

    /**
     * Update kupon (admin/backend)
     */
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_cart_value' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'active' => 'required|in:0,1',
            'status' => 'required|in:unused,used,expired', // admin bisa ubah status
        ]);

        $coupon->update($data);

        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil diperbarui.');
    }

    /**
     * Hapus kupon (admin/backend)
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('coupons.index')->with('success', 'Kupon berhasil dihapus.');
    }

    // ==========================
    // FRONTEND / MEMBER METHODS
    // ==========================

    public function memberIndex()
    {
        $coupons = Coupon::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.member.coupons.index', compact('coupons'));
    }


    /**
     * Halaman redeem point (member)
     */
    public function redeem()
    {
        $user = Auth::user();

        $requiredPoints = 30; // contoh: 30 point = 100rb
        $couponValue = 100000;

        return view('frontend.member.coupons.redeem', [  // <-- ganti ke 'coupons'
            'points' => $user->points ?? 0,
            'requiredPoints' => $requiredPoints,
            'couponValue' => $couponValue,
        ]);
    }


    /**
     * Proses redeem point menjadi kupon (member)
     */
    public function redeemStore(Request $request)
    {
        $user = Auth::user();

        $requiredPoints = 30;
        $couponValue = 100000;

        if ($user->points < $requiredPoints) {
            return redirect()->back()->with('error', 'Point Anda tidak cukup untuk menukar kupon.');
        }

        // Kurangi point user
        $user->decrement('points', $requiredPoints);

        // Generate kode kupon unik
        $code = 'REWARD-' . Str::upper(Str::random(6));

        // Simpan kupon
        Coupon::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'fixed',
            'value' => $couponValue,
            'minimum_cart_value' => $couponValue,
            'expiry_date' => now()->addMonth(),
            'active' => 1,
            'status' => 'unused',
        ]);

        return redirect()->route('member.coupons.redeem')
            ->with('success', "Berhasil menukar $requiredPoints point menjadi kupon Rp " . number_format($couponValue));
    }
}
