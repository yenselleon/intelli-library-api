<?php

namespace App\Exports;

use App\Exports\Contracts\ExportableInterface;
use InvalidArgumentException;

class ExportFactory
{
    protected $exports = [
        'authors' => AuthorsExport::class,
        'books' => BooksExport::class,
    ];

    public function make(string $entity): ExportableInterface
    {
        if (!isset($this->exports[$entity])) {
            throw new InvalidArgumentException("Export type '{$entity}' is not supported.");
        }

        return app($this->exports[$entity]);
    }

    public function getAvailableEntities(): array
    {
        return array_keys($this->exports);
    }
}
