<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем конкретные товары для каждой категории
        $products = [
            // Электроника
            [
                'name' => 'iPhone 15',
                'price' => 99999.99,
                'category_id' => Category::where('slug', 'electronic')->first()->id,
                'in_stock' => true,
                'rating' => 4.8
            ],
            [
                'name' => 'MacBook',
                'price' => 124999.99,
                'category_id' => Category::where('slug', 'electronic')->first()->id,
                'in_stock' => true
            ],

            // Одежда
            [
                'name' => 'Джинсы ',
                'price' => 4999.99,
                'category_id' => Category::where('slug', 'clothing')->first()->id,
                'in_stock' => true,
            ],

            // Книги
            [
                'name' => 'Война и мир',
                'price' => 899.99,
                'category_id' => Category::where('slug', 'books')->first()->id,
                'in_stock' => true,
                'rating' => 4.7
            ],

            // Спорт
            [
                'name' => 'Беговая дорожка',
                'price' => 45999.99,
                'category_id' => Category::where('slug', 'sport')->first()->id,
                'in_stock' => false,
                'rating' => 4.3,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
