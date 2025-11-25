<?php

namespace Database\Factories;

use App\Models\BackupHost;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackupHostFactory extends Factory
{
    protected $model = BackupHost::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'driver' => 'wings',
            'config' => null,
            'use_path_style_endpoint' => true,
        ];
    }
}
