<?php

namespace App\Extensions\Backups;

use App\Extensions\Filesystem\S3Filesystem;
use App\Models\BackupHost;
use Aws\S3\S3Client;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Webmozart\Assert\Assert;

class BackupManager
{
    /**
     * The array of resolved backup drivers.
     *
     * @var array<string, FilesystemAdapter>
     */
    protected array $adapters = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, callable>
     */
    protected array $customCreators;

    public function __construct(protected Application $app) {}

    /**
     * Returns a backup adapter instance for a specific backup configuration.
     */
    public function adapter(BackupHost $backupHost): FilesystemAdapter
    {
        $driver = $backupHost->driver;

        $config = [
            'adapter' => $driver === 's3' ? 's3' : 'wings',
        ];

        if ($backupHost->config) {
            $hostConfig = $backupHost->config;

            $config = array_merge($config, $hostConfig);
        }


        $adapterName = "backup_config_{$backupHost->id}_{$driver}";

        return $this->adapters[$adapterName] ??= $this->createAdapter($config);
    }

    /**
     * Create an adapter from config.
     *
     * @param  array<string, mixed>  $config
     */
    protected function createAdapter(array $config): FilesystemAdapter
    {
        $adapter = $config['adapter'];

        $adapterMethod = 'create' . Str::studly($adapter) . 'Adapter';
        $instance = $this->{$adapterMethod}($config);
        Assert::isInstanceOf($instance, FilesystemAdapter::class);

        return $instance;
    }

    /**
     * Set the given backup adapter instance.
     */
    public function set(string $name, FilesystemAdapter $disk): self
    {
        $this->adapters[$name] = $disk;

        return $this;
    }

    /**
     * Creates a new daemon adapter.
     *
     * @param  array<string, mixed>  $config
     */
    public function createWingsAdapter(array $config): FilesystemAdapter
    {
        return new InMemoryFilesystemAdapter();
    }

    /**
     * Creates a new S3 adapter.
     *
     * @param  array<string, mixed>  $config
     */
    public function createS3Adapter(array $config): FilesystemAdapter
    {
        $config['version'] = 'latest';

        if (!empty($config['key']) && !empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }
        $client = new S3Client($config);

        return new S3Filesystem($client, $config['bucket'], $config['prefix'] ?? '', $config['options'] ?? []);
    }

    /**
     * Unset the given adapter instances.
     *
     * @param  string|string[]  $adapter
     */
    public function forget(array|string $adapter): self
    {
        $adapters = &$this->adapters;
        foreach ((array) $adapter as $adapterName) {
            unset($adapters[$adapterName]);
        }

        return $this;
    }

    /**
     * Register a custom adapter creator closure.
     */
    public function extend(string $adapter, Closure $callback): self
    {
        $this->customCreators[$adapter] = $callback;

        return $this;
    }
}
