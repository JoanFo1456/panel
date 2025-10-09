<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPasskeys\Support\Config;

return new class extends Migration
{
    public function up()
    {
        $authenticatableClass = Config::getAuthenticatableModel();
        $authenticatableTableName = (new $authenticatableClass)->getTable();

        Schema::create('passkeys', function (Blueprint $table) use ($authenticatableTableName) {
            $table->id();

            // match users.id (int unsigned)
            $table->unsignedInteger('authenticatable_id');
            $table->foreign('authenticatable_id', 'passkeys_authenticatable_fk')
                  ->references('id')
                  ->on($authenticatableTableName)
                  ->cascadeOnDelete();

            $table->text('name');
            $table->text('credential_id');
            $table->json('data');

            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }
};
