<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $driver
 * @property array<string, mixed>|null $config
 * @property bool $use_path_style_endpoint
 */
class BackupHost extends Model
{
    protected $table = 'backup_hosts';

    protected $attributes = [
        'use_path_style_endpoint' => true,
        'use_accelerate_endpoint' => false,
    ];

    protected $fillable = [
        'name',
        'driver',
        'config',
        'use_path_style_endpoint',
        'use_accelerate_endpoint',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'use_path_style_endpoint' => 'boolean',
            'use_accelerate_endpoint' => 'boolean',
        ];
    }

    public function nodes(): BelongsToMany
    {
        return $this->belongsToMany(Node::class, 'backup_host_node');
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    /** @return array<int, array<string, mixed>> */
    public function getBackupsList(): array
    {
        /** @var Collection<int, Backup> $backups */
        $backups = $this->backups()->with('server')->get();

        return $backups->map(
            /** @param Backup $backup */
            function ($backup) {
                return [
                    'name' => $backup->name,
                    'server' => $backup->server->name,
                    'size' => $backup->bytes,
                    'created' => $backup->created_at,
                    'status' => $backup->status,
                    'locked' => $backup->is_locked,
                ];
            }
        )->toArray();
    }
}
