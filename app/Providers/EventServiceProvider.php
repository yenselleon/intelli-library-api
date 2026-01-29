<?php

namespace App\Providers;

use App\Events\BookAuthorChanged;
use App\Events\BookCreated;
use App\Events\BookDeleted;
use App\Listeners\RecalculateAuthorBookCount;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BookCreated::class => [
            RecalculateAuthorBookCount::class,
        ],
        BookDeleted::class => [
            RecalculateAuthorBookCount::class,
        ],
        BookAuthorChanged::class => [
            RecalculateAuthorBookCount::class,
        ],
    ];

    public function boot()
    {
        parent::boot();

        //
    }
}
