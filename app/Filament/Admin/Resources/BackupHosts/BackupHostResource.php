<?php

namespace App\Filament\Admin\Resources\BackupHosts;

use App\Filament\Admin\Resources\BackupHosts\Pages\CreateBackupHost;
use App\Filament\Admin\Resources\BackupHosts\Pages\EditBackupHost;
use App\Filament\Admin\Resources\BackupHosts\Pages\ListBackupHosts;
use App\Filament\Admin\Resources\BackupHosts\Pages\ViewBackupHost;
use App\Filament\Admin\Resources\BackupHosts\Schemas\BackupHostForm;
use App\Filament\Admin\Resources\BackupHosts\Schemas\BackupHostInfolist;
use App\Filament\Admin\Resources\BackupHosts\Tables\BackupHostsTable;
use App\Models\BackupHost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BackupHostResource extends Resource
{
    protected static ?string $model = BackupHost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return trans('admin/backup.nav_title');
    }

    public static function form(Schema $schema): Schema
    {
        return BackupHostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BackupHostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BackupHostsTable::configure($table);
    }
    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBackupHosts::route('/'),
            'create' => CreateBackupHost::route('/create'),
            'edit' => EditBackupHost::route('/{record}/edit'),
        ];
    }
}
