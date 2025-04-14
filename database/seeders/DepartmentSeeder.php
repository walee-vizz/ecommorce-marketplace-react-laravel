<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'meta_title' => 'Electronics',
                'meta_description' => 'Electronics Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'meta_title' => 'Fashion',
                'meta_description' => 'Fashion Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'meta_title' => 'Home & Garden',
                'meta_description' => 'Home & Garden Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'meta_title' => 'Sports',
                'meta_description' => 'Sports Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'meta_title' => 'Automotive',
                'meta_description' => 'Automotive Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'meta_title' => 'Health',
                'meta_description' => 'Health Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Grocery',
                'slug' => 'grocery',
                'meta_title' => 'Grocery',
                'meta_description' => 'Grocery Department',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        $inserted = Department::insert($departments);
    }
}
