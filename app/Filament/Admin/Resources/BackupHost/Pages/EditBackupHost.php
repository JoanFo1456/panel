<?php

namespace App\Filament\Admin\Resources\BackupHost\Pages;

use App\Filament\Admin\Resources\BackupHost\BackupHostResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBackupHost extends EditRecord
{
    protected static string $resource = BackupHostResource::class;

    protected function getDefaultHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->authorize('backup_host.delete')
                ->disabled(fn ($record) => $record->backups->count() > 0)
                ->label(fn ($record) => $record->backups->count() > 0 ? trans('admin/backup.has_records') : trans('filament-actions::delete.single.modal.actions.delete.label')),
            Action::make('save')
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getDefaultFormActions(): array
    {
        return [];
    }
}
