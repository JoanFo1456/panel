<?php

namespace App\Filament\Server\Resources\ServerWebhooks\Pages;

use App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServerWebhook extends ViewRecord
{
    protected static string $resource = ServerWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
