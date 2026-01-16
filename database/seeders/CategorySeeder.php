<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Электроника',
                'slug' => 'electronic',
                'description' => 'Смартфоны, ноутбуки, планшеты и другая электроника',
            ],
            [
                'name' => 'Одежда',
                'slug' => 'clothing',
                'description' => 'Мужская, женская и детская одежда',
            ],
            [
                'name' => 'Обувь',
                'slug' => 'shoes',
                'description' => 'Кроссовки, туфли, ботинки и другая обувь',
            ],
            [
                'name' => 'Книги',
                'slug' => 'books',
                'description' => 'Художественная и учебная литература',
            ],
            [
                'name' => 'Дом и сад',
                'slug' => 'garden',
                'description' => 'Товары для дома и садоводства',
            ],
            [
                'name' => 'Красота и здоровье',
                'slug' => 'health',
                'description' => 'Косметика, парфюмерия и товары для здоровья',
            ],
            [
                'name' => 'Спорт и отдых',
                'slug' => 'sport',
                'description' => 'Спортивное оборудование и товары для отдыха',
            ],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

    }
}
