<?php

namespace App\Listeners;

use App\Events\ActivityLogged;
use App\Models\ServerWebhook;
use App\Services\ServerWebhookService;

class DispatchServerWebhooks
{
    public function handle(ActivityLogged $event): void
    {
        if (!$event->isServerEvent()) {
            return;
        }

        $server = $event->model->subject;
        if (!$server) {
            return;
        }

        $eventName = $event->model->event;
        $serverWebhooks = ServerWebhook::query()
            ->where('server_id', $server->id)
            ->whereJsonContains('events', $eventName)
            ->get();

        if ($serverWebhooks->isEmpty()) {
            return;
        }

        $eventData = [
            'event' => $eventName,
            'server_id' => $server->id,
            'server_uuid' => $server->uuid,
            'server_name' => $server->name,
            'user_id' => $event->model->actor_id,
            'user_email' => $event->model->actor?->email,
            'timestamp' => $event->model->timestamp->toISOString(),
        ];

        if ($event->model->properties) {
            $eventData = array_merge($eventData, $event->model->properties);
        }

        ServerWebhookService::dispatch($eventName, $eventData, $server);
    }
}