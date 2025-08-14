<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\Permission\RoleConstant;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => RoleConstant::ADMIN]);
        Role::create(['name' => RoleConstant::CUSTOMER]);
//        $user = User::query()->find(250812073823861192);
//        $user->assignRole(RoleConstant::ADMIN);
//        $user2 = User::query()->find(250814074315577120);
//        $user->assignRole(RoleConstant::CUSTOMER);

    }
}
