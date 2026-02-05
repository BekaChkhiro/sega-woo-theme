<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccountNavigation extends Component
{
    public string $layout;
    public bool $showIcons;
    public array $menuItems;
    public string $currentEndpoint;

    /**
     * Icon mappings for WooCommerce account endpoints
     */
    protected array $icons = [
        'dashboard' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>',
        'orders' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>',
        'downloads' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>',
        'edit-address' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
        'edit-account' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
        'customer-logout' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>',
        'payment-methods' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
        'subscriptions' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>',
        'wishlist' => '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>',
    ];

    /**
     * Default icon for unknown endpoints
     */
    protected string $defaultIcon = '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

    public function __construct(
        string $layout = 'vertical',
        bool $showIcons = true
    ) {
        $this->layout = $layout;
        $this->showIcons = $showIcons;
        $this->menuItems = $this->getMenuItems();
        $this->currentEndpoint = $this->getCurrentEndpoint();
    }

    /**
     * Get WooCommerce account menu items
     */
    protected function getMenuItems(): array
    {
        if (!function_exists('wc_get_account_menu_items')) {
            return [];
        }

        return wc_get_account_menu_items();
    }

    /**
     * Get current WooCommerce endpoint
     */
    protected function getCurrentEndpoint(): string
    {
        if (!function_exists('WC') || !WC()->query) {
            return 'dashboard';
        }

        return WC()->query->get_current_endpoint() ?: 'dashboard';
    }

    /**
     * Check if an endpoint is active
     */
    public function isActive(string $endpoint): bool
    {
        return $endpoint === $this->currentEndpoint ||
               (empty($this->currentEndpoint) && $endpoint === 'dashboard');
    }

    /**
     * Get icon for an endpoint
     */
    public function getIcon(string $endpoint): string
    {
        return $this->icons[$endpoint] ?? $this->defaultIcon;
    }

    /**
     * Get endpoint URL
     */
    public function getEndpointUrl(string $endpoint): string
    {
        if (!function_exists('wc_get_account_endpoint_url')) {
            return '#';
        }

        return wc_get_account_endpoint_url($endpoint);
    }

    /**
     * Get CSS classes for a navigation item
     */
    public function getItemClasses(string $endpoint): string
    {
        $isActive = $this->isActive($endpoint);
        $isLogout = $endpoint === 'customer-logout';

        $baseClasses = 'flex items-center gap-3 text-sm font-medium transition-all';

        if ($this->layout === 'vertical') {
            $layoutClasses = 'rounded-lg px-4 py-3';
        } else {
            $layoutClasses = 'rounded-lg px-3 py-2';
        }

        if ($isActive) {
            $stateClasses = 'bg-primary-50 text-primary-700';
        } elseif ($isLogout) {
            $stateClasses = 'text-red-600 hover:bg-red-50 hover:text-red-700';
        } else {
            $stateClasses = 'text-secondary-700 hover:bg-secondary-50 hover:text-secondary-900';
        }

        return "{$baseClasses} {$layoutClasses} {$stateClasses}";
    }

    /**
     * Get CSS classes for an icon
     */
    public function getIconClasses(string $endpoint): string
    {
        $isActive = $this->isActive($endpoint);
        $isLogout = $endpoint === 'customer-logout';

        if ($isActive) {
            return 'text-primary-600';
        } elseif ($isLogout) {
            return 'text-red-500';
        }

        return 'text-secondary-400';
    }

    /**
     * Get container classes based on layout
     */
    public function getContainerClasses(): string
    {
        if ($this->layout === 'horizontal') {
            return 'flex flex-wrap gap-2';
        }

        return 'space-y-1';
    }

    public function render(): View
    {
        return view('components.account-navigation');
    }
}
