<?php

namespace App\Filament\Server\Resources\ServerWebhooks\Pages;

use App\Enums\WebhookType;
use App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource;
use App\Models\WebhookConfiguration;
use App\Traits\Filament\CanCustomizeHeaderActions;
use App\Traits\Filament\CanCustomizeHeaderWidgets;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServerWebhook extends EditRecord
{
    use CanCustomizeHeaderActions;
    use CanCustomizeHeaderWidgets;

    protected static string $resource = ServerWebhookResource::class;

    /** @return array<Action|ActionGroup> */
    protected function getDefaultHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('test_now')
                ->label(trans('admin/webhook.test_now'))
                ->color('primary')
                ->action(fn (WebhookConfiguration $record) => $record->run())
                ->tooltip(trans('admin/webhook.test_now_help')),
            $this->getSaveFormAction()->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
