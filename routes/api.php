<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Transaction;
use Carbon\Carbon;

Route::post('/validate-barcode', function (Request $request) {

    $request->validate([
        'barcode' => 'required|string',
    ]);

    $barcode = trim($request->barcode);

    // Cari transaksi + appointment
    $transaction = Transaction::with('appointment')
        ->where('transaction_code', $barcode)
        ->whereIn('payment_status', ['DP', 'Paid'])
        ->first();

    if (!$transaction) {
        return response()->json([
            'success' => false,
            'type' => 'INVALID_TRANSACTION',
            'message' => 'Transaksi tidak valid'
        ], 404);
    }

    $appointment = $transaction->appointment;

    if (!$appointment) {
        return response()->json([
            'success' => false,
            'type' => 'APPOINTMENT_NOT_FOUND',
            'message' => 'Appointment tidak ditemukan'
        ], 400);
    }

    // Jika barcode sudah digunakan
    if ($transaction->used_at !== null) {
        return response()->json([
            'success' => false,
            'type' => 'ALREADY_USED',
            'message' => 'Barcode sudah digunakan sebelumnya'
        ], 403);
    }

    // Validasi hanya tanggal booking (tidak peduli sudah lewat jam berapa)
    $today = Carbon::now()->format('Y-m-d');
    $bookingDate = Carbon::parse($appointment->booking_date)->format('Y-m-d');

    if ($today !== $bookingDate) {
        return response()->json([
            'success' => false,
            'type' => 'WRONG_DATE',
            'message' => 'Barcode hanya bisa digunakan pada hari booking'
        ], 403);
    }

    // Hitung durasi start → end (murni, tanpa telat)
    $start  = Carbon::parse($appointment->booking_date . ' ' . $appointment->booking_start_time);
    $end    = Carbon::parse($appointment->booking_date . ' ' . $appointment->booking_end_time);

    $durationMinutes = $end->diffInMinutes($start);

    // Update appointment
    if ($appointment->status === 'Confirmed') {
        $appointment->update([
            'status' => 'Completed'
        ]);
    }

    // Tandai transaksi digunakan
    $transaction->update([
        'used_at' => Carbon::now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Barcode valid. Silakan mulai sesi foto.',

        'transaction' => [
            'id' => $transaction->id,
            'transaction_code' => $transaction->transaction_code,
            'payment_status' => $transaction->payment_status,
            'used_at' => $transaction->used_at,
        ],

        'appointment' => [
            'id' => $appointment->id,
            'status' => $appointment->status,
            'booking_date' => $appointment->booking_date,
            'booking_start_time' => $appointment->booking_start_time,
            'booking_end_time' => $appointment->booking_end_time,
            'duration_minutes' => $durationMinutes, // 👍 dipakai renderer.js
        ]
    ]);
});
