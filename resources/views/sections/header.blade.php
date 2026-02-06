<header x-data class="sticky top-0 z-40 border-b border-secondary-100 bg-white/95 backdrop-blur-sm">
  <div class="shop-container">
    <div class="flex h-16 items-center justify-between gap-4 lg:h-20">
      {{-- Logo / Site Name --}}
      <a
        class="flex-shrink-0 text-xl font-bold text-primary-600 transition-colors hover:text-primary-700 lg:text-2xl"
        href="{{ home_url('/') }}"
      >
        @if (has_custom_logo())
          {!! get_custom_logo() !!}
        @else
          {!! $siteName !!}
        @endif
      </a>

      {{-- Primary Navigation --}}
      @if (has_nav_menu('primary_navigation'))
        <nav
          class="hidden lg:flex lg:flex-1 lg:justify-center"
          aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}"
        >
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'menu_class' => 'flex items-center gap-8',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="text-sm font-medium text-secondary-700 transition-colors hover:text-primary-600">',
            'link_after' => '</span>',
          ]) !!}
        </nav>
      @endif

      {{-- Header Actions (Search, Cart) --}}
      <div class="flex items-center gap-2 lg:gap-4">
        {{-- Search Toggle --}}
        <button
          type="button"
          @click="$dispatch('open-search-popup')"
          class="flex h-10 w-10 items-center justify-center rounded-full text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900"
          aria-label="{{ __('Search', 'sage') }}"
          aria-haspopup="dialog"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </button>

        {{-- Mini Cart Dropdown --}}
        @if (function_exists('WC'))
          @include('partials.mini-cart')
        @endif

        {{-- Mobile Menu Toggle --}}
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-full text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 lg:hidden"
          aria-label="{{ __('Menu', 'sage') }}"
          aria-expanded="false"
          data-mobile-menu-toggle
        >
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  {{-- Mobile Navigation (hidden by default) --}}
  <div class="hidden border-t border-secondary-100 lg:hidden" data-mobile-menu>
    <div class="shop-container py-4">
      @if (has_nav_menu('primary_navigation'))
        <nav aria-label="{{ wp_get_nav_menu_name('primary_navigation') }}">
          {!! wp_nav_menu([
            'theme_location' => 'primary_navigation',
            'menu_class' => 'flex flex-col gap-2',
            'container' => false,
            'echo' => false,
            'link_before' => '<span class="block py-2 text-base font-medium text-secondary-700">',
            'link_after' => '</span>',
          ]) !!}
        </nav>
      @endif
    </div>
  </div>
</header>
