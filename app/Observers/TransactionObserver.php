<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\PointLog;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Jalankan saat transaksi dibuat
     */
    public function created(Transaction $transaction)
    {
        $this->handleReward($transaction);
    }

    /**
     * Jalankan saat transaksi diupdate
     */
    public function updated(Transaction $transaction)
    {
        $this->handleReward($transaction);
    }

    /**
     * Logika reward POINT
     */
    protected function handleReward(Transaction $transaction)
    {
        Log::info('handleReward called', [
            'transaction_id' => $transaction->id,
            'status' => $transaction->payment_status
        ]);

        // 1️⃣ Pastikan transaksi sudah dibayar
        if ($transaction->payment_status !== 'Paid') {
            Log::info('Not Paid yet', ['transaction_id' => $transaction->id]);
            return;
        }

        // 🔥 TAMBAHAN: update coupon (AMAN, tidak ganggu logic lama)
        if ($transaction->relationLoaded('coupon') || method_exists($transaction, 'coupon')) {
            $transaction->loadMissing('coupon');

            if ($transaction->coupon && $transaction->coupon->status === 'unused') {
                $transaction->coupon->update([
                    'status' => 'used',
                    'used_at' => now()
                ]);

                Log::info('Coupon marked as used', [
                    'transaction_id' => $transaction->id,
                    'coupon_id' => $transaction->coupon->id
                ]);
            }
        }

        // 2️⃣ Pastikan belum pernah direward
        if ($transaction->rewarded_at) {
            Log::info('Already rewarded', ['transaction_id' => $transaction->id]);
            return;
        }

        // 3️⃣ Load relasi yang dibutuhkan
        $transaction->loadMissing(['user', 'appointment.service']);

        $user = $transaction->user;
        $appointment = $transaction->appointment;

        if (!$user) {
            Log::warning('No user found', ['transaction_id' => $transaction->id]);
            return;
        }

        if (! $user->hasRole('member')) {
            Log::info('User bukan member, skip reward', ['user_id' => $user->id]);
            return;
        }

        // 4️⃣ Hitung points dari service appointment
        $totalPoints = 0;
        if ($appointment && $appointment->service) {
            $totalPoints = (int) ($appointment->service->reward_points ?? 0);
        }

        Log::info('Total points calculated', [
            'transaction_id' => $transaction->id,
            'totalPoints' => $totalPoints
        ]);

        if ($totalPoints <= 0) {
            Log::info('No points to reward', ['transaction_id' => $transaction->id]);
            return;
        }

        // 5️⃣ Tambahkan points ke user
        $user->increment('points', $totalPoints);

        PointLog::create([
            'user_id' => $user->id,
            'points' => $totalPoints,
            'type' => 'earn',
            'description' => 'Point dari transaksi #' . $transaction->id,
        ]);

        // 6️⃣ Tandai transaksi sudah direward
        $transaction->updateQuietly(['rewarded_at' => now()]);

        Log::info('Points awarded successfully', [
            'transaction_id' => $transaction->id,
            'points' => $totalPoints
        ]);
    }
}
