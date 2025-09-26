<?php

namespace App\Providers;

use App\Events\ActivityLogged;
use App\Listeners\DispatchWebhooks;
use App\Listeners\DispatchServerWebhooks;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        'App\\*' => [DispatchWebhooks::class],
        'eloquent.created*' => [DispatchWebhooks::class],
        'eloquent.deleted*' => [DispatchWebhooks::class],
        'eloquent.updated*' => [DispatchWebhooks::class],
        ActivityLogged::class => [DispatchServerWebhooks::class],
    ];
}
