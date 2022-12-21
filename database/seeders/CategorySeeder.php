<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::query()->create([
            'name' => 'Ensiklopedia',
            'logo' => 'http://ini-logo.id/ensiklopedia',
            'description' => 'Ini adalah deskripsi dari kategori Ensklopedia'
        ]);
    }
}
