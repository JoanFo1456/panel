@php
    $leftComponent = $getLeftComponent();
    $rightComponent = $getRightComponent();
    $gap = $getComponentGap();
    $alignment = $getAlignment();
    $size = $getSize();
    $hasHintIcon = $getOriginalHintIcon();
    $hintIconColor = $getHintIconColor();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="space-y-2">
        @if ($leftComponent || $rightComponent)
            <div class="flex {{ $alignment }} {{ $gap }} w-full">
                @if ($leftComponent)
                    <div style="flex: 0 0 {{ $size[0] }}%">
                        {{ $leftComponent->container($getContainer()) }}
                    </div>
                @endif
                @if ($rightComponent)
                    <div class="flex items-center" style="flex: 0 0 {{ $size[1] }}%">
                        <div class="flex-1">
                            {{ $rightComponent->container($getContainer()) }}
                        </div>
                        @if ($hasHintIcon)
                            <div class="ml-2 flex-shrink-0">
                                <x-filament::icon-button
                                    :icon="$hasHintIcon"
                                    :tooltip="$getHintIconTooltip()"
                                    size="sm"
                                    :color="$hintIconColor"
                                />
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-dynamic-component>