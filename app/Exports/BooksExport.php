<?php

namespace App\Exports;

use App\Book;
use App\Exports\Contracts\ExportableInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BooksExport implements ExportableInterface, FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Book::with('author')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Author',
            'Description',
            'Published Year',
            'Created At',
        ];
    }

    public function map($book): array
    {
        return [
            $book->id,
            $book->title,
            $book->author ? $book->author->name . ' ' . $book->author->surname : 'N/A',
            $book->description,
            $book->published_year,
            $book->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
