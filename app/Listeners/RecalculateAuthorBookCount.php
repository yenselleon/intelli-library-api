<?php

namespace App\Listeners;

use App\Author;
use App\Events\BookAuthorChanged;
use App\Events\BookCreated;
use App\Events\BookDeleted;

class RecalculateAuthorBookCount
{
    public function handle($event)
    {
        $authorIds = $this->getAuthorIds($event);

        foreach ($authorIds as $authorId) {
            $author = Author::find($authorId);
            if ($author) {
                $author->update(['books_count' => $author->books()->count()]);
            }
        }
    }

    private function getAuthorIds($event)
    {
        if ($event instanceof BookAuthorChanged) {
            return [$event->oldAuthorId, $event->newAuthorId];
        }

        return [$event->authorId];
    }
}
