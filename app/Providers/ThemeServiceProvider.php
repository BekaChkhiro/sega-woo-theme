<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Register View Composers
        $this->bootViewComposers();

        // Blade components are auto-discovered in Sage
        // No need to manually register them
    }

    /**
     * Register view composers.
     *
     * Note: Composers with `protected static $views` property are auto-discovered by Sage.
     * Manual registration is only needed for composers without that property.
     *
     * @return void
     */
    protected function bootViewComposers()
    {
        // Composers are auto-discovered via their static $views property:
        // - Homepage::class -> 'front-page'
        // - Shop::class -> 'woocommerce.archive-product'
        // - Product::class -> 'woocommerce.single-product'
        //
        // No manual registration needed.
    }

}
