<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('users')->get()->each(function ($user) {
            $customization = json_decode($user->customization, true);
                $customization['navigation_type'] = $customization['top_navigation'] ? 'top' : 'side';
                DB::table('users')->where('id', $user->id)->update([
                    'customization' => json_encode($customization)
                ]);
            });
    }

    public function down(): void
    {
        // No action needed for down migration
    }
};
