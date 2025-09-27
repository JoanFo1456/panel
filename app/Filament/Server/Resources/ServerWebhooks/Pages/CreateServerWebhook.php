<?php

namespace App\Filament\Server\Resources\ServerWebhooks\Pages;

use App\Enums\WebhookType;
use App\Filament\Server\Resources\ServerWebhooks\ServerWebhookResource;
use App\Traits\Filament\CanCustomizeHeaderActions;
use App\Traits\Filament\CanCustomizeHeaderWidgets;
use App\Enums\WebhookScope;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateServerWebhook extends CreateRecord
{
    use CanCustomizeHeaderActions;
    use CanCustomizeHeaderWidgets;

    protected static string $resource = ServerWebhookResource::class;

    protected static bool $canCreateAnother = false;

    /** @return array<Action|ActionGroup> */
    protected function getDefaultHeaderActions(): array
    {
        return [
            $this->getCancelFormAction()->formId('form'),
            $this->getCreateFormAction()->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $server = Filament::getTenant();
        $data['server_id'] = $server->id;
        $data['scope'] = WebhookScope::SERVER;
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return EditServerWebhook::getUrl(['record' => $this->getRecord()]);
    }

    public function mount(): void
    {
        parent::mount();
        ServerWebhookResource::sendHelpBanner();
    }
}
