<?php

namespace App\Tests\Unit\Filament;

use App\Enums\CustomizationKey;
use App\Filament\Themes\ThemedPanel;
use App\Models\User;
use App\Tests\TestCase;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Auth;

class ThemedPanelTest extends TestCase
{
    private function panel(): ThemedPanel
    {
        /** @var ThemedPanel $panel */
        $panel = ThemedPanel::make();

        return $panel->id('testing');
    }

    private function withThemes(ThemedPanel $panel): ThemedPanel
    {
        $panel->themePlugin('alpha', 'Alpha', new FakeThemePlugin(
            'alpha',
            ['primary' => Color::Rose, 'blurple' => Color::Purple],
            'Alpha Sans',
            'resources/css/themes/alpha.css',
        ));

        $panel->themePlugin('beta', 'Beta', new FakeThemePlugin(
            'beta',
            ['primary' => Color::Lime],
        ));

        return $panel;
    }

    private function actingAsUserWithTheme(?string $theme): User
    {
        $user = new User();
        $user->customization = $theme === null ? [] : [CustomizationKey::Theme->value => $theme];

        Auth::guard(config('auth.defaults.guard', 'web'))->setUser($user);

        return $user;
    }

    public function test_it_lists_every_registered_theme(): void
    {
        $panel = $this->withThemes($this->panel());

        $this->assertSame(['alpha' => 'Alpha', 'beta' => 'Beta'], $panel->getThemes());
    }

    public function test_it_falls_back_to_the_global_default_when_the_user_has_not_picked_one(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(null);

        $this->assertSame('beta', $panel->getActiveThemeId());
    }

    public function test_the_users_theme_wins_over_the_global_default(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme('alpha');

        $this->assertSame('alpha', $panel->getActiveThemeId());
    }

    public function test_a_user_can_opt_out_of_the_global_default(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(ThemedPanel::None);

        $this->assertNull($panel->getActiveThemeId());
    }

    public function test_an_uninstalled_theme_falls_through_to_the_global_default(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme('gone');

        $this->assertSame('beta', $panel->getActiveThemeId());
    }

    public function test_only_the_active_themes_colors_reach_filament(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(null);
        FilamentColor::register(['primary' => Color::Blue, 'gray' => Color::Zinc]);

        $panel->boot();

        $colors = FilamentColor::getColors();

        $this->assertSame(Color::Lime[500], $colors['primary'][500]);
        $this->assertArrayNotHasKey('blurple', $colors);
        $this->assertSame(Color::Zinc[500], $colors['gray'][500]);
    }

    public function test_the_panel_keeps_its_own_colors_when_no_theme_is_active(): void
    {
        config()->set('panel.filament.default-theme', ThemedPanel::None);

        $panel = $this->withThemes($this->panel())->colors(['primary' => Color::Blue]);
        $this->actingAsUserWithTheme(null);

        $panel->boot();

        $this->assertSame(Color::Blue[500], FilamentColor::getColors()['primary'][500]);
    }

    public function test_it_serves_the_active_themes_font_and_vite_entrypoint(): void
    {
        config()->set('panel.filament.default-theme', 'alpha');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(null);

        $this->assertTrue($panel->hasCustomFontFamily());
        $this->assertSame('Alpha Sans', $panel->getFontFamily());
        $this->assertSame('resources/css/themes/alpha.css', $panel->getViteTheme());
    }

    public function test_a_theme_without_a_font_or_vite_entrypoint_leaves_the_defaults_alone(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(null);

        $this->assertFalse($panel->hasCustomFontFamily());
        $this->assertNull($panel->getViteTheme());
    }

    public function test_an_inactive_themes_render_hooks_emit_nothing(): void
    {
        config()->set('panel.filament.default-theme', 'beta');

        $panel = $this->withThemes($this->panel());
        $this->actingAsUserWithTheme(null);

        $panel->boot();

        $this->assertSame('<!--beta-->', (string) FilamentView::renderHook('panels::head.end'));
    }
}

final class FakeThemePlugin implements Plugin
{
    /** @param array<string, array<int|string, string|int>|string> $colors */
    public function __construct(
        private readonly string $id,
        private readonly array $colors,
        private readonly ?string $font = null,
        private readonly ?string $viteTheme = null,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function register(Panel $panel): void
    {
        $panel->colors($this->colors);

        if ($this->font !== null) {
            $panel->font($this->font);
        }

        if ($this->viteTheme !== null) {
            $panel->viteTheme($this->viteTheme);
        }

        $panel->renderHook('panels::head.end', fn (): string => "<!--{$this->id}-->");
    }

    public function boot(Panel $panel): void {}
}
