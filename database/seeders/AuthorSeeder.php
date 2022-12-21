<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Author::query()->create([
            'name' => 'Fulan',
            'gender' => 'Male',
            'birth_date' => '1999-12-21',
            'religion' => 'Islam',
            'ethnic' => 'Sunda',
            'citizenship' => 'Indonesia',
            'photo' => 'http://ini-url-photo.id/fulan',
            'hobbies' => 'Menulis',
            'description' => 'Ini deskripsi Fulan'
        ]);
    }
}
