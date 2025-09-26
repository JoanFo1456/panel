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
        Schema::create('server_webhook_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('server_id');
            $table->string('endpoint');
            $table->string('description');
            $table->json('events');
            $table->string('type')->default('regular');
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('server_id');
        });

        Schema::create('server_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_webhook_configuration_id')->constrained('server_webhook_configurations');
            $table->unsignedInteger('server_short_id');
            $table->string('event');
            $table->string('endpoint');
            $table->timestamp('successful_at')->nullable();
            $table->json('payload');
            $table->timestamps();

            $table->index('server_short_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_webhooks');
        Schema::dropIfExists('server_webhook_configurations');
    }
};
