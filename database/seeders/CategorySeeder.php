<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'department_id' => 1,
                'parent_id' => null,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Computers',
                'slug' => 'computers',
                'department_id' => 1,
                'parent_id' => 1,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smartphone',
                'slug' => 'smartphone',
                'department_id' => 1,
                'parent_id' => 1,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Desktops',
                'slug' => 'desktops',
                'department_id' => 1,
                'parent_id' => 2,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptops',
                'slug' => 'laptops',
                'department_id' => 1,
                'parent_id' => 2,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Android',
                'slug' => 'android',
                'department_id' => 1,
                'parent_id' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'iOS',
                'slug' => 'ios',
                'department_id' => 1,
                'parent_id' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ];

        Category::insert($categories);
    }
}
