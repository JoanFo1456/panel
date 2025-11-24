<?php

namespace App\Filament\Admin\Resources\BackupHosts\Pages;

use App\Filament\Admin\Resources\BackupHosts\BackupHostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBackupHosts extends ListRecords
{
    protected static string $resource = BackupHostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
