<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'file_path',
        'file_name',
        'public_url',
        'mime_type',
        'file_size',
        'uploaded_at',
    ];

    protected $dates = ['uploaded_at', 'deleted_at'];

    /**
     * Relasi ke Transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Akses URL publik otomatis (helper)
     */
    public function getUrlAttribute()
    {
        if ($this->public_url) {
            return $this->public_url;
        }

        return asset('storage/' . $this->file_path);
    }

    /**
     * Cek apakah file masih ada di storage
     */
    public function getExistsAttribute()
    {
        return file_exists(storage_path('app/public/' . $this->file_path));
    }
}
