<?php

use App\Author;
use Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{
    public function run()
    {
        Author::create([
            'name' => 'Robert C.',
            'surname' => 'Martin',
        ]);

        Author::create([
            'name' => 'Martin',
            'surname' => 'Fowler',
        ]);

        Author::create([
            'name' => 'Eric',
            'surname' => 'Evans',
        ]);

        Author::create([
            'name' => 'Kent',
            'surname' => 'Beck',
        ]);
    }
}
