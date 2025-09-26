<?php

namespace App\Services;

use App\Jobs\ProcessServerWebhook;
use App\Models\Server;
use App\Models\ServerWebhook;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ServerWebhookService
{
    /**
     * Dispatch webhooks for a server event
     *
     * @param string $eventName
     * @param array $eventData
     * @param Server|null $server
     */
    public static function dispatch(string $eventName, array $eventData = [], ?Server $server = null): void
    {
        if (!$server) {
            /** @var Server|null $server */
            $server = Filament::getTenant();
        }

        if (!$server) {
            return;
        }

        $serverWebhooks = ServerWebhook::query()
            ->where('server_id', $server->id)
            ->whereJsonContains('events', $eventName)
            ->get();

        $contextualData = array_merge($eventData, [
            'server_id' => $server->id,
            'server_uuid' => $server->uuid,
            'server_name' => $server->name,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'timestamp' => now()->toISOString(),
        ]);

        foreach ($serverWebhooks as $serverWebhook) {
            ProcessServerWebhook::dispatch($serverWebhook, $eventName, [$contextualData]);
        }
    }

    /**
     * Get available server events for webhook configuration
     *
     * @return array<string, string>
     */
    public static function getAvailableEvents(): array
    {
        return ServerWebhook::filamentCheckboxList();
    }
}