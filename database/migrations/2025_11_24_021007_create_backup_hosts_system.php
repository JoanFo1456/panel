<?php

use App\Models\Backup;
use App\Models\BackupHost;
use App\Models\Node;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backup_hosts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver')->default('wings');
            $table->json('config')->nullable();
            $table->boolean('use_path_style_endpoint')->default(true);
            $table->boolean('use_accelerate_endpoint')->default(false);
            $table->timestamps();

            $table->unique('name');
        });

        Schema::create('backup_host_node', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_host_id')->constrained('backup_hosts')->onDelete('cascade');
            $table->foreignId('node_id')->constrained('nodes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['backup_host_id', 'node_id']);
        });

        Schema::table('backups', function (Blueprint $table) {
            $table->foreignId('backup_host_id')->nullable()->after('disk')->constrained('backup_hosts')->onDelete('cascade');
        });

        $this->seedBackupHosts();

        $this->migrateExistingBackups();

        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn('disk');
        });

        Schema::table('backups', function (Blueprint $table) {
            $table->foreignId('backup_host_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->string('disk')->after('backup_host_id');
        });

        $this->reverseMigrateBackups();

        Schema::table('backups', function (Blueprint $table) {
            $table->dropForeign(['backup_host_id']);
            $table->dropColumn('backup_host_id');
        });

        Schema::dropIfExists('backup_host_node');
        Schema::dropIfExists('backup_hosts');
    }

    private function seedBackupHosts(): void
    {
        $nodes = Node::all();

        $wingsHost = BackupHost::create([
            'name' => 'Wings',
            'driver' => 'wings',
            'config' => null,
            'use_path_style_endpoint' => true,
        ]);

        foreach ($nodes as $node) {
            $wingsHost->nodes()->attach($node->id);
        }

        $s3Config = config('backups.disks.s3');
        if (!empty($s3Config['bucket'])) {
            $s3Host = BackupHost::create([
                'name' => 'S3 Backups',
                'driver' => 's3',
                'config' => [
                    'region' => $s3Config['region'],
                    'key' => $s3Config['key'],
                    'secret' => $s3Config['secret'],
                    'bucket' => $s3Config['bucket'],
                    'prefix' => $s3Config['prefix'],
                    'endpoint' => $s3Config['endpoint'],
                    'use_path_style_endpoint' => $s3Config['use_path_style_endpoint'] ?? false,
                    'use_accelerate_endpoint' => $s3Config['use_accelerate_endpoint'],
                    'storage_class' => $s3Config['storage_class'],
                ],
            ]);

            foreach ($nodes as $node) {
                $s3Host->nodes()->attach($node->id);
            }
        }
    }

    private function migrateExistingBackups(): void
    {
        $wingsHost = BackupHost::where('driver', 'wings')->first();
        $s3Host = BackupHost::where('driver', 's3')->first();

        Backup::with('server.node')->chunk(100, function ($backups) use ($wingsHost, $s3Host) {
            foreach ($backups as $backup) {
                $hostId = null;
                if ($backup->backupHost->driver === 'wings' && $wingsHost) {
                    $hostId = $wingsHost->id;
                } elseif ($backup->backupHost->driver === 's3' && $s3Host) {
                    $hostId = $s3Host->id;
                }

                if ($hostId) {
                    $backup->update(['backup_host_id' => $hostId]);
                }
            }
        });
    }

    private function reverseMigrateBackups(): void
    {
        Backup::with('backupHost')->chunk(100, function ($backups) {
            foreach ($backups as $backup) {
                $disk = $backup->backupHost->driver;
                DB::table('backups')->where('id', $backup->id)->update(['disk' => $disk]);
            }
        });
    }
};
