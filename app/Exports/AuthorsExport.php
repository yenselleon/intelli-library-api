<?php

namespace App\Exports;

use App\Author;
use App\Exports\Contracts\ExportableInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuthorsExport implements ExportableInterface, FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Author::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Surname',
            'Full Name',
            'Books Count',
            'Created At',
        ];
    }

    public function map($author): array
    {
        return [
            $author->id,
            $author->name,
            $author->surname,
            $author->full_name,
            $author->books_count,
            $author->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
