<?php

namespace Database\Seeders;

use App\Models\Access\Menu;
use App\Models\Access\Role;
use App\Models\Access\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $roles = ['Administrator', 'User'];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name]);
        }

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mail.com',
            'password' => Hash::make('12345678'),
            'role_id' => Role::where('name', 'Administrator')->first()->id,
        ]);


        Menu::create([
            'name' => 'Dashboard',
            'route' => '/',
            'roles' => ['Administrator'],
            'icon' => 'HomeOutlined',
            'order' => 0,
        ]);


        $masterDataId = Menu::create([
            'name' => 'Setting',
            'route' => null,
            'roles' => ['Administrator'],
            'icon' => 'SettingOutlined',
            'order' => 1,
        ]);

        Menu::create([
            'name' => 'User Management',
            'route' => '/setting/users',
            'roles' => ['Administrator'],
            'parent_id' => $masterDataId->id,
            'order' => 1,
        ]);

        Menu::create([
            'name' => 'Role Management',
            'route' => '/setting/roles',
            'roles' => ['Administrator'],
            'parent_id' => $masterDataId->id,
            'order' => 2,
        ]);
        Menu::create([
            'name' => 'Menu Management',
            'route' => '/setting/menus',
            'roles' => ['Administrator'],
            'parent_id' => $masterDataId->id,
            'order' => 3,
        ]);
    }
}
