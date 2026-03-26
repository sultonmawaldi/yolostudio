<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Appointment;
use App\Models\Coupon;
use App\Models\Employee;
use App\Models\Service;
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
        $transactions = Transaction::with(['appointment.employee.user', 'appointment.service'])
            ->latest()
            ->get();

        $employees = Employee::with('user')->get();
        $services = Service::all();

        return view('backend.transactions.index', compact('transactions', 'employees', 'services'));
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
            'appointment_id'   => 'required|exists:appointments,id',
            'dp_method'        => 'nullable|in:Cash,Midtrans,Coupon',
            'pelunasan_method' => 'nullable|in:Cash,Midtrans,Coupon',
            'amount'           => 'required|numeric',
            'total_amount'     => 'required|numeric',
            'coupon_id'        => 'nullable|exists:coupons,id',
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
                return back()->withErrors([
                    'coupon_id' => 'Kupon tidak valid atau sudah digunakan.'
                ]);
            }

            $validated['total_amount'] -= $coupon->value;
        }

        // =========================
        // CREATE TRANSACTION
        // =========================
        Transaction::create($validated);

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
            'dp_method'        => 'nullable|in:Cash,Midtrans',
            'pelunasan_method' => 'nullable|in:Cash,Midtrans',
            'payment_status'   => 'required|in:Pending,DP,Paid,Failed',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }


    /**
     * ============================
     * PAY REMAINING VIA MIDTRANS (ADMIN)
     * ============================
     */
    public function payRemainingMidtrans(Transaction $transaction)
    {
        // 🔥 CEK JIKA SUDAH LUNAS
        if ($transaction->payment_status === 'Paid') {
            return back()->with('info', 'Transaksi sudah lunas.');
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        // 🔥 HITUNG SISA PEMBAYARAN
        $remainingAmount = $transaction->total_amount - $transaction->amount;

        // 🔥 TAMBAHAN PENTING (ANTI ERROR 400 MIDTRANS)
        if ($remainingAmount <= 0) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi sudah digunakan sebelumnya dan hanya dapat digunakan 1 kali.');
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_code,
                'gross_amount' => $remainingAmount,
            ],
            'customer_details' => [
                'first_name' => $transaction->appointment->name ?? 'Customer',
                'email' => $transaction->appointment->email ?? 'admin@local.test',
            ],
            'callbacks' => [
                // route finish diarahkan ke callback controller
                'finish' => route('transactions.payRemainingMidtrans', $transaction->id),
            ]
        ];

        // Jika ini callback finish Midtrans
        if (request()->has('result_type') && request()->get('result_type') === 'success') {

            // 🔥 CEK LAGI UNTUK MENCEGAH DOUBLE CALLBACK
            if ($transaction->payment_status === 'Paid') {
                return redirect()->route('transactions.index')
                    ->with('info', 'Transaksi sudah lunas.');
            }

            // Load services agar observer bisa hitung points
            $transaction->load('services');

            $transaction->update([
                'payment_status' => 'Paid',
                'amount' => $transaction->total_amount,
                'pelunasan_method' => 'Midtrans',
            ]);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Confirmed']);
            }

            return redirect()->route('transactions.index')
                ->with('success', 'Pelunasan via Midtrans berhasil.');
        }

        // Generate Snap Token
        $snapToken = Snap::getSnapToken($params);

        return view('backend.transactions.pay_remaining', compact(
            'transaction',
            'snapToken'
        ));
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
            'pelunasan_method' => 'Cash', // 🔥 TAMBAHKAN INI
        ]);

        if ($transaction->appointment) {
            $transaction->appointment->update(['status' => 'Confirmed']);
        }

        return redirect()->route('transactions.index')
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
            return redirect()->route('transactions.index')
                ->with('error', 'Order ID tidak cocok.');
        }

        if (in_array($status, ['capture', 'settlement'])) {
            $transaction->update([
                'payment_status' => 'Paid',
                'amount' => $transaction->total_amount,
                'pelunasan_method' => 'Midtrans', // 🔥 TAMBAHKAN INI
            ]);

            if ($transaction->appointment) {
                $transaction->appointment->update(['status' => 'Confirmed']);
            }

            return redirect()->route('transactions.index')
                ->with('success', 'Pembayaran berhasil dan status lunas.');
        }

        if ($status === 'pending') {
            $transaction->update([
                'payment_status' => 'DP'
            ]);

            return redirect()->route('transactions.index')
                ->with('info', 'Pembayaran masih pending.');
        }

        $transaction->update([
            'payment_status' => 'Failed'
        ]);

        return redirect()->route('transactions.index')
            ->with('error', 'Pembayaran gagal.');
    }
}
