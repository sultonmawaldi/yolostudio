<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpireQrisPayment extends Command
{
    protected $signature = 'app:expire-qris-payment';

    protected $description = 'Auto cancel expired QRIS payments';

    public function handle()
    {
        Log::info('QRIS EXPIRE CHECK START: ' . now());

        try {
            $expiredTransactions = Transaction::with('appointment')
                ->where('payment_status', 'Pending')
                ->where('payment_method', 'qris')
                ->whereNotNull('payment_expired_at')
                ->where('payment_expired_at', '<', now())
                ->get();

            Log::info('QRIS EXPIRED FOUND: ' . $expiredTransactions->count());

            foreach ($expiredTransactions as $transaction) {

                DB::beginTransaction();

                try {
                    Log::info('EXPIRE TRANSACTION ID: ' . $transaction->id);

                    // update transaction
                    $transaction->update([
                        'payment_status' => 'Failed',
                    ]);

                    // update appointment jika ada
                    if ($transaction->appointment) {
                        $transaction->appointment->update([
                            'status' => 'Cancelled'
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();

                    Log::error('QRIS EXPIRE ERROR ID ' . $transaction->id . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('QRIS EXPIRE GLOBAL ERROR: ' . $e->getMessage());
        }

        Log::info('QRIS EXPIRE CHECK END: ' . now());

        return 0;
    }
}
