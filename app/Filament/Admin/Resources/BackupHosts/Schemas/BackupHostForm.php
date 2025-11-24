<?php

namespace App\Filament\Admin\Resources\BackupHosts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BackupHostForm
{
    public static function configure(Schema $schema): Schema
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
                            ->label(trans('admin/backup.backup_driver'))
                            ->options([
                                'wings' => 'Wings',
                                's3' => 'S3',
                            ])
                            ->default('wings')
                            ->inline()
                            ->required()
                            ->live(),
                        Grid::make(2)
                            ->visible(fn ($get) => $get('driver') === 's3')
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
                                ToggleButtons::make('use_path_style_endpoint')
                                    ->label(trans('admin/backup.use_path_style_endpoint'))
                                    ->options([
                                        true => trans('admin/backup.yes'),
                                        false => trans('admin/backup.no'),
                                    ])
                                    ->default(false)
                                    ->inline(),
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
}
