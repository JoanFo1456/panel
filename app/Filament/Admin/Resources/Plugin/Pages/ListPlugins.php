<?php

namespace App\Filament\Admin\Resources\Plugin\Pages;

use App\Enums\PluginCategory;
use App\Facades\Plugins;
use App\Filament\Admin\Resources\Plugin;
use App\Models\Plugin as PluginModel;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ListPlugins extends ListRecords
{
    protected static string $resource = Plugin::class;

    public function reorderTable(array $order, string|int|null $draggedRecordKey = null): void
    {
        Plugins::updateLoadOrder($order);
    }

    public function getTabs(): array
    {
        $tabs = [];

        foreach (PluginCategory::cases() as $category) {
            $tabs[$category->value] = Tab::make($category->getLabel())
                ->icon($category->getIcon())
                ->badge(PluginModel::whereCategory($category->value)->count())
                ->modifyQueryUsing(fn ($query) => $query->whereCategory($category->value));
        }

        $tabs['all'] = Tab::make(trans('admin/plugin.all'))
            ->badge(PluginModel::count());

        return $tabs;
    }
}
