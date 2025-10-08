@php
    $fieldWrapperView = $getFieldWrapperView();
    $isVertical = $isVertical();
    $pipsMode = $getPipsMode();
    $livewireKey = $getLivewireKey();
    $isDisabled = $isDisabled();
@endphp

<style>
.fi-fo-compact-slider {
    border-radius: var(--radius-lg);
    border-style: var(--tw-border-style);
    --tw-shadow: 0 1px 3px 0 var(--tw-shadow-color, #0000001a), 0 1px 2px -1px var(--tw-shadow-color, #0000001a);
    --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
    box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    --tw-ring-color: color-mix(in oklab, var(--gray-950) 10%, transparent);
    background-color: transparent;
    border-width: 0;
    gap: 0;
}

.fi-fo-compact-slider:where(.dark, .dark *) {
    --tw-ring-color: #fff3;
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider:where(.dark, .dark *) {
        --tw-ring-color: color-mix(in oklab, var(--color-white) 20%, transparent);
    }
}

.fi-fo-compact-slider .noUi-connect {
    background-color: var(--primary-500);
}

.fi-fo-compact-slider .noUi-connect:where(.dark, .dark *) {
    background-color: var(--primary-600);
}

.fi-fo-compact-slider .noUi-connects {
    border-radius: var(--radius-lg);
    background-color: var(--gray-950);
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider .noUi-connects {
        background-color: color-mix(in oklab, var(--gray-950) 5%, transparent);
    }
}

.fi-fo-compact-slider .noUi-connects:where(.dark, .dark *) {
    background-color: #ffffff0d;
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider .noUi-connects:where(.dark, .dark *) {
        background-color: color-mix(in oklab, var(--color-white) 5%, transparent);
    }
}

.fi-fo-compact-slider .noUi-handle {
    border-radius: var(--radius-lg);
    border-style: var(--tw-border-style);
    border-width: 1px;
    border-color: var(--gray-950);
    position: absolute;
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider .noUi-handle {
        border-color: color-mix(in oklab, var(--gray-950) 10%, transparent);
    }
}

.fi-fo-compact-slider .noUi-handle {
    background-color: var(--color-white);
    --tw-shadow: 0 0 #0000;
    box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    backface-visibility: hidden;
}

.fi-fo-compact-slider .noUi-handle:focus {
    outline-style: var(--tw-outline-style);
    outline-width: 2px;
    outline-color: var(--primary-600);
}

.fi-fo-compact-slider .noUi-handle:where(.dark, .dark *) {
    border-color: #fff3;
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider .noUi-handle:where(.dark, .dark *) {
        border-color: color-mix(in oklab, var(--color-white) 20%, transparent);
    }
}

.fi-fo-compact-slider .noUi-handle:where(.dark, .dark *) {
    background-color: var(--gray-700);
}

.fi-fo-compact-slider .noUi-handle:where(.dark, .dark *):focus {
    outline-color: var(--primary-500);
}

.fi-fo-compact-slider .noUi-handle:before,
.fi-fo-compact-slider .noUi-handle:after {
    border-style: var(--tw-border-style);
    background-color: var(--gray-400);
    border-width: 0;
}

.fi-fo-compact-slider .noUi-tooltip {
    border-radius: var(--radius-md);
    border-style: var(--tw-border-style);
    background-color: var(--color-white);
    color: var(--gray-950);
    --tw-shadow: 0 1px 3px 0 var(--tw-shadow-color, #0000001a), 0 1px 2px -1px var(--tw-shadow-color, #0000001a);
    --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
    box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    --tw-ring-color: color-mix(in oklab, var(--gray-950) 10%, transparent);
    border-width: 0;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.2s ease, visibility 0.2s ease;
}

.fi-fo-compact-slider.noUi-state-drag .noUi-tooltip,
.fi-fo-compact-slider .noUi-active .noUi-tooltip,
.fi-fo-compact-slider .noUi-handle.noUi-active .noUi-tooltip,
.fi-fo-compact-slider .noUi-origin .noUi-handle.noUi-active .noUi-tooltip,
.fi-fo-compact-slider:hover .noUi-tooltip {
    opacity: 1;
    visibility: visible;
}

.fi-fo-compact-slider .noUi-tooltip:where(.dark, .dark *) {
    background-color: var(--gray-800);
    color: var(--color-white);
    --tw-ring-color: #fff3;
}

@supports (color: color-mix(in lab, red, red)) {
    .fi-fo-compact-slider .noUi-tooltip:where(.dark, .dark *) {
        --tw-ring-color: color-mix(in oklab, var(--color-white) 20%, transparent);
    }
}

.fi-fo-compact-slider .noUi-horizontal .noUi-tooltip {
    bottom: 100%;
    left: 50%;
    transform: translate(-50%, -8px);
    margin-bottom: 0;
}

.fi-fo-compact-slider .noUi-vertical .noUi-tooltip {
    top: 50%;
    right: 100%;
    transform: translate(-8px, -50%);
    margin-right: 0;
}

.fi-fo-compact-slider .noUi-pips .noUi-value {
    color: var(--gray-950);
}

.fi-fo-compact-slider .noUi-pips .noUi-value:where(.dark, .dark *) {
    color: var(--color-white);
}

.fi-fo-compact-slider.fi-fo-compact-slider-vertical {
    margin-top: 0;
    height: calc(var(--spacing) * 24);
}
</style>

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('slider', 'filament/forms') }}"
        x-data="sliderFormComponent({
                    arePipsStepped: @js($arePipsStepped()),
                    behavior: @js($getBehaviorForJs()),
                    decimalPlaces: @js($getDecimalPlaces()),
                    fillTrack: @js($getFillTrack()),
                    isDisabled: @js($isDisabled),
                    isRtl: @js($isRtl()),
                    isVertical: @js($isVertical),
                    maxDifference: @js($getMaxDifference()),
                    minDifference: @js($getMinDifference()),
                    maxValue: @js($getMaxValue()),
                    minValue: @js($getMinValue()),
                    nonLinearPoints: @js($getNonLinearPoints()),
                    pipsDensity: @js($getPipsDensity()),
                    pipsFilter: @js($getPipsFilterForJs()),
                    pipsFormatter: @js($getPipsFormatterForJs()),
                    pipsMode: @js($pipsMode),
                    pipsValues: @js($getPipsValues()),
                    rangePadding: @js($getRangePadding()),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                    step: @js($getStep()),
                    tooltips: @js($getTooltipsForJs()),
                })"
        wire:ignore
        wire:key="{{ $livewireKey }}.{{
            substr(md5(serialize([
                $isDisabled,
            ])), 0, 64)
        }}"
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->merge($getExtraAlpineAttributes(), escape: false)
                ->class([
                    'fi-fo-compact-slider',
                    'fi-fo-compact-slider-has-pips' => $pipsMode,
                    'fi-fo-compact-slider-has-tooltips' => $hasTooltips(),
                    'fi-fo-compact-slider-vertical' => $isVertical,
                ])
        }}
    ></div>
</x-dynamic-component>