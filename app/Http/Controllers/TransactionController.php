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
     * STORE TRANSACTION
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

        // 🔥 FIX STATUS AWAL
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
                    'coupon_id' => 'Kupon tidak valid atau sudah digunakan'
                ]);
            }

            $validated['total_amount'] -= $coupon->value;
        }

        Transaction::create($validated);

        return redirect()->route('member.dashboard')
            ->with('success', 'Transaksi berhasil dibuat');
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
            // 🔥 FIX ENUM LENGKAP
            'dp_method'        => 'nullable|in:Cash,Midtrans,Coupon',
            'pelunasan_method' => 'nullable|in:Cash,Midtrans,Coupon',

            // 🔥 TAMBAH EXPIRED
            'payment_status'   => 'required|in:Pending,DP,Paid,Failed,Expired',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Status transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * ============================
     * PAY REMAINING VIA MIDTRANS (ADMIN)
     * ============================
     */
    public function payRemainingMidtrans(Transaction $transaction)
    {
        if ($transaction->payment_status === 'Paid') {
            return back()->with('info', 'Transaksi sudah lunas');
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $remainingAmount = $transaction->total_amount - $transaction->amount;

        if ($remainingAmount <= 0) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi sudah tidak memiliki sisa pembayaran');
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
            // 🔥 EXPIRY AUTO (WAJIB BIAR QRIS EXPIRE)
            'expiry' => [
                'unit' => 'minute',
                'duration' => 15
            ],
            'callbacks' => [
                'finish' => route('transactions.payRemainingMidtrans', $transaction->id),
            ]
        ];

        // 🔥 HANDLE CALLBACK UI
        if (request()->has('result_type') && request()->get('result_type') === 'success') {

            if ($transaction->payment_status === 'Paid') {
                return redirect()->route('transactions.index')
                    ->with('info', 'Transaksi sudah lunas');
            }

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
                ->with('success', 'Pelunasan via Midtrans berhasil');
        }

        $snapToken = Snap::getSnapToken($params);

        return view('backend.transactions.pay_remaining', compact(
            'transaction',
            'snapToken'
        ));
    }

    /**
     * ============================
     * PAY REMAINING CASH
     * ============================
     */
    public function payRemainingCash(Transaction $transaction)
    {
        $transaction->load('services');

        $transaction->update([
            'payment_status' => 'Paid',
            'amount' => $transaction->total_amount,
            'pelunasan_method' => 'Cash',
        ]);

        if ($transaction->appointment) {
            $transaction->appointment->update(['status' => 'Confirmed']);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Pelunasan tunai berhasil');
    }

    /**
     * ============================
     * MIDTRANS MEMBER
     * ============================
     */
    public function memberPayRemainingMidtrans(Transaction $transaction)
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) abort(403);

        if ($transaction->payment_status === 'Paid') {
            return redirect()->route('member.transactions.index')
                ->with('info', 'Transaksi sudah lunas');
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
            'expiry' => [
                'unit' => 'minute',
                'duration' => 15
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
     * CALLBACK UI (Bukan webhook)
     * ============================
     */
    public function paymentFinish(Request $request, Transaction $transaction)
    {
        return redirect()->route('transactions.index')
            ->with('info', 'Menunggu konfirmasi pembayaran...');
    }
}
