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
     *
     * @param array<string, mixed> $data
     */
    public function handle(string|ActivityLogged $event, array $data = []): void
    {
        if ($event instanceof ActivityLogged) {
            $this->handleActivityLogged($event);
        } else {
            $this->handleEloquentEvent($event, $data);
        }
    }

    protected function handleActivityLogged(ActivityLogged $activityLogged): void
    {
        $eventName = $activityLogged->model->event;

        if (!$activityLogged->isServerEvent()) {
            return;
        }

        $server = null;
        $firstSubject = $activityLogged->model->subjects->first();
        if ($firstSubject && $firstSubject->subject_type === Server::class) {
            $server = $firstSubject->subject;
        } elseif (isset($activityLogged->model->properties['server'])) {
            $server = Server::find($activityLogged->model->properties['server']['id'] ?? null);
        }

        if (!$server instanceof Server) {
            return;
        }

        $serverWebhooks = $server->serverWebhooks()
            ->whereJsonContains('events', $eventName)
            ->get();

        foreach ($serverWebhooks as $webhook) {
            WebhookService::dispatch($eventName, $activityLogged->model->properties?->toArray() ?? [], $server);
            break; 
        }
    }

    /**
     *
     * @param array<string, mixed> $data
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
