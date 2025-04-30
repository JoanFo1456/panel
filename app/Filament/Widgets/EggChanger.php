<?php

namespace App\Filament\Widgets;

use Filament\Forms\Form;
use Filament\Widgets\Widget;
use App\Models\Egg;
use App\Models\Server;
use App\Services\EggChangerService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
class EggChanger extends Widget
{
    protected static string $view = 'filament.components.eggchanger';

    public function getFormSchema($server): array
    {
        return [
            Select::make('image')
                ->label('Docker Image')
                ->live()
                ->visible(fn (Server $server) => in_array($server['egg_id'], $server['egg_name']))
                ->disabled(fn () => !auth()->user()->can(Permission::ACTION_STARTUP_DOCKER_IMAGE, $server))
                ->afterStateUpdated(function ($state, Server $server) {
                    $original = $server->image;
                    $server->forceFill(['image' => $state])->saveOrFail();

                    if ($original !== $server->image) {
                        Activity::event('server:startup.image')
                            ->property(['old' => $original, 'new' => $state])
                            ->log();
                    }

                    Notification::make()
                        ->title('Docker image updated')
                        ->body('Restart the server to use the new image.')
                        ->success()
                        ->send();
                })
                ->options(function (Server $server) {
                    $images = $server->egg->docker_images;

                    return array_flip($images);
                })
                ->selectablePlaceholder(false)
                ->columnSpan([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                ]),
            Select::make('egg_id')
                ->disabled()
                ->prefixIcon('tabler-egg')
                ->columnSpan([
                    'default' => 6,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 4,
                ])
                ->relationship('egg', 'name')
                ->label(trans('admin/server.name'))
                ->searchable()
                ->preload()
                ->required()
                ->hintAction(
                    Action::make('change_egg')
                        ->label(trans('admin/server.change_egg'))
                        ->action(function (array $data, Server $server, EggChangerService $service) {
                            $service->handle($server, $data['egg_id'], $data['keepOldVariables']);

                            // Use redirect instead of fillForm to prevent server variables from duplicating
                            $this->redirect($this->getUrl(['record' => $server, 'tab' => '-egg-tab']), true);
                        })
                        ->form(fn (Server $server) => [
                            Select::make('egg_id')
                                ->label(trans('admin/server.new_egg'))
                                ->prefixIcon('tabler-egg')
                                ->options(fn () => Egg::all()->filter(fn (Egg $egg) => $egg->id !== $server->egg->id)->mapWithKeys(fn (Egg $egg) => [$egg->id => $egg->name]))
                                ->searchable()
                                ->preload(),
                            Toggle::make('keepOldVariables')
                                ->label(trans('admin/server.keep_old_variables'))
                                ->default(true),
                        ])
                )
                ->columnSpan([
                    'default' => 6,
                    'sm' => 3,
                    'md' => 3,
                    'lg' => 4,
                ]),
        ];
    }
}