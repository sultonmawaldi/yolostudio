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
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        $notification = new Notification();

        $orderId = $notification->order_id;
        $status  = $notification->transaction_status;
        $gross   = (float) $notification->gross_amount;

        $transaction = Transaction::where('midtrans_order_id', $orderId)
            ->orWhere('transaction_code', $orderId)
            ->first();


        if (!$transaction) {
            Log::error("⚠️ Midtrans callback: Transaction not found (Order ID: {$orderId})");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        Log::info("📩 Midtrans Notification", [
            'order_id' => $orderId,
            'status'   => $status,
            'gross'    => $gross,
        ]);

        if (in_array($status, ['capture', 'settlement'])) {

            $newAmount = $transaction->amount + $gross;

            if ($newAmount >= $transaction->total_amount) {
                $transaction->update([
                    'payment_status' => 'Paid',
                    'amount'         => $transaction->total_amount,
                ]);

                if ($transaction->appointment) {
                    $transaction->appointment->update(['status' => 'Confirmed']);
                }

                Log::info("✅ Transaction {$orderId} fully paid.");
            } else {
                $transaction->update([
                    'payment_status' => 'DP',
                    'amount'         => $newAmount,
                ]);

                if ($transaction->appointment) {
                    $transaction->appointment->update(['status' => 'Processing']);
                }

                Log::info("💰 Transaction {$orderId} updated to DP. Current amount: {$newAmount}");
            }
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {

            $transaction->update(['payment_status' => 'Failed']);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Cancelled']);
            }

            Log::warning("❌ Transaction {$orderId} failed ({$status})");
        }

        return response()->json(['message' => 'Notification processed']);
    }
}
