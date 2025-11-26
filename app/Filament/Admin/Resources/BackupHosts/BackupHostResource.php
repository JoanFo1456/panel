<?php

namespace App\Filament\Admin\Resources\BackupHosts;

use App\Filament\Admin\Resources\BackupHosts\Pages\CreateBackupHost;
use App\Filament\Admin\Resources\BackupHosts\Pages\EditBackupHost;
use App\Filament\Admin\Resources\BackupHosts\Pages\ListBackupHosts;
use App\Filament\Admin\Resources\BackupHosts\RelationManagers\BackupRelationManager;
use App\Filament\Admin\Resources\BackupHosts\Schemas\BackupHostForm;
use App\Filament\Admin\Resources\BackupHosts\Tables\BackupHostsTable;
use App\Models\BackupHost;
use App\Traits\Filament\CanCustomizePages;
use App\Traits\Filament\CanCustomizeRelations;
use App\Traits\Filament\CanModifyForm;
use App\Traits\Filament\CanModifyTable;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BackupHostResource extends Resource
{
    use CanCustomizePages;
    use CanCustomizeRelations;
    use CanModifyForm;
    use CanModifyTable;
    
    protected static ?string $model = BackupHost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return trans('admin/backup.nav_title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns([
                        'default' => 2,
                        'sm' => 3,
                        'md' => 3,
                        'lg' => 4,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label(trans('admin/backup.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2),
                        ToggleButtons::make('driver')
                            ->disabled(fn ($record) => $record !== null)
                            ->label(trans('admin/backup.backup_driver'))
                            ->options([
                                'wings' => 'Wings',
                                's3' => 'S3',
                            ])
                            ->default('wings')
                            ->inline()
                            ->required()
                            ->columnSpan(2)
                            ->live(),
                        Grid::make(2)
                            ->visible(fn ($get) => $get('driver') === 's3')
                            ->columns(4)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('config.bucket')
                                    ->label(trans('admin/backup.s3_bucket'))
                                    ->required(),
                                TextInput::make('config.region')
                                    ->label(trans('admin/backup.s3_region'))
                                    ->required(),
                                TextInput::make('config.key')
                                    ->label(trans('admin/backup.s3_key'))
                                    ->required(),
                                TextInput::make('config.secret')
                                    ->label(trans('admin/backup.s3_secret'))
                                    ->required()
                                    ->password(),
                                TextInput::make('config.endpoint')
                                    ->label(trans('admin/backup.s3_endpoint')),
                                TextInput::make('config.prefix')
                                    ->label(trans('admin/backup.s3_prefix')),
                                Toggle::make('config.use_path_style_endpoint')
                                    ->label(trans('admin/backup.use_path_style_endpoint'))
                                    ->default(true)
                                    ->dehydrateStateUsing(fn ($state) => $state ? 1 : 0),
                                Toggle::make('config.use_accelerate_endpoint')
                                    ->label(trans('admin/backup.use_accelerate_endpoint'))
                                    ->default(false)
                                    ->dehydrateStateUsing(fn ($state) => $state ? 1 : 0),

                            ]),
                        Select::make('node_ids')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label(trans('admin/backup.linked_nodes'))
                            ->relationship('nodes', 'name', fn (Builder $query) => $query->whereIn('nodes.id', user()?->accessibleNodes()->pluck('id')))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
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
            ])
            ->recordActions([
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize('backup_host.delete'),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
    }

    public static function getDefaultRelations(): array
    {
        return [
            BackupRelationManager::class,
        ];
    }

    public static function getDefaultPages(): array
    {
        return [
            'index' => ListBackupHosts::route('/'),
            'create' => CreateBackupHost::route('/create'),
            'edit' => EditBackupHost::route('/{record}/edit'),
        ];
    }
}
