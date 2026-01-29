<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookDeleted
{
    use Dispatchable, SerializesModels;

    public $authorId;

    public function __construct($authorId)
    {
        $this->authorId = $authorId;
    }
}
