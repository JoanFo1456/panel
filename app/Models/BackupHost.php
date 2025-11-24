<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    protected $fillable = [
        'name',
        'driver',
        'config',
        'use_path_style_endpoint',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'use_path_style_endpoint' => 'boolean',
        ];
    }

    public function nodes(): BelongsToMany
    {
        return $this->belongsToMany(Node::class);
    }
}
