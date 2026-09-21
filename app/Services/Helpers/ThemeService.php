<?php

namespace App\Services\Helpers;

use App\Enums\CustomizationKey;
use App\Filament\Themes\ThemedPanel;
use App\Models\User;
use Filament\Facades\Filament;

class ThemeService
{
    /** @return array<string, string> */
    public function getThemes(): array
    {
        return $this->getPanel()?->getThemes() ?? [];
    }

    public function getActiveThemeId(): ?string
    {
        return $this->getPanel()?->getActiveThemeId();
    }

    /** @return array<string, string> */
    public function getThemeOptions(): array
    {
        return [ThemedPanel::None => trans('profile.default_theme')] + $this->getThemes();
    }

    public function getSelectedThemeId(?User $user): string
    {
        $preference = $user?->getCustomization(CustomizationKey::Theme);

        if (is_string($preference) && array_key_exists($preference, $this->getThemes())) {
            return $preference;
        }

        return $this->getActiveThemeId() ?? ThemedPanel::None;
    }

    private function getPanel(): ?ThemedPanel
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        return $panel instanceof ThemedPanel ? $panel : null;
    }
}
