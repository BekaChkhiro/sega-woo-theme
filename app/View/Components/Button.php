<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $variant;
    public string $size;
    public string $type;
    public bool $fullWidth;
    public bool $rounded;
    public bool $loading;
    public bool $disabled;
    public ?string $href;

    public function __construct(
        string $variant = 'primary',
        string $size = 'md',
        string $type = 'button',
        bool $fullWidth = false,
        bool $rounded = false,
        bool $loading = false,
        bool $disabled = false,
        ?string $href = null
    ) {
        $this->variant = $variant;
        $this->size = $size;
        $this->type = $type;
        $this->fullWidth = $fullWidth;
        $this->rounded = $rounded;
        $this->loading = $loading;
        $this->disabled = $disabled || $loading;
        $this->href = $href;
    }

    public function isLink(): bool
    {
        return $this->type === 'link' || $this->href !== null;
    }

    public function baseClasses(): string
    {
        return 'inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';
    }

    public function variantClasses(): string
    {
        return match ($this->variant) {
            'primary' => 'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 focus:ring-primary-500 shadow-sm shadow-primary-600/25',
            'secondary' => 'bg-secondary-600 text-white hover:bg-secondary-700 active:bg-secondary-800 focus:ring-secondary-500 shadow-sm',
            'outline' => 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50 active:bg-primary-100 focus:ring-primary-500',
            'outline-secondary' => 'border-2 border-secondary-300 text-secondary-700 hover:bg-secondary-50 active:bg-secondary-100 focus:ring-secondary-500',
            'ghost' => 'text-secondary-700 hover:bg-secondary-100 active:bg-secondary-200 focus:ring-secondary-500',
            'danger' => 'bg-error-600 text-white hover:bg-error-700 active:bg-error-800 focus:ring-error-500 shadow-sm',
            'success' => 'bg-success-600 text-white hover:bg-success-700 active:bg-success-800 focus:ring-success-500 shadow-sm',
            'link' => 'text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline focus:ring-primary-500',
            default => 'bg-primary-600 text-white hover:bg-primary-700 active:bg-primary-800 focus:ring-primary-500 shadow-sm shadow-primary-600/25',
        };
    }

    public function sizeClasses(): string
    {
        $borderRadius = $this->rounded ? 'rounded-full' : match ($this->size) {
            'xs', 'sm' => 'rounded-lg',
            'md' => 'rounded-xl',
            'lg', 'xl' => 'rounded-xl',
            default => 'rounded-xl',
        };

        $padding = match ($this->size) {
            'xs' => 'px-2.5 py-1 text-xs',
            'sm' => 'px-3 py-1.5 text-sm',
            'md' => 'px-4 py-2 text-sm',
            'lg' => 'px-5 py-2.5 text-base',
            'xl' => 'px-6 py-3 text-base',
            default => 'px-4 py-2 text-sm',
        };

        return "{$borderRadius} {$padding}";
    }

    public function iconSizeClasses(): string
    {
        return match ($this->size) {
            'xs' => 'h-3.5 w-3.5',
            'sm' => 'h-4 w-4',
            'md' => 'h-5 w-5',
            'lg' => 'h-5 w-5',
            'xl' => 'h-6 w-6',
            default => 'h-5 w-5',
        };
    }

    public function widthClasses(): string
    {
        return $this->fullWidth ? 'w-full' : '';
    }

    public function allClasses(): string
    {
        return implode(' ', array_filter([
            $this->baseClasses(),
            $this->variantClasses(),
            $this->sizeClasses(),
            $this->widthClasses(),
        ]));
    }

    public function render(): View
    {
        return view('components.button');
    }
}
