<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    protected $fillable = [
        'title',
        'service_id', // ✅ ganti dari category
        'description',
        'image',
        'status',
    ];

    /**
     * Relasi ke Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
