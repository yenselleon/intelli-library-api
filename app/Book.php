<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'description',
        'author_id',
        'published_year',
    ];

    protected $casts = [
        'published_year' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
