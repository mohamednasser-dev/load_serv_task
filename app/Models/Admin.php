<?php

namespace App\Models;

use App\Enums\AdminTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements LaratrustUser, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions , CausesActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'type',
        'is_active'
    ];

    const TYPE = ['admin', 'employee'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//        // Adding a "created" event listener
//        static::creating(function ($user) {
//            // Check if the 'type' attribute is 'employee'
//            $user->type = AdminTypesEnum::ADMIN;
//        });
//    }

    public function scopeActive($query)
    {
        $query->where('is_active', 1);
    }

    // Password Mutator: Automatically hashes the password when setting it
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
