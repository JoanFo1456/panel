@php
    use App\Enums\TablerIcon;
    use Illuminate\Support\Js;
@endphp

<div x-data="{ open: false }" class="grid gap-px">
    <x-filament::dropdown.list.item
        :icon="TablerIcon::Palette"
        :tooltip="trans('profile.theme')"
        x-on:click="open = ! open"
    >
        {{ $themes[$selected] ?? '' }}
    </x-filament::dropdown.list.item>

    <div x-cloak x-show="open" class="grid gap-px">
        @foreach ($themes as $id => $name)
            <x-filament::dropdown.list.item
                :icon="$id === $selected ? TablerIcon::Check : TablerIcon::Point"
                :color="$id === $selected ? 'primary' : 'gray'"
                wire:click="save({{ Js::from($id) }})"
            >
                {{ $name }}
            </x-filament::dropdown.list.item>
        @endforeach
    </div>
</div>
