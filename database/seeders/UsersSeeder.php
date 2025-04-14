<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Enums\VendorStatusEnum;
use App\Models\User;
use App\Models\Vendor;
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

        $vendor = Vendor::create([
            'user_id' => $venderUser->id,
            'status' => VendorStatusEnum::APPROVED,
            'store_name' => 'Vender Store',
            'store_address' => 'Vender Store Address',
        ]);

        $user = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'user@mail.com',
        ]);
        $user->assignRole(RolesEnum::User->value);
    }
}
