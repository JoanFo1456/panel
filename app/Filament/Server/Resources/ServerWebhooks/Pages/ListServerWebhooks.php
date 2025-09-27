<?php

namespace App\Filament\Server\Resources\ServerWebhooks\Pages;

use App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource;
use App\Traits\Filament\CanCustomizeHeaderActions;
use App\Traits\Filament\CanCustomizeHeaderWidgets;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListServerWebhooks extends ListRecords
{
    use CanCustomizeHeaderActions;
    use CanCustomizeHeaderWidgets;

    protected static string $resource = ServerWebhookResource::class;

    /** @return array<Action|ActionGroup> */
    protected function getDefaultHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->hidden(function () {
                    /** @var \App\Models\Server $server */
                    $server = Filament::getTenant();

                    return $server->serverWebhooks()->count() <= 0;
                }),
        ];
    }
}
