<?php

namespace App\Filament\Components\Forms\Fields;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class AffixedInput extends Field
{
    use HasExtraAlpineAttributes;

    /** @var view-string */
    protected string $view = 'filament.components.affixed-input';

    protected Field|Closure|null $leftComponent = null;

    protected Field|Closure|null $rightComponent = null;

    protected string|Closure|null $componentGap = 'gap-2';

    protected string|Closure|null $alignment = 'items-center';

    protected array|Closure|null $size = [50, 50];

    protected string|Closure|null $hintIconColor = 'primary';

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(true);
    }

    public function leftComponent(Field|Closure|null $component): static
    {
        $this->leftComponent = $component;

        return $this;
    }

    public function rightComponent(Field|Closure|null $component): static
    {
        $this->rightComponent = $component;

        return $this;
    }

    public function componentGap(string|Closure|null $gap): static
    {
        $this->componentGap = $gap;

        return $this;
    }

    public function alignment(string|Closure|null $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function size(int|array|Closure $leftSize, int $rightSize = null): static
    {
        if (is_array($leftSize)) {
            $this->size = $leftSize;
        } elseif ($rightSize !== null) {
            $this->size = [$leftSize, $rightSize];
        } else {
            $this->size = [$leftSize, $leftSize];
        }

        return $this;
    }

    public function getLeftComponent(): ?Field
    {
        return $this->evaluate($this->leftComponent);
    }

    public function getRightComponent(): ?Field
    {
        return $this->evaluate($this->rightComponent);
    }

    public function getComponentGap(): string
    {
        return $this->evaluate($this->componentGap) ?? 'gap-2';
    }

    public function getAlignment(): string
    {
        return $this->evaluate($this->alignment) ?? 'items-center';
    }

    public function getSize(): array
    {
        return $this->evaluate($this->size) ?? [50, 50];
    }

    public function hintIconColor(string|Closure|null $color): static
    {
        $this->hintIconColor = $color;

        return $this;
    }

    public function getHintIconColor(): string
    {
        return $this->evaluate($this->hintIconColor) ?? 'primary';
    }

    public function getHintIcon(): ?string
    {
        if ($this->getLeftComponent() || $this->getRightComponent()) {
            return null;
        }
        
        return parent::getHintIcon();
    }

    public function getHintIconTooltip(): ?string
    {
        return parent::getHintIconTooltip();
    }

    public function getOriginalHintIcon(): ?string
    {
        return parent::getHintIcon();
    }

    protected function getChildComponentsForForm(): array
    {
        $components = [];
        
        if ($leftComponent = $this->getLeftComponent()) {
            $components[] = $leftComponent;
        }
        
        if ($rightComponent = $this->getRightComponent()) {
            $components[] = $rightComponent;
        }
        
        return $components;
    }

    public function getChildComponentContainers(bool $withHidden = false): array
    {
        return collect($this->getChildComponentsForForm())
            ->mapWithKeys(fn (Field $component): array => $component->getChildComponentContainers($withHidden))
            ->all();
    }
}