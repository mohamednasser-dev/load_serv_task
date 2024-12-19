<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Sponser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '0123456',
            'password' => '123456',
            'type' => 'admin',
        ]);

        $superAdmin->addRole(Role::where('name','super_admin')->first());

        $employee = Admin::create([
            'name' => 'employee',
            'email' => 'employee@gmail.com',
            'phone' => '01094641332',
            'password' => '123456',
            'type' => 'employee',
        ]);

        $employee->addRole(Role::where('name','employee_role')->first());

         Customer::create([
            'name' => 'mohamed nasser',
            'email' => 'customer@gmail.com',
            'phone' => '01094641332',
        ]);
    }
}
