<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PointController extends Controller
{
    // Tampilkan halaman redeem
    public function redeemPage()
    {
        $user = Auth::user();
        return view('frontend.member.points.redeem', compact('user'));
    }

    // Proses redeem point
    public function redeem(Request $request)
    {
        $user = Auth::user();
        $requiredPoints = 30;

        if ($user->points < $requiredPoints) {
            return back()->with('error', 'Point Anda tidak cukup untuk menukar kupon.');
        }

        // Ambil kupon kosong (unused)
        $coupon = Coupon::whereNull('user_id')
            ->where('status', 'unused')
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Maaf, tidak ada kupon yang tersedia untuk ditukar.');
        }

        // Assign kupon ke user
        $coupon->update([
            'user_id' => $user->id,
            'status' => 'unused',
            'code' => 'REWARD-' . strtoupper(Str::random(6)),
        ]);

        // Kurangi point user
        $user->decrement('points', $requiredPoints);

        return back()->with('success', "Point berhasil ditukar! Anda mendapatkan kupon: {$coupon->code}");
    }
}
