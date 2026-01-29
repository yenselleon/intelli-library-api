<?php

namespace App\Exports\Contracts;

interface ExportableInterface
{
    public function collection();

    public function headings(): array;

    public function map($row): array;
}
