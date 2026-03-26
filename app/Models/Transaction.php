<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'transaction_code',

        'dp_method',
        'pelunasan_method',

        'amount',
        'total_amount',
        'payment_status',

        'midtrans_order_id',
        'payment_result',
        'payload',
        'coupon_id',
        'qr_url',
        'rewarded_at',
    ];

    protected $casts = [
        'payment_result' => 'array',
        'payload' => 'array',
        'public_token_expires_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    /**
     * Relasi ke appointment
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Relasi ke user pemilik transaksi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Relasi ke kupon (jika ada)
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * Scope transaksi Paid
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'Paid');
    }

    /**
     * Scope transaksi DP
     */
    public function scopeDp($query)
    {
        return $query->where('payment_status', 'DP');
    }

    public function photoResults()
    {
        return $this->hasMany(PhotoResult::class, 'transaction_id');
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $transaction->public_token = bin2hex(random_bytes(8));
            $transaction->public_token_expires_at = now()->addDays(7);
        });

        // 🔥 TAMBAHKAN INI
        static::updated(function ($transaction) {
            if (
                in_array($transaction->payment_status, ['Paid', 'settlement', 'capture']) &&
                $transaction->coupon_id
            ) {
                Coupon::where('id', $transaction->coupon_id)
                    ->where('status', 'unused')
                    ->update([
                        'status' => 'used',
                        'used_at' => now()
                    ]);
            }
        });
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price', 'qty')
            ->withTimestamps();
    }
}
