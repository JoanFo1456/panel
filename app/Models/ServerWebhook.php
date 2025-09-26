<?php

namespace App\Models;

use App\Enums\WebhookType;
use App\Jobs\ProcessServerWebhook;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Livewire\Features\SupportEvents\HandlesEvents;

/**
 * @property string|array<string, mixed>|null $payload
 * @property string $endpoint
 * @property string $description
 * @property string[] $events
 * @property WebhookType|string|null $type
 * @property int $server_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property array<string, string>|null $headers
 */
class ServerWebhook extends Model
{
    use HandlesEvents, HasFactory, SoftDeletes;

    protected $table = 'server_webhook_configurations';

    protected $fillable = [
        'server_id',
        'type',
        'payload',
        'endpoint',
        'description',
        'events',
        'headers',
    ];


    protected $attributes = [
        'type' => WebhookType::Regular,
        'payload' => null,
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'payload' => 'array',
            'type' => WebhookType::class,
            'headers' => 'array',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function serverWebhookExecutions(): HasMany
    {
        return $this->hasMany(ServerWebhookExecution::class, 'server_webhook_configuration_id');
    }

    /** @return string[] */
    public static function allPossibleServerEvents(): array
    {
        $events = [
            'server:file.read',
            'server:file.write',
            'server:file.rename',
            'server:file.copy',
            'server:file.compress',
            'server:file.decompress',
            'server:file.delete',
            'server:file.create-directory',
            'server:file.uploaded',
            'server:file.pull',
            'server:file.download',

            'server:power.start',
            'server:power.stop',
            'server:power.restart',
            'server:power.kill',

            'server:console.command',

            'server:startup.edit',
            'server:startup.image',
            'server:settings.rename',
            'server:settings.description',
            'server:settings.reinstall',

            'server:allocation.notes',
            'server:allocation.primary',
            'server:allocation.create',
            'server:allocation.delete',

            'server:schedule.create',
            'server:schedule.update',
            'server:schedule.execute',
            'server:schedule.delete',

            'server:task.create',
            'server:task.update',
            'server:task.delete',

            'server:backup.start',
            'server:backup.delete',
            'server:backup.download',
            'server:backup.rename',
            'server:backup.restore',
            'server:backup.restore-complete',
            'server:backup.restore-failed',

            'server:database.create',
            'server:database.rotate-password',
            'server:database.delete',

            'server:subuser.create',
            'server:subuser.update',
            'server:subuser.delete',

            'server:sftp.denied',
        ];

        Event::dispatch('server:webhook.events', [&$events]);

        return array_unique($events);
    }

    /** @return array<string, string> */
    public static function filamentCheckboxList(): array
    {
        $list = [];
        $events = static::allPossibleServerEvents();
        foreach ($events as $event) {
            $list[$event] = static::transformEventName($event);
        }

        return $list;
    }

    public static function transformEventName(string $event): string
    {
        return str($event)
            ->after('server:')
            ->replace('.', ' → ')
            ->title()
            ->toString();
    }

    /**
     * @param array<mixed, mixed> $replacement
     */
    public function replaceVars(array $replacement, string $subject): string
    {
        return preg_replace_callback(
            '/{{(.*?)}}/',
            function ($matches) use ($replacement) {
                $trimmed = trim($matches[1]);
                return data_get($replacement, $trimmed, $trimmed);
            },
            $subject
        );
    }

    /** @param array<mixed, mixed> $eventData */
    public function run(?string $eventName = null, ?array $eventData = null): void
    {
        $eventName ??= 'server:file.write';
        $eventData ??= static::getServerWebhookSampleData();

        ProcessServerWebhook::dispatch($this, $eventName, [$eventData]);
    }

    public function transformClassName(string $event): string
    {
        return str($event)
            ->after('eloquent.')
            ->replace('App\\Models\\', '')
            ->replace('App\\Events\\', 'event: ')
            ->toString();
    }

    /**
     * @return array<string, mixed>
     */
    public static function getServerWebhookSampleData(): array
    {
        return [
            'event' => 'server:file.write',
            'server_id' => 1,
            'server_uuid' => '12345678-1234-1234-1234-123456789012',
            'server_name' => 'Example Server',
            'user_id' => 1,
            'user_email' => 'user@example.com',
            'file' => '/server/config/server.properties',
            'timestamp' => now()->toISOString(),
        ];
    }
}