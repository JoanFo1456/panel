<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $event
 * @property string $endpoint
 * @property int $server_short_id
 * @property \Illuminate\Support\Carbon|null $successful_at
 * @property array<array-key, mixed> $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ServerWebhookExecution extends Model
{
    use HasFactory, MassPrunable;

    protected $table = 'server_webhooks';

    protected $fillable = [
        'server_webhook_configuration_id',
        'server_short_id',
        'payload',
        'successful_at',
        'event',
        'endpoint'
    ];

    public function casts()
    {
        return [
            'payload' => 'array',
            'successful_at' => 'datetime',
        ];
    }

    public function serverWebhook(): BelongsTo
    {
        return $this->belongsTo(ServerWebhook::class, 'server_webhook_configuration_id');
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', Carbon::now()->subDays(config('panel.webhook.prune_days', 30)));
    }
}