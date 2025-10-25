<?php

namespace App\Enums;

enum CustomizationKey: string
{
    case ConsoleRows = 'console_rows';
    case ConsoleFont = 'console_font';
    case ConsoleFontSize = 'console_font_size';
    case ConsoleGraphPeriod = 'console_graph_period';
    case DashboardLayout = 'dashboard_layout';
    case NavigationType = 'navigation_type';

    public function getDefaultValue(): string|int|bool
    {
        return match ($this) {
            self::ConsoleRows => 30,
            self::ConsoleFont => 'monospace',
            self::ConsoleFontSize => 14,
            self::ConsoleGraphPeriod => 30,
            self::DashboardLayout => 'grid',
            self::NavigationType => 'side'
        };
    }

    /** @return array<string, string|int|bool> */
    public static function getDefaultCustomization(): array
    {
        $default = [];

        foreach (self::cases() as $key) {
            $default[$key->value] = $key->getDefaultValue();
        }

        return $default;
    }
}
