<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main categories
        $mainCategories = [
            ['name' => 'Electronics'],
            ['name' => 'Books'],
            ['name' => 'Clothing'],
        ];

        foreach ($mainCategories as $main) {
            Category::create($main);
        }

        // Create subcategories
        $electronics = Category::where('name', 'Electronics')->first();
        $books = Category::where('name', 'Books')->first();
        $clothing = Category::where('name', 'Clothing')->first();

        $subCategories = [];
        if ($electronics && isset($electronics->id)) {
            $subCategories[] = ['name' => 'Mobile Phones', 'parent_id' => $electronics->getKey()];
            $subCategories[] = ['name' => 'Laptops', 'parent_id' => $electronics->getKey()];
        }
        if ($books && isset($books->id)) {
            $subCategories[] = ['name' => 'Fiction', 'parent_id' => $books->getKey()];
            $subCategories[] = ['name' => 'Non-Fiction', 'parent_id' => $books->getKey()];
        }
        if ($clothing && isset($clothing->id)) {
            $subCategories[] = ['name' => 'Men\'s Clothing', 'parent_id' => $clothing->getKey()];
            $subCategories[] = ['name' => 'Women\'s Clothing', 'parent_id' => $clothing->getKey()];
        }

        foreach ($subCategories as $sub) {
            Category::create($sub);
        }
    }
}
