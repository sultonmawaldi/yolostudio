<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'type',               // fixed / percentage
        'value',              // nilai diskon
        'minimum_cart_value', // minimal transaksi
        'expiry_date',        // tanggal kadaluarsa
        'active',             // 0 / 1
        'status',             // unused / used / expired
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Relasi ke user pemilik kupon
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke transaksi yang menggunakan kupon ini
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'coupon_id');
    }

    /**
     * Cek apakah kupon masih valid
     */
    public function isValid(): bool
    {
        return $this->active &&
            $this->status === 'unused' &&
            (!$this->expiry_date || $this->expiry_date->isFuture());
    }

    /**
     * Tandai kupon sebagai digunakan
     */
    public function markAsUsed(): void
    {
        $this->update(['status' => 'used']);
    }

    /**
     * Tandai kupon sebagai expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Tandai kupon sebagai aktif kembali (jika perlu reset manual)
     */
    public function resetStatus(): void
    {
        $this->update(['status' => 'unused', 'active' => true]);
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'coupon_service',
            'coupon_id',
            'service_id'
        );
    }
}
