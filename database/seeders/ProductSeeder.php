<?php

namespace Database\Seeders;

use App\Enums\ContentTypeEnum;
use App\Enums\MediaTypeEnum;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::pluck('id')->toArray();
        Product::factory(20)->create()->each(function ($product) use ($categories) {
            $randomCategories = collect($categories)->shuffle()->take(rand(1, 3))->toArray();
            $product->categories()->attach($randomCategories);
            $media = [
                new Media([
                    'path' => 'demo/test.webp',
                    'type' => MediaTypeEnum::FEATURED,
                    'content_type' => ContentTypeEnum::IMAGE,
                ]),
            ];

            $product->media()->saveMany($media);
        });
    }
}
