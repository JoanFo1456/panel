<?php

namespace App\Extensions\Features\Schemas;

use App\Extensions\Features\FeatureSchemaInterface;
use App\Models\Server;
use App\Models\User;
use Filament\Actions\Action;

/**
 * Marks an egg as running in native Windows containers. It only labels the egg, so it has
 * no console listeners and is never offered as an action on the server console.
 */
class WindowsSchema implements FeatureSchemaInterface
{
    /** @return array<string> */
    public function getListeners(): array
    {
        return [];
    }

    public function getId(): string
    {
        return 'windows';
    }

    public function authorize(User $user, Server $server): bool
    {
        return false;
    }

    public function getAction(): Action
    {
        return Action::make($this->getId());
    }
}
