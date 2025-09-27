<?php

namespace App\Listeners;

use App\Enums\WebhookScope;
use App\Events\ActivityLogged;
use App\Models\Server;
use App\Models\WebhookConfiguration;
use App\Services\WebhookService;

class DispatchWebhooks
{
    /**
     * Handle both eloquent events and activity logged events.
     */
    public function handle(string|ActivityLogged $event, array $data = []): void
    {
        if ($event instanceof ActivityLogged) {
            $this->handleActivityLogged($event);
        } else {
            $this->handleEloquentEvent($event, $data);
        }
    }

    /**
     * Handle ActivityLogged events for server webhooks.
     */
    protected function handleActivityLogged(ActivityLogged $activityLogged): void
    {
        $eventName = $activityLogged->model->event;
        
        if (!$activityLogged->isServerEvent()) {
            return;
        }

        // Get the server from the activity log
        $server = null;
        if ($activityLogged->model->subject_type === Server::class) {
            $server = $activityLogged->model->subject;
        } elseif (isset($activityLogged->model->properties['server'])) {
            $server = Server::find($activityLogged->model->properties['server']['id'] ?? null);
        }

        if (!$server) {
            return;
        }

        // Get server webhooks that listen for this event
        $serverWebhooks = $server->serverWebhooks()
            ->whereJsonContains('events', $eventName)
            ->get();

        foreach ($serverWebhooks as $webhook) {
            WebhookService::dispatch($eventName, $activityLogged->model->properties?->toArray() ?? [], $server);
            break; // WebhookService::dispatch handles all webhooks for the event
        }
    }

    /**
     * Handle eloquent events for global webhooks.
     */
    protected function handleEloquentEvent(string $eventName, array $data): void
    {
        if (!$this->eventIsWatched($eventName)) {
            return;
        }

        $matchingHooks = cache()->rememberForever("webhooks.$eventName", function () use ($eventName) {
            return WebhookConfiguration::query()
                ->where('scope', WebhookScope::GLOBAL)
                ->whereJsonContains('events', $eventName)
                ->get();
        });

        /** @var WebhookConfiguration $webhookConfig */
        foreach ($matchingHooks as $webhookConfig) {
            if (in_array($eventName, $webhookConfig->events)) {
                $webhookConfig->run($eventName, $data);
            }
        }
    }

    protected function eventIsWatched(string $eventName): bool
    {
        $watchedEvents = cache()->rememberForever('watchedWebhooks', function () {
            return WebhookConfiguration::where('scope', WebhookScope::GLOBAL)
                ->pluck('events')
                ->flatten()
                ->unique()
                ->values()
                ->all();
        });

        return in_array($eventName, $watchedEvents);
    }
}
