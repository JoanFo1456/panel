<?php

namespace App\Jobs;

use App\Enums\WebhookType;
use App\Models\ServerWebhook;
use App\Models\ServerWebhookExecution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ProcessServerWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private ServerWebhook $serverWebhook,
        private string $eventName,
        private array $eventData
    ) {}

    public function handle(): void
    {
        $data = $this->eventData[0] ?? [];
        if (count($data) === 1) {
            $data = reset($data);
        }

        $data = Arr::wrap($data);
        $data['event'] = $this->serverWebhook->transformClassName($this->eventName);

        if ($this->serverWebhook->type === WebhookType::Discord) {
            $payload = json_encode($this->serverWebhook->payload);
            $tmp = $this->serverWebhook->replaceVars($data, $payload);
            $data = json_decode($tmp, true);

            $embeds = data_get($data, 'embeds');
            if ($embeds) {
                foreach ($embeds as &$embed) {
                    if (data_get($embed, 'has_timestamp')) {
                        $embed['timestamp'] = Carbon::now();
                        unset($embed['has_timestamp']);
                    }
                }
                $data['embeds'] = $embeds;
            }
        } else {
            $data = $this->buildPayload();
        }
        
        $webhookExecution = ServerWebhookExecution::create([
            'server_webhook_configuration_id' => $this->serverWebhook->id,
            'server_short_id' => $this->serverWebhook->server_id,
            'event' => $this->eventName,
            'endpoint' => $this->serverWebhook->endpoint,
            'payload' => $data,
        ]);

        try {
            $customHeaders = $this->serverWebhook->headers ?? [];
            $headers = [];
            foreach ($customHeaders as $key => $value) {
                $headers[$key] = $this->serverWebhook->replaceVars($data, $value);
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->serverWebhook->endpoint, $data);

            if ($response->successful()) {
                $webhookExecution->update(['successful_at' => now()]);
            }
        } catch (\Exception $e) {
            logger()->error('Server webhook failed', [
                'webhook_id' => $this->serverWebhook->id,
                'event' => $this->eventName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildPayload(): array
    {
        $data = $this->eventData[0] ?? [];
        
        if ($this->serverWebhook->payload) {
            $payload = $this->serverWebhook->payload;
            
            $jsonPayload = json_encode($payload);
            $replacedJson = $this->serverWebhook->replaceVars($data, $jsonPayload);
            return json_decode($replacedJson, true) ?? $payload;
        }

        return array_merge(['event' => $this->eventName], $data);
    }
}