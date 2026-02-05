<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ColorSwatch extends Component
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

    /**
     * Determine if a hex color is light (for contrast calculations).
     */
    public function isLightColor(string $hexColor): bool
    {
        $hex = ltrim($hexColor, '#');

        // Handle shorthand hex
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // Default to dark if invalid
        if (strlen($hex) !== 6) {
            return false;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.5;
    }

    public function render(): View
    {
        return view('components.color-swatch');
    }
}
