<?php

namespace App\Filament\Admin\Resources\BackupHosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BackupHostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('admin/backup.name'))
                    ->searchable(),
                TextColumn::make('driver')
                    ->label(trans('admin/backup.driver'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wings' => 'Wings',
                        's3' => 'S3',
                        default => $state,
                    }),
                TextColumn::make('nodes.name')
                    ->badge()
                    ->label(trans('admin/backup.linked_nodes'))
                    ->placeholder(trans('admin/backup.no_nodes')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
