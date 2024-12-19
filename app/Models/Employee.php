<?php

namespace App\Models;

use App\Enums\AdminTypesEnum;

class Employee extends Admin
{
    protected $table = 'admins';

    // Boot method for model events
//    protected static function boot()
//    {
//        parent::boot();
//        // Adding a "created" event listener
//        static::creating(function ($user) {
//            // Check if the 'type' attribute is 'employee'
//            $user->type = AdminTypesEnum::EMP;
//        });
//    }
}
