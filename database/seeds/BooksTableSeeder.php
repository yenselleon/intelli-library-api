<?php

use App\Book;
use App\Author;
use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
{
    public function run()
    {
        $robertMartin = Author::where('surname', 'Martin')->first();
        $martinFowler = Author::where('name', 'Martin')->where('surname', 'Fowler')->first();
        $ericEvans = Author::where('surname', 'Evans')->first();
        $kentBeck = Author::where('surname', 'Beck')->first();

        if ($robertMartin) {
            Book::create([
                'title' => 'Clean Code',
                'description' => 'A Handbook of Agile Software Craftsmanship',
                'author_id' => $robertMartin->id,
                'published_year' => 2008,
            ]);

            Book::create([
                'title' => 'Clean Architecture',
                'description' => 'A Craftsman\'s Guide to Software Structure and Design',
                'author_id' => $robertMartin->id,
                'published_year' => 2017,
            ]);
        }

        if ($martinFowler) {
            Book::create([
                'title' => 'Refactoring',
                'description' => 'Improving the Design of Existing Code',
                'author_id' => $martinFowler->id,
                'published_year' => 1999,
            ]);

            Book::create([
                'title' => 'Patterns of Enterprise Application Architecture',
                'description' => 'The practice of enterprise application development',
                'author_id' => $martinFowler->id,
                'published_year' => 2002,
            ]);
        }

        if ($ericEvans) {
            Book::create([
                'title' => 'Domain-Driven Design',
                'description' => 'Tackling Complexity in the Heart of Software',
                'author_id' => $ericEvans->id,
                'published_year' => 2003,
            ]);
        }

        if ($kentBeck) {
            Book::create([
                'title' => 'Test Driven Development',
                'description' => 'By Example',
                'author_id' => $kentBeck->id,
                'published_year' => 2002,
            ]);
        }

        $this->syncAuthorBookCounts();
    }

    private function syncAuthorBookCounts()
    {
        Author::all()->each(function ($author) {
            $author->update(['books_count' => $author->books()->count()]);
        });
    }
}
