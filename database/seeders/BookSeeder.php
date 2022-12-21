<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Book::query()->create([
            'name' => 'Buku Untuk Dibaca',
            'id_author' => 1,
            'id_category' => 1,
            'book_language' => 'Indonesia',
            'total_pages' => 300,
            'sinopsis' => 'Ini merupakan sinopsis dari buku berjudul Buku Untuk Dibaca yang ditulis Oleh Fulan',
            'type' => 'Non-Fantasy',
            'publisher' => 'PT. Ini Publisher',
            'published_at' => '2021-12-02',
            'isbn' => '9827178201283',
            'photo' => 'http://ini-photo.id/buku-untuk-dibaca',
            'description' => 'Ini adalah deskripsi dari buka berjudul Buku Untuk Dibaca yang ditulis oleh Fulan'
        ]);
    }
}
