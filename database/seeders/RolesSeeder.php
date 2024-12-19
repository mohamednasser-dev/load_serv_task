<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Gift;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Room;
use App\Models\Sponser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create Roles
        $adminRole = Role::create([
            'name' => 'super_admin',
            'display_name_ar' => 'الادمن الرئيسي',
            'display_name_en' => 'Super Admin',
        ]);

        // assign permissions to role
        $adminRole->givePermissions(Permission::all());

        // create Roles
        $employeeRole = Role::create([
            'name' => 'employee_role',
            'display_name_ar' => 'الموظفيين',
            'display_name_en' => 'Employees',
        ]);

        // assign permissions to role
        $employeeRole->givePermissions([
            'invoices_list',
            'invoices_update',
            'invoices_change_status'
        ]);
    }
}
