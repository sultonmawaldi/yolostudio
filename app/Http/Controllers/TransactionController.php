<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Appointment;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Snap;
use Midtrans\Config;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransactionController extends Controller
{
    /**
     * ============================
     * MEMBER DASHBOARD
     * ============================
     */
    public function memberIndex()
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->get();

        $coupons = Coupon::where('user_id', $user->id)->get();
        $usedCoupons = $coupons->where('status', 'used')->count();
        $newCouponMessage = session('new_coupon');

        // ✅ Ganti view dari dashboard ke transactions.index
        return view('frontend.member.transactions.index', compact(
            'transactions',
            'coupons',
            'usedCoupons',
            'newCouponMessage'
        ));
    }


    public function memberShow(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('frontend.member.transactions.show', compact('transaction'));
    }

    /**
     * ============================
     * ADMIN LIST
     * ============================
     */
    public function index()
    {
        $transactions = Transaction::with(
            'appointment.service',
            'appointment.employee.user'
        )
            ->latest()
            ->get();

        return view('backend.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $appointments = Appointment::with('service', 'employee.user')->get();
        return view('backend.transactions.create', compact('appointments'));
    }

    /**
     * ============================
     * STORE TRANSACTION + QR
     * ============================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'coupon_id' => 'nullable|exists:coupons,id',
        ]);

        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['transaction_code'] =
            'TRX-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        $validated['payment_status'] = 'Pending';

        // ====== DISKON KUPON ======
        if (!empty($validated['coupon_id'])) {
            $coupon = Coupon::where('id', $validated['coupon_id'])
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereNull('user_id');
                })
                ->where('status', 'unused')
                ->first();

            if (!$coupon) {
                return back()->withErrors(['coupon_id' => 'Kupon tidak valid atau sudah digunakan.']);
            }

            $validated['total_amount'] -= $coupon->value;
        }

        $transaction = Transaction::create($validated);

        /**
         * ==============================
         * QR CODE GENERATOR — SAFE MODE
         * ==============================
         */
        try {
            $qrDir = public_path('qrcodes');
            if (!file_exists($qrDir)) {
                mkdir($qrDir, 0777, true);
            }

            $qrPath = 'qrcodes/' . $transaction->transaction_code . '.png';
            $fullPath = public_path($qrPath);

            $png = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate($transaction->transaction_code);

            file_put_contents($fullPath, $png);

            $transaction->update(['qr_url' => $qrPath]);
        } catch (\Exception $e) {
            \Log::error('QR Generation Error: ' . $e->getMessage());
        }

        return redirect()->route('member.dashboard')
            ->with('success', 'Transaksi berhasil dibuat!');
    }

    /**
     * ============================
     * EDIT / UPDATE
     * ============================
     */
    public function edit(Transaction $transaction)
    {
        $appointments = Appointment::with('service', 'employee.user')->get();
        return view('backend.transactions.edit', compact('transaction', 'appointments'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'coupon_id' => 'nullable|exists:coupons,id',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * ============================
     * PAY REMAINING (ADMIN)
     * ============================
     */
    public function payRemainingCash(Transaction $transaction)
    {
        // Load services agar observer bisa hitung points
        $transaction->load('services');

        $transaction->update([
            'payment_status' => 'Paid',
            'amount' => $transaction->total_amount,
            'payment_method' => 'Cash'
        ]);


        if ($transaction->appointment) {
            $transaction->appointment->update(['status' => 'Confirmed']);
        }

        // Generate QR jika belum ada
        if (empty($transaction->qr_url)) {
            try {
                $qrDir = public_path('qrcodes');
                if (!file_exists($qrDir)) {
                    mkdir($qrDir, 0777, true);
                }

                $qrPath = 'qrcodes/' . $transaction->transaction_code . '.png';
                $fullPath = public_path($qrPath);

                $png = QrCode::format('png')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($transaction->transaction_code);

                file_put_contents($fullPath, $png);

                $transaction->update(['qr_url' => $qrPath]);
            } catch (\Exception $e) {
                \Log::error("QR Generation Failed: " . $e->getMessage());
            }
        }

        return redirect()->route('member.dashboard')
            ->with('success', 'Pelunasan tunai berhasil.');
    }

    /**
     * ============================
     * MIDTRANS (member)
     * ============================
     */
    public function memberPayRemainingMidtrans(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) abort(403);

        if ($transaction->payment_status === 'Paid') {
            return redirect()->route('member.transactions.index')
                ->with('info', 'Transaksi sudah lunas.');
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => $transaction->total_amount - $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'callbacks' => [
                'finish' => route('member.payment.finish', $transaction->id),
            ]
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('frontend.member.transactions.pay_remaining', compact(
            'transaction',
            'snapToken'
        ));
    }

    /**
     * ============================
     * MIDTRANS CALLBACK
     * ============================
     */
    public function paymentFinish(Request $request, Transaction $transaction)
    {
        $status = $request->get('transaction_status');
        $orderId = $request->get('order_id');

        if ($transaction->transaction_code !== $orderId) {
            return redirect()->route('member.transactions.index', ['info' => 'notfound']);
        }

        if (in_array($status, ['capture', 'settlement'])) {
            // Load services agar observer bisa hitung points
            $transaction->load('services');

            $transaction->update([
                'payment_status' => 'Paid',
                'amount' => $transaction->total_amount,
            ]);

            return redirect()->route('member.transactions.index', [
                'paid' => 'true',
                'transaction_code' => $transaction->transaction_code
            ]);
        }

        if ($status === 'pending') {
            $transaction->update(['payment_status' => 'DP']);
            return redirect()->route('member.transactions.index', ['pending' => 'true']);
        }

        $transaction->update(['payment_status' => 'Failed']);
        return redirect()->route('member.transactions.index', ['failed' => 'true']);
    }
}
