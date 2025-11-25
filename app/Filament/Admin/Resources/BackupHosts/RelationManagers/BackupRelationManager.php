<?php

namespace App\Filament\Admin\Resources\BackupHosts\RelationManagers;

use App\Enums\BackupStatus;
use App\Models\Backup;
use App\Services\Backups\DownloadLinkService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\Request;

class BackupRelationManager extends RelationManager
{
    protected static string $relationship = 'backups';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('server.name')
                    ->label('Server'),
                TextColumn::make('bytes')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024 / 1024, 2) . ' MB'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                IconColumn::make('is_locked')
                    ->boolean()
                    ->label('Locked'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->color('primary')
                    ->icon('tabler-download')
                    ->url(fn (DownloadLinkService $downloadLinkService, Backup $backup, Request $request) => $downloadLinkService->handle($backup, $request->user()), true)
                    ->visible(fn (Backup $backup) => $backup->status === BackupStatus::Successful),
                DeleteAction::make(),
            ])
            ->headerActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
