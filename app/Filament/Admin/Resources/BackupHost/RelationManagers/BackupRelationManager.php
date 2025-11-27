<?php

namespace App\Filament\Admin\Resources\BackupHost\RelationManagers;

use App\Enums\BackupStatus;
use App\Models\Backup;
use App\Services\Backups\DownloadLinkService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\Request;

class BackupRelationManager extends RelationManager
{
    protected static string $relationship = 'backups';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(trans('admin/backup.name')),
                TextColumn::make('server.name')
                    ->label(trans('admin/backup.server')),
                TextColumn::make('bytes')
                    ->label(trans('admin/backup.size'))
                    ->formatStateUsing(fn ($state) => convert_bytes_to_readable($state)),
                TextColumn::make('created_at')
                    ->label(trans('admin/backup.created_at'))
                    ->dateTime(),
                TextColumn::make('status')
                    ->label(trans('admin/backup.status'))
                    ->badge(),
                IconColumn::make('is_locked')
                    ->boolean()
                    ->label(trans('admin/backup.locked')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label(trans('admin/backup.download'))
                    ->color('primary')
                    ->icon('tabler-download')
                    ->authorize('backup.download')
                    ->url(fn (DownloadLinkService $downloadLinkService, Backup $backup, Request $request) => $downloadLinkService->handle($backup, $request->user()), true)
                    ->visible(fn (Backup $backup) => $backup->status === BackupStatus::Successful),
                DeleteAction::make()
                    ->authorize('backup.delete'),
            ])
            ->headerActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize('backup.delete'),
                ]),
            ]);
    }
}
