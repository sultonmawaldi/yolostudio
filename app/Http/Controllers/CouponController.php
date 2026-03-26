<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\User;
use App\Models\Service;
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
        $coupons = Coupon::with(['user', 'services'])->orderByDesc('created_at')->get();
        return view('backend.coupons.index', compact('coupons'));
    }

    /**
     * Form tambah kupon (admin/backend)
     */
    public function create()
    {
        $users = User::all();
        $services = Service::all();

        return view('backend.coupons.create', compact('users', 'services'));
    }

    /**
     * Simpan kupon baru (admin/backend)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|integer|min:0',
            'minimum_cart_value' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|array',
            'service_id.*' => 'exists:services,id',
            'active' => 'required|in:0,1',
        ]);

        // 🔥 Ambil service_id lalu hapus dari data utama
        $serviceIds = $data['service_id'] ?? [];
        unset($data['service_id']);

        // Default status
        $data['status'] = 'unused';

        // Simpan kupon
        $coupon = Coupon::create($data);

        // 🔥 Simpan relasi ke pivot (kalau ada)
        if (!empty($serviceIds)) {
            $coupon->services()->sync($serviceIds);
        }

        return redirect()->route('coupons.index')
            ->with('success', 'Kupon berhasil dibuat.');
    }

    /**
     * Form edit kupon (admin/backend)
     */
    public function edit(Coupon $coupon)
    {
        $users = User::all();
        $services = Service::all();

        return view('backend.coupons.edit', compact('coupon', 'users', 'services'));
    }

    /**
     * Update kupon (admin/backend)
     */
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|integer|min:0',
            'minimum_cart_value' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id',
            'service_id' => 'nullable|array',
            'service_id.*' => 'exists:services,id',
            'active' => 'required|in:0,1',
            'status' => 'required|in:unused,used,expired',
        ]);

        // 🔥 Ambil service_id lalu hapus dari data utama
        $serviceIds = $data['service_id'] ?? [];
        unset($data['service_id']);

        // Update kupon
        $coupon->update($data);

        // 🔥 Update relasi pivot (kalau kosong = hapus semua relasi)
        $coupon->services()->sync($serviceIds);

        return redirect()->route('coupons.index')
            ->with('success', 'Kupon berhasil diperbarui.');
    }

    /**
     * Hapus kupon (admin/backend)
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('coupons.index')
            ->with('success', 'Kupon berhasil dihapus.');
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

        $requiredPoints = 100;
        $couponValue = 100000;

        return view('frontend.member.coupons.redeem', [
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

        $requiredPoints = 100;
        $couponValue = 100000;

        if ($user->points < $requiredPoints) {
            return redirect()->back()
                ->with('error', 'Point Anda tidak cukup untuk menukar kupon.');
        }

        // Kurangi point
        $user->decrement('points', $requiredPoints);

        // Generate kode kupon
        $code = 'REWARD-' . Str::upper(Str::random(6));

        // ✅ BUAT KUPON
        $coupon = Coupon::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'fixed',
            'value' => $couponValue,
            'minimum_cart_value' => null,
            'expiry_date' => null,
            'active' => 1,
            'status' => 'unused',
        ]);

        /**
         * ============================================
         * 🔥 ATTACH KE PIVOT (WAJIB)
         * ============================================
         */
        $coupon->services()->attach([1, 2, 3]);

        return redirect()->route('member.coupons.redeem')
            ->with('success', "Berhasil menukar $requiredPoints point menjadi kupon.");
    }
}
