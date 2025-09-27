<?php

namespace App\Filament\Widgets;

use App\Models\WebhookConfiguration;
use App\Services\WebhookService;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class DiscordPreview extends Widget
{
    protected string $view = 'filament.admin.widgets.discord-preview';

    /** @var array<string, string> */
    protected $listeners = [
        'refresh-widget' => '$refresh',
    ];

    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 1;

    public ?WebhookConfiguration $record = null;

    /** @var string|array<string, mixed>|null */
    public string|array|null $payload = null;

    /**
     * @return array{
     *     link: callable,
     *     content: mixed,
     *     sender: array{name: string, avatar: string},
     *     embeds: array<int, mixed>,
     *     getTime: mixed
     * }
     */
    public function getViewData(): array
    {
        if (!$this->record || !$this->record->payload) {
            return [
                'link' => fn ($href, $child) => $href ? "<a href=\"$href\" target=\"_blank\" class=\"link\">$child</a>" : $child,
                'content' => null,
                'sender' => [
                    'name' => 'Webhook',
                    'avatar' => 'https://cdn.discordapp.com/embed/avatars/0.png',
                ],
                'embeds' => [],
                'getTime' => fn () => now()->format('Y-m-d H:i'),
            ];
        }

        $this->payload = json_encode($this->record->payload);
        
        $sampleData = $this->record->scope === \App\Enums\WebhookScope::SERVER 
            ? WebhookService::getServerWebhookSampleData()
            : $this->record->sampleWebhookData();

        $replacedPayload = $this->record->replaceVars($sampleData, $this->payload);
        $data = json_decode($replacedPayload, true);

        return [
            'link' => fn ($href, $child) => $href ? "<a href=\"$href\" target=\"_blank\" class=\"link\">$child</a>" : $child,
            'content' => data_get($data, 'content'),
            'sender' => [
                'name' => data_get($data, 'username', 'Webhook'),
                'avatar' => data_get($data, 'avatar_url', 'https://cdn.discordapp.com/embed/avatars/0.png'),
            ],
            'embeds' => collect(data_get($data, 'embeds', []))
                ->take(10)
                ->map(function (array $embed): array {
                    $embed['color'] = $embed['color'] ?? null;

                    if ($embed['color']) {
                        $embed['color'] = '#' . str_pad(dechex($embed['color']), 6, '0', STR_PAD_LEFT);
                    }

                    if (isset($embed['timestamp'])) {
                        $embed['timestamp'] = Carbon::parse($embed['timestamp'])->format('Y-m-d H:i');
                    }

                    return $embed;
                })
                ->all(),
            'getTime' => fn () => now()->format('Y-m-d H:i'),
        ];
    }
}