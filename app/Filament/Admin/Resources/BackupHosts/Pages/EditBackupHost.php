<?php

namespace App\Filament\Admin\Resources\BackupHosts\Pages;

use App\Filament\Admin\Resources\BackupHosts\BackupHostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBackupHost extends EditRecord
{
    protected static string $resource = BackupHostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCancelFormAction()->formId('form'),
            DeleteAction::make(),
        ];
    }
}
