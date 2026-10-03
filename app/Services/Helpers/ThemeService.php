<?php

namespace App\Services\Helpers;

use App\Contracts\Plugins\HasTheme;
use App\Enums\CustomizationKey;
use App\Models\Plugin;
use App\Traits\EnvironmentWriterTrait;
use Filament\Facades\Filament;
use Filament\Panel;

class ThemeService
{
    use EnvironmentWriterTrait;

    public const Default = 'default';

    public const None = 'none';

    /** @return array<string, string> */
    public function getThemes(): array
    {
        return Plugin::query()
            ->themes()
            ->get()
            ->filter(fn (Plugin $plugin) => $this->isTheme($plugin->id))
            ->mapWithKeys(fn (Plugin $plugin) => [$plugin->id => $plugin->name])
            ->all();
    }

    /** @return array<string, string> */
    public function getThemeOptions(): array
    {
        return [self::None => trans('profile.no_theme')] + $this->getThemes();
    }

    /** @return array<string, string> */
    public function getUserThemeOptions(): array
    {
        return [self::Default => trans('profile.default_theme')] + $this->getThemeOptions();
    }

    public function getActiveThemeId(): ?string
    {
        $themeId = user()?->getCustomization(CustomizationKey::Theme) ?? self::Default;

        if ($themeId === self::Default) {
            $themeId = config('panel.filament.default-theme');
        }

        return is_string($themeId) && $this->isTheme($themeId) ? $themeId : null;
    }

    public function getSelectedOption(): string
    {
        $themeId = user()?->getCustomization(CustomizationKey::Theme);

        return is_string($themeId) && array_key_exists($themeId, $this->getUserThemeOptions()) ? $themeId : self::Default;
    }

    public function isActive(string $themeId): bool
    {
        return $this->getActiveThemeId() === $themeId;
    }

    public function setDefaultTheme(string $themeId): void
    {
        $this->writeToEnvironment(['FILAMENT_DEFAULT_THEME' => $themeId]);
    }

    private function isTheme(string $themeId): bool
    {
        $panel = $this->getPanel();

        return $panel->hasPlugin($themeId) && $panel->getPlugin($themeId) instanceof HasTheme;
    }

    private function getPanel(): Panel
    {
        return Filament::getCurrentOrDefaultPanel();
    }
}
