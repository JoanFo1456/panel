<?php

namespace App\Livewire;

use App\Enums\CustomizationKey;
use App\Services\Helpers\ThemeService;
use Illuminate\View\View;
use Livewire\Component;

final class ThemeSwitcher extends Component
{
    private ThemeService $themeService;

    public function boot(ThemeService $themeService): void
    {
        $this->themeService = $themeService;
    }

    public function save(string $theme): void
    {
        if (!array_key_exists($theme, $this->themeService->getThemeOptions())) {
            return;
        }

        user()?->setCustomization(CustomizationKey::Theme, $theme);

        $this->js('window.location.reload()');
    }

    public function render(): View
    {
        return view('livewire.theme-switcher', [
            'themes' => $this->themeService->getThemeOptions(),
            'selected' => $this->themeService->getSelectedThemeId(user()),
        ]);
    }
}
