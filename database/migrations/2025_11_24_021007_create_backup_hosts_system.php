<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create backup_hosts table
        Schema::create('backup_hosts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver')->default('wings');
            $table->json('config')->nullable();
            $table->boolean('use_path_style_endpoint')->default(true);
            $table->timestamps();

            $table->unique('name');
        });

        // Create backup_host_node pivot table
        Schema::create('backup_host_node', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_host_id')->constrained('backup_hosts')->onDelete('cascade');
            $table->foreignId('node_id')->constrained('nodes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['backup_host_id', 'node_id']);
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->foreignId('backup_host_id')->nullable()->constrained('backup_hosts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['backup_host_id']);
            $table->dropColumn('backup_host_id');
        });

        Schema::dropIfExists('backup_host_node');

        Schema::dropIfExists('backup_hosts');
    }
};
