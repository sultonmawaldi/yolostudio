<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Relasi ke kategori layanan
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke appointment (booking)
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relasi ke employee melalui pivot employee_service
     * - duration: durasi sesi per service
     * - break_duration: waktu istirahat/persiapan
     * - slot_duration: opsional jika tiap service punya slot berbeda
     */
    public function employees()
{
    return $this->belongsToMany(Employee::class)
        ->withPivot('duration', 'break_duration')
        ->withTimestamps();
}

}
