<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookAuthorChanged
{
    use Dispatchable, SerializesModels;

    public $oldAuthorId;

    public $newAuthorId;

    public function __construct($oldAuthorId, $newAuthorId)
    {
        $this->oldAuthorId = $oldAuthorId;
        $this->newAuthorId = $newAuthorId;
    }
}
