@php
    $leftComponent = $getLeftComponent();
    $rightComponent = $getRightComponent();
    $gap = $getComponentGap();
    $alignment = $getAlignment();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <x-slot name="label">
        {{ $getLabel() }}
    </x-slot>

    @if ($leftComponent || $rightComponent)
        <div class="flex items-center {{ $gap }} w-full">
            @if ($leftComponent)
                <div class="flex-1 min-w-0 self-center">
                    {{ $leftComponent->container($getContainer()) }}
                </div>
            @endif
            @if ($rightComponent)
                <div class="flex-1 min-w-0 self-center">
                    {{ $rightComponent->container($getContainer()) }}
                </div>
            @endif
        </div>
    @endif
</x-dynamic-component>