@php
    $leftComponent = $getLeftComponent();
    $rightComponent = $getRightComponent();
    $gap = $getComponentGap();
    $alignment = $getAlignment();
    $size = $getSize();
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
                    <div style="flex: 0 0 {{ $size[1] }}%">
                        {{ $rightComponent->container($getContainer()) }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-dynamic-component>