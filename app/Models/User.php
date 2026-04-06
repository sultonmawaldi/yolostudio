<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ResetPasswordNotification;
use App\Models\Employee;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, AuthenticationLoggable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'image',
        'role_uid',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data employee (jika ada)
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Relasi many-to-many dengan services
     */
    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * Relasi ke appointments milik user
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Relasi ke transaksi milik user
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relasi ke kupon milik user
     */
    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'user_id');
    }

    /**
     * URL profil untuk AdminLTE
     */
    public function adminlte_profile_url()
    {
        return "/profile";
    }

    /**
     * Gambar user untuk AdminLTE
     */
    public function adminlte_image()
    {
        $userImage = Auth::user()->image;

        if ($userImage) {
            if (strpos($userImage, 'https://') === 0) {
                return $userImage;
            } else {
                return asset('uploads/images/profile/' . $userImage);
            }
        } else {
            return asset('vendor/adminlte/dist/img/gravtar.jpg');
        }
    }

    /**
     * Gambar profil user
     */
    public function profileImage()
    {
        // Cek apakah user punya gambar dan file ada di public/uploads/images/profile/
        if ($this->image && file_exists(public_path('uploads/images/profile/' . $this->image))) {
            return asset('uploads/images/profile/' . $this->image);
        }

        // Fallback default AdminLTE gravatar
        $defaultGravatar = public_path('vendor/adminlte/dist/img/gravatar.jpg');
        if (file_exists($defaultGravatar)) {
            return asset('vendor/adminlte/dist/img/gravatar.jpg');
        }

        // Fallback terakhir ke UI Avatar jika default AdminLTE tidak ada
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=fff&color=3b82f6';
    }

    /**
     * Gambar employee (bisa sama dengan profile)
     */
    public function employeeImage()
    {
        $userImage = $this->image;

        if (!empty($userImage)) {
            return asset('uploads/images/profile/' . $userImage);
        } else {
            return asset('vendor/adminlte/dist/img/gravtar.jpg');
        }
    }
    public function pointLogs()
    {
        return $this->hasMany(PointLog::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
