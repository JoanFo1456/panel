<?php

namespace App\Services;

use App\Enums\WebhookScope;
use App\Jobs\ProcessWebhook;
use App\Models\Server;
use App\Models\WebhookConfiguration;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    /**
     * Dispatch webhooks for a specific event.
     */
    public static function dispatch(string $eventName, array $contextualData, ?Server $server = null): void
    {
        // Handle server-scoped webhooks
        if ($server) {
            $serverWebhooks = $server->serverWebhooks()
                ->whereJsonContains('events', $eventName)
                ->get();

            foreach ($serverWebhooks as $webhook) {
                ProcessWebhook::dispatch($webhook, $eventName, $contextualData);
            }
        }

        // Handle global webhooks
        $globalWebhooks = WebhookConfiguration::query()
            ->where('scope', WebhookScope::GLOBAL)
            ->whereJsonContains('events', $eventName)
            ->get();

        foreach ($globalWebhooks as $webhook) {
            ProcessWebhook::dispatch($webhook, $eventName, $contextualData);
        }
    }

    /**
     * Get all possible events for a given scope.
     */
    public static function getAllEvents(WebhookScope $scope = WebhookScope::GLOBAL): array
    {
        return WebhookConfiguration::filamentCheckboxList($scope);
    }

    /**
     * Get sample data for testing server webhooks.
     */
    public static function getServerWebhookSampleData(): array
    {
        return [
            'user' => [
                'uuid' => '12345678-1234-5678-9012-123456789012',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'image' => 'https://www.gravatar.com/avatar/default',
                'admin' => true,
                'language' => 'en',
                'created_at' => '2025-06-01T12:31:50.000000Z',
                'updated_at' => '2025-06-01T12:31:50.000000Z',
            ],
            'server' => [
                'uuid' => '87654321-4321-8765-2109-876543210987',
                'name' => 'Example Server',
                'node' => 'node1.example.com',
                'description' => 'Sample Minecraft server',
            ],
        ];
    }
}