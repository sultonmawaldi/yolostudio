<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Coupon;
use Illuminate\Support\Str;
use App\Events\CouponGenerated;

class TransactionObserver
{
    /**
     * Event ketika transaksi baru dibuat
     */
    public function created(Transaction $transaction)
    {
        $this->handleSuccessfulTransaction($transaction);
    }

    /**
     * Event ketika transaksi diupdate
     */
    public function updated(Transaction $transaction)
    {
        if (
            $transaction->wasChanged('payment_status') &&
            in_array($transaction->payment_status, ['Paid', 'DP', 'Cash'])
        ) {
            $this->handleSuccessfulTransaction($transaction);
        }
    }

    /**
     * Logika reward & kupon
     */
    protected function handleSuccessfulTransaction(Transaction $transaction)
    {
        $user = $transaction->user;
        if (!$user) {
            return;
        }

        // ✅ Jika transaksi pakai kupon → tandai kupon used
        if ($transaction->coupon_id) {
            $coupon = $transaction->coupon;
            if (
                $coupon &&
                $coupon->status === 'unused' &&
                $coupon->user_id === $user->id
            ) {
                $coupon->update(['status' => 'used']);
            }
        }

        // ✅ Hitung total transaksi sukses (Paid, DP, Cash) yang tidak pakai kupon
        $successfulCount = $user->transactions()
            ->whereIn('payment_status', ['Paid', 'DP', 'Cash'])
            ->whereNull('coupon_id')
            ->count();

        // ✅ Generate kupon baru setiap kelipatan 3 transaksi
        if ($successfulCount > 0 && $successfulCount % 3 === 0) {
            $coupon = Coupon::create([
                'user_id'            => $user->id,
                'code'               => 'REWARD-' . strtoupper(Str::random(6)),
                'type'               => 'fixed',
                'value'              => 50000,
                'minimum_cart_value' => 100000,
                'expiry_date'        => now()->addDays(30),
                'active'             => 1,
                'status'             => 'unused',
            ]);

            // 🔥 Trigger event untuk listener
            event(new CouponGenerated($coupon));
        }
    }
}
