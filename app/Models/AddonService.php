<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AddonService extends Model
{
    use HasFactory;

    /**
     * Nama tabel pivot
     */
    protected $table = 'addon_service';

    /**
     * Pivot biasanya tidak perlu fillable ketat
     */
    protected $guarded = [];

    /**
     * Timestamps ada di tabel
     */
    public $timestamps = true;

    /* =======================
     |  RELATIONS
     =======================*/

    /**
     * Relasi ke master addon
     * addon_service.addon_id -> addons.id
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /**
     * Relasi ke service
     * addon_service.service_id -> services.id
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
