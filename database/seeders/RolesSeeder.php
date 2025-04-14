<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => RolesEnum::Admin->value]);
        $venderRole = Role::create(['name' => RolesEnum::Vendor->value]);
        $userRole = Role::create(['name' => RolesEnum::User->value]);

        $approveVenders = Permission::create(['name' => PermissionsEnum::ApproveVendors->value]);
        $sellProducts = Permission::create(['name' => PermissionsEnum::SellProducts->value]);
        $buyProducts = Permission::create(['name' => PermissionsEnum::BuyProducts->value]);


        $adminRole->syncPermissions($approveVenders, $sellProducts, $buyProducts);
        $venderRole->syncPermissions($sellProducts, $buyProducts);
        $userRole->syncPermissions($buyProducts);
    }
}
