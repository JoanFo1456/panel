<?php

namespace App\Filament\Themes;

use Filament\Contracts\Plugin;
use Filament\Panel;

final class ScopedThemePlugin implements Plugin
{
    public function __construct(
        private readonly Plugin $plugin,
        private readonly ThemedPanel $panel,
        private readonly string $themeId,
    ) {}

    public function getId(): string
    {
        return $this->plugin->getId();
    }

    public function register(Panel $panel): void
    {
        $this->panel->scopeToTheme($this->themeId, fn () => $this->plugin->register($panel));
    }

    public function boot(Panel $panel): void
    {
        $this->panel->scopeToTheme($this->themeId, fn () => $this->plugin->boot($panel));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}
