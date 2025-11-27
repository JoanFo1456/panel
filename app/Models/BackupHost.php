<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $driver
 * @property array<string, mixed>|null $config
 * @property bool $use_path_style_endpoint
 * @property bool $use_accelerate_endpoint
 */
class BackupHost extends Model
{
    protected $table = 'backup_hosts';

    protected $fillable = [
        'name',
        'driver',
        'config',
    ];
    public function getPermission(string $resource): int
    {
        return $this->permissions[$resource] ?? AdminAcl::NONE;
    }
    protected function casts(): array
    {
        return [
            'config' => 'array',
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
}
