<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'name' => 'Waleed Khan',
            'email' => 'walee@mail.com',
        ]);

        $adminUser->assignRole(RolesEnum::Admin->value);

        $venderUser = User::factory()->create([
            'name' => 'Vender User',
            'email' => 'vendor@mail.com',
        ]);
        $venderUser->assignRole(RolesEnum::Vendor->value);

        $user = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'user@mail.com',
        ]);
        $user->assignRole(RolesEnum::User->value);
    }
}
