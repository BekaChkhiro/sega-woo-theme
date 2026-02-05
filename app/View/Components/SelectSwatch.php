<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectSwatch extends Component
{
    public string $attributeName;
    public string $attributeLabel;
    public string $sanitizedName;
    public array $options;
    public int $productId;

    public function __construct(
        string $attributeName,
        string $attributeLabel,
        string $sanitizedName,
        array $options = [],
        int $productId = 0
    ) {
        $this->attributeName = $attributeName;
        $this->attributeLabel = $attributeLabel;
        $this->sanitizedName = $sanitizedName;
        $this->options = $options;
        $this->productId = $productId;
    }

    /**
     * Get the attribute slug for form field.
     */
    public function attributeSlug(): string
    {
        return 'attribute_' . $this->sanitizedName;
    }

    /**
     * Get the default selected value.
     */
    public function defaultValue(): string
    {
        foreach ($this->options as $option) {
            if (!empty($option['selected'])) {
                return $option['slug'];
            }
        }
        return '';
    }

    public function render(): View
    {
        return view('components.select-swatch');
    }
}
