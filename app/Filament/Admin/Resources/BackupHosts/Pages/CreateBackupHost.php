<?php

namespace App\Filament\Admin\Resources\BackupHosts\Pages;

use App\Filament\Admin\Resources\BackupHosts\BackupHostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBackupHost extends CreateRecord
{
    protected static string $resource = BackupHostResource::class;

    protected static bool $canCreateAnother = false;
}
