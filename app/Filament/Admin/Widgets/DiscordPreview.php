<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Server;
use App\Models\WebhookConfiguration;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class DiscordPreview extends Widget
{
    protected static string $view = 'filament.admin.widgets.discord-preview';

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'lg';

    public WebhookConfiguration $record;

    public function getViewData(): array
    {
        $payload = $this->record->payload;
        $content = data_get($payload, 'content');
        $username = data_get($payload, 'sender_username');
        $avatar = data_get($payload, 'avatar_url');
        $embeds = data_get($payload, 'embeds', []);
        $sender = $this->easterEgg($username);

        $data = array_merge(Server::factory()->definition(), [
            'id' => random_int(1, 100),
            'event' => $this->record->transformClassName(collect($this->record->events)->random()),
        ]);

        if ($content) {
            $content = $this->record->replaceVars($data, $content);
        }

        foreach ($embeds as &$embed) {
            $embed['description'] = $this->record->replaceVars($data, data_get($embed, 'description'));

            if ($fields = data_get($embed, 'fields')) {
                $embed['fields'] = Arr::map($fields, fn ($field) => [
                    'name' => $this->record->replaceVars($data, data_get($field, 'name')),
                    'value' => $this->record->replaceVars($data, data_get($field, 'value')),
                    'inline' => data_get($field, 'inline'),
                ]);
            }

            if (data_get($embed, 'has_timestamp')) {
                $embed['timestamp'] = $this->record->getTime();
            }
        }

        return [
            'content' => $content,
            'username' => $username,
            'sender' => $sender,
            'avatar' => $avatar,
            'embeds' => $embeds,
            'getTime' => $this->record->getTime(),
            'actions' => [
                Action::make('preview')
                    ->label('Preview')
                    ->disabled(fn () => !$content && !$embeds)
                    ->modalContent(fn () => $this->getIframe())
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->action(fn () => null),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function easterEgg(?string $author): array
    {
        // If this is approved, add the other pelican contributors.
        return match ($author) {
            'JoanFo' => [
                'name' => $author,
                'avatar' => 'https://cdn.discordapp.com/avatars/668228483796959272/fa232a470776f48fc9aa53d5a8a6a074.png',
                'decoration' => 'https://cdn.discordapp.com/avatar-decoration-presets/a_af5ee420e5f860ff2cdbb5fa4633f2cf.png?size=96&amp;amp;passthrough=false',
                'human' => true,
            ],
            'Lance' => [
                'name' => $author,
                'avatar' => 'https://cdn.discordapp.com/avatars/108350949411532800/5c0366c62ccb4263734f9decebf4944d.png',
                'decoration' => 'https://cdn.discordapp.com/avatar-decoration-presets/a_b3d5743ff7a2cda95d28fd984f82a5f8.png?size=96&amp;amp;passthrough=false',
                'human' => true,
            ],
            'notCharles' => [
                'name' => $author,
                'avatar' => 'https://cdn.discordapp.com/avatars/168955129830178816/d6de49de0ff5f3f3338c8cad510825cf.png',
                'decoration' => null,
                'human' => true,
            ],
            default => [
                'name' => 'Pelican',
                'avatar' => $this->sender['avatar_url'] ?? 'https://cdn.discordapp.com/avatars/1222179499253170307/d4d6873acc8a0d5fb5eaa5aa81572cf3.png',
                'decoration' => null,
                'human' => false,
            ]
        };
    }

    public function getIframe(): HtmlString
    {
        $src = route('preview', ['record' => $this->record]);

        return new HtmlString(<<<HTML
        <iframe
            src="$src"
            style="width: 100%; height: 75vh">
        </iframe>
        HTML);
    }
}
