<?php

namespace App\Filament\Themes;

use App\Enums\CustomizationKey;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Vite;

class ThemedPanel extends Panel
{
    public const None = 'none';

    /** @var array<string, string> */
    protected array $themeNames = [];

    /** @var array<string, array<array<string, array<int|string, string|int>|string>|Closure>> */
    protected array $themeColors = [];

    /** @var array<string, array{family: string|Closure|null, url: string|Closure|null, provider: string|Closure|null, preload: array<string>|Closure|null}> */
    protected array $themeFonts = [];

    /** @var array<string, array{0: string|array<string>, 1: string|null}> */
    protected array $themeViteThemes = [];

    protected ?string $scopedThemeId = null;

    public function __construct(
        protected Container $container,
        protected Vite $vite,
    ) {}

    public function themePlugin(string $themeId, string $name, Plugin $plugin): static
    {
        $this->themeNames[$themeId] = $name;

        return $this->plugin(new ScopedThemePlugin($plugin, $this, $themeId));
    }

    public function scopeToTheme(string $themeId, Closure $callback): void
    {
        $previousThemeId = $this->scopedThemeId;
        $this->scopedThemeId = $themeId;

        try {
            $callback();
        } finally {
            $this->scopedThemeId = $previousThemeId;
        }
    }

    /** @return array<string, string> */
    public function getThemes(): array
    {
        return $this->themeNames;
    }

    public function getActiveThemeId(): ?string
    {
        $candidates = [
            user()?->getCustomization(CustomizationKey::Theme),
            config('panel.filament.default-theme'),
        ];

        foreach ($candidates as $candidate) {
            if (!is_string($candidate) || $candidate === '') {
                continue;
            }

            if ($candidate === self::None) {
                return null;
            }

            if (array_key_exists($candidate, $this->themeNames)) {
                return $candidate;
            }
        }

        return null;
    }

    public function boot(): void
    {
        parent::boot();

        FilamentColor::register(fn (): array => $this->getActiveThemeColors());
    }

    /** @param array<string, array<int|string, string|int>|string>|Closure $colors */
    public function colors(array|Closure $colors): static
    {
        if ($this->scopedThemeId === null) {
            return parent::colors($colors);
        }

        $this->themeColors[$this->scopedThemeId][] = $colors;

        return $this;
    }

    /** @param array<string>|Closure|null $preload */
    public function font(string|Closure|null $family, string|Closure|null $url = null, string|Closure|null $provider = null, array|Closure|null $preload = null): static
    {
        if ($this->scopedThemeId === null) {
            return parent::font($family, $url, $provider, $preload);
        }

        $this->themeFonts[$this->scopedThemeId] = [
            'family' => $family,
            'url' => $url,
            'provider' => $provider,
            'preload' => $preload,
        ];

        return $this;
    }

    public function getFontFamily(): string
    {
        $family = $this->evaluate($this->getActiveThemeFont()['family'] ?? null);

        return is_string($family) && $family !== '' ? $family : parent::getFontFamily();
    }

    public function hasCustomFontFamily(): bool
    {
        return ($this->getActiveThemeFont()['family'] ?? null) !== null || parent::hasCustomFontFamily();
    }

    public function getFontProvider(): string
    {
        $provider = $this->evaluate($this->getActiveThemeFont()['provider'] ?? null);

        return is_string($provider) && $provider !== '' ? $provider : parent::getFontProvider();
    }

    public function getFontUrl(): ?string
    {
        $url = $this->evaluate($this->getActiveThemeFont()['url'] ?? null);

        return is_string($url) && $url !== '' ? $url : parent::getFontUrl();
    }

    /** @return array<string> */
    public function getFontPreload(): array
    {
        $preload = $this->evaluate($this->getActiveThemeFont()['preload'] ?? null);

        return is_array($preload) ? $preload : parent::getFontPreload();
    }

    /** @param string|array<string> $theme */
    public function viteTheme(string|array $theme, ?string $buildDirectory = null): static
    {
        if ($this->scopedThemeId === null) {
            return parent::viteTheme($theme, $buildDirectory);
        }

        $this->themeViteThemes[$this->scopedThemeId] = [$theme, $buildDirectory];

        return $this;
    }

    /** @return string|array<string>|null */
    public function getViteTheme(): string|array|null
    {
        return $this->getActiveThemeViteTheme()[0] ?? parent::getViteTheme();
    }

    public function getTheme(): Theme
    {
        $activeThemeViteTheme = $this->getActiveThemeViteTheme();

        if ($activeThemeViteTheme === null) {
            return parent::getTheme();
        }

        [$viteTheme, $buildDirectory] = $activeThemeViteTheme;

        return Theme::make('app')->html(($this->vite)($viteTheme, $buildDirectory));
    }

    /** @param string|array<string>|null $scopes */
    public function renderHook(string $name, Closure $hook, string|array|null $scopes = null): static
    {
        if ($this->scopedThemeId !== null) {
            $hook = $this->scopeHookToTheme($hook, $this->scopedThemeId);
        }

        return parent::renderHook($name, $hook, $scopes);
    }

    protected function scopeHookToTheme(Closure $hook, string $themeId): Closure
    {
        return function (array $data, array $scopes) use ($hook, $themeId): string {
            if ($this->getActiveThemeId() !== $themeId) {
                return '';
            }

            return (string) $this->container->call($hook, ['data' => $data, 'scopes' => $scopes]);
        };
    }

    /** @return array<string, array<int|string, string|int>|string> */
    protected function getActiveThemeColors(): array
    {
        $activeThemeId = $this->getActiveThemeId();

        if ($activeThemeId === null) {
            return [];
        }

        $colors = [];

        foreach ($this->themeColors[$activeThemeId] ?? [] as $set) {
            foreach ($this->evaluate($set) as $name => $color) {
                $colors[$name] = $color;
            }
        }

        return $colors;
    }

    /** @return array{family: string|Closure|null, url: string|Closure|null, provider: string|Closure|null, preload: array<string>|Closure|null}|null */
    protected function getActiveThemeFont(): ?array
    {
        $activeThemeId = $this->getActiveThemeId();

        return $activeThemeId === null ? null : ($this->themeFonts[$activeThemeId] ?? null);
    }

    /** @return array{0: string|array<string>, 1: string|null}|null */
    protected function getActiveThemeViteTheme(): ?array
    {
        $activeThemeId = $this->getActiveThemeId();

        return $activeThemeId === null ? null : ($this->themeViteThemes[$activeThemeId] ?? null);
    }
}
