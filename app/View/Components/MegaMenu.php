<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WP_Term;

class MegaMenu extends Component
{
    public array $items = [];
    public string $mode;
    public string $menuLocation;
    public int $limit;
    public bool $showProductCount;
    public bool $showThumbnails;
    public bool $showViewAll;
    public string $title;

    public function __construct(
        string $mode = 'categories',
        string $menuLocation = 'mega_menu',
        int $limit = 0,
        bool $showProductCount = true,
        bool $showThumbnails = true,
        bool $showViewAll = true,
        string $title = ''
    ) {
        $this->mode = $mode;
        $this->menuLocation = $menuLocation;
        $this->limit = $limit;
        $this->showProductCount = $showProductCount;
        $this->showThumbnails = $showThumbnails;
        $this->showViewAll = $showViewAll;
        $this->title = $title ?: __('Categories', 'sage');

        // Get items based on mode
        if ($mode === 'menu') {
            $this->items = $this->getMenuItems();
        } else {
            $this->items = $this->getCategories();
        }
    }

    /**
     * Get WordPress menu items with children
     */
    protected function getMenuItems(): array
    {
        if (! has_nav_menu($this->menuLocation)) {
            // Fallback to categories if menu not set
            return $this->getCategories();
        }

        $menuLocations = get_nav_menu_locations();
        $menuId = $menuLocations[$this->menuLocation] ?? 0;

        if (! $menuId) {
            return $this->getCategories();
        }

        $menuItems = wp_get_nav_menu_items($menuId);

        if (! $menuItems || is_wp_error($menuItems)) {
            return $this->getCategories();
        }

        // Build hierarchical menu structure
        return $this->buildMenuTree($menuItems);
    }

    /**
     * Build hierarchical tree from flat menu items
     */
    protected function buildMenuTree(array $menuItems): array
    {
        $itemsById = [];
        $tree = [];

        // First pass: index items by ID
        foreach ($menuItems as $item) {
            $itemsById[$item->ID] = [
                'id' => $item->ID,
                'name' => $item->title,
                'link' => $item->url,
                'target' => $item->target,
                'parent' => (int) $item->menu_item_parent,
                'thumbnail' => $this->getMenuItemThumbnail($item),
                'icon' => null,
                'count' => null,
                'children' => [],
                'hasChildren' => false,
            ];
        }

        // Second pass: build tree structure
        foreach ($itemsById as $id => &$item) {
            if ($item['parent'] === 0) {
                $tree[] = &$item;
            } else {
                if (isset($itemsById[$item['parent']])) {
                    $itemsById[$item['parent']]['children'][] = &$item;
                    $itemsById[$item['parent']]['hasChildren'] = true;
                }
            }
        }

        // Limit top-level items (0 = no limit)
        if ($this->limit > 0) {
            return array_slice($tree, 0, $this->limit);
        }

        return $tree;
    }

    /**
     * Get thumbnail for menu item (if it's a category or has featured image)
     */
    protected function getMenuItemThumbnail($item): ?string
    {
        if (! $this->showThumbnails) {
            return null;
        }

        // Check if menu item is a product category
        if ($item->type === 'taxonomy' && $item->object === 'product_cat') {
            return $this->getCategoryThumbnail((int) $item->object_id);
        }

        // Check for custom menu item thumbnail (requires plugin or custom meta)
        $thumbnailId = get_post_meta($item->ID, '_menu_item_thumbnail_id', true);
        if ($thumbnailId) {
            return wp_get_attachment_image_url($thumbnailId, 'thumbnail');
        }

        return null;
    }

    /**
     * Get parent categories with their subcategories
     */
    protected function getCategories(): array
    {
        if (! function_exists('get_terms')) {
            return [];
        }

        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
            'orderby' => 'count',
            'order' => 'DESC',
        ];

        // limit=0 means no limit (show all)
        if ($this->limit > 0) {
            $args['number'] = $this->limit;
        }

        $parentCategories = get_terms($args);

        if (is_wp_error($parentCategories) || empty($parentCategories)) {
            return [];
        }

        $categories = [];

        foreach ($parentCategories as $parent) {
            $children = $this->getSubcategories($parent->term_id);

            $categories[] = [
                'id' => $parent->term_id,
                'name' => $parent->name,
                'slug' => $parent->slug,
                'count' => $parent->count,
                'link' => get_term_link($parent),
                'thumbnail' => $this->getCategoryThumbnail($parent->term_id),
                'icon' => $this->getCategoryIcon($parent->slug),
                'children' => $children,
                'hasChildren' => ! empty($children),
            ];
        }

        return $categories;
    }

    /**
     * Get subcategories for a parent category
     */
    protected function getSubcategories(int $parentId): array
    {
        $childTerms = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => $parentId,
            'number' => 8,
            'orderby' => 'count',
            'order' => 'DESC',
        ]);

        if (is_wp_error($childTerms) || empty($childTerms)) {
            return [];
        }

        $children = [];

        foreach ($childTerms as $child) {
            $children[] = [
                'id' => $child->term_id,
                'name' => $child->name,
                'slug' => $child->slug,
                'count' => $child->count,
                'link' => get_term_link($child),
                'thumbnail' => $this->getCategoryThumbnail($child->term_id),
            ];
        }

        return $children;
    }

    /**
     * Get category thumbnail URL
     */
    protected function getCategoryThumbnail(int $termId): ?string
    {
        if (! $this->showThumbnails) {
            return null;
        }

        $thumbnailId = get_term_meta($termId, 'thumbnail_id', true);

        if (! $thumbnailId) {
            return null;
        }

        return wp_get_attachment_image_url($thumbnailId, 'thumbnail');
    }

    /**
     * Get a default icon for the category based on slug
     * Returns an SVG path or null
     */
    protected function getCategoryIcon(string $slug): ?string
    {
        // Map common category slugs to icons
        $iconMap = [
            'electronics' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            'clothing' => 'M7 7h10a2 2 0 012 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm5-5a3 3 0 00-3 3v2h6V5a3 3 0 00-3-3z',
            'fashion' => 'M7 7h10a2 2 0 012 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2zm5-5a3 3 0 00-3 3v2h6V5a3 3 0 00-3-3z',
            'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            'sports' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'beauty' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
            'toys' => 'M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5',
            'books' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'food' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
            'garden' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
        ];

        return $iconMap[$slug] ?? null;
    }

    /**
     * Get shop page URL
     */
    public function shopUrl(): string
    {
        if (function_exists('wc_get_page_permalink')) {
            return wc_get_page_permalink('shop');
        }

        return '/shop';
    }

    /**
     * Check if there are items to display
     */
    public function hasItems(): bool
    {
        return ! empty($this->items);
    }

    /**
     * Get available menu locations for this component
     */
    public static function getAvailableMenuLocations(): array
    {
        return [
            'mega_menu' => __('Mega Menu', 'sage'),
        ];
    }

    public function render(): View
    {
        return view('components.mega-menu');
    }
}
