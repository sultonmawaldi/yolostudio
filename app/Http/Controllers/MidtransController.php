<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function notificationHandler(Request $request)
    {
        // 🔥 CONFIG MIDTRANS
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        $notification = new Notification();

        $orderId = $notification->order_id;
        $status  = $notification->transaction_status;
        $gross   = (float) $notification->gross_amount;

        // 🔍 CARI TRANSAKSI
        $transaction = Transaction::where('midtrans_order_id', $orderId)
            ->orWhere('transaction_code', $orderId)
            ->first();

        if (!$transaction) {
            Log::error("⚠️ Midtrans callback: Transaction not found (Order ID: {$orderId})");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // 🔥 ANTI DOUBLE PROCESS
        if ($transaction->payment_status === 'Paid') {
            Log::info("⚠️ Already paid: {$orderId}");
            return response()->json(['message' => 'Already processed']);
        }

        Log::info("📩 Midtrans Notification", [
            'order_id' => $orderId,
            'status'   => $status,
            'gross'    => $gross,
        ]);

        /**
         * =========================================
         * ✅ SUCCESS PAYMENT (DP / FULL)
         * =========================================
         */
        if (in_array($status, ['capture', 'settlement'])) {

            $newAmount = $transaction->amount + $gross;

            // 🔥 FULL PAYMENT
            if ($newAmount >= $transaction->total_amount) {

                $transaction->update([
                    'payment_status' => 'Paid',
                    'amount'         => $transaction->total_amount,
                ]);

                if ($transaction->appointment) {
                    $transaction->appointment->update(['status' => 'Confirmed']);
                }

                Log::info("✅ FULL PAID: {$orderId}");
            }
            // 🔥 DP PAYMENT
            else {

                $transaction->update([
                    'payment_status' => 'DP',
                    'amount'         => $newAmount,
                ]);

                if ($transaction->appointment) {
                    $transaction->appointment->update(['status' => 'Processing']);
                }

                Log::info("💰 DP PAYMENT: {$orderId}, amount: {$newAmount}");
            }
        }

        /**
         * =========================================
         * ⏳ PENDING (QRIS / VA BELUM BAYAR)
         * =========================================
         */
        elseif ($status === 'pending') {

            $transaction->update([
                'payment_status' => 'Pending',
            ]);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Processing']);
            }

            Log::info("⏳ PENDING: {$orderId}");
        }

        /**
         * =========================================
         * ⌛ EXPIRED (AUTO DARI MIDTRANS)
         * =========================================
         */
        elseif ($status === 'pending') {

            $transaction->update([
                'payment_status' => 'Pending',
            ]);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Pending']);
            }
        }

        /**
         * =========================================
         * ❌ FAILED / CANCEL
         * =========================================
         */
        elseif (in_array($status, ['deny', 'cancel'])) {

            $transaction->update([
                'payment_status' => 'Failed',
            ]);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Cancelled']);
            }

            Log::warning("❌ FAILED: {$orderId} ({$status})");
        }

        return response()->json(['message' => 'Notification processed']);
    }
}
