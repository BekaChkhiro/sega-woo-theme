<header x-data="{ categoriesOpen: false }" @keydown.escape.window="categoriesOpen = false" class="sticky top-0 z-40 border-b border-secondary-100 bg-white/95 backdrop-blur-sm">
  <div class="shop-container">
    <div class="flex h-16 items-center justify-between gap-4 lg:h-20">
      {{-- Logo + Categories Button Container --}}
      <div class="flex items-center gap-3 lg:gap-4">
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

        {{-- Categories Dropdown Button (Desktop only, not on homepage) --}}
        @if (!is_front_page())
          <div class="relative hidden lg:block" @click.away="categoriesOpen = false">
            <button
              type="button"
              @click="categoriesOpen = !categoriesOpen"
              @mouseenter="categoriesOpen = true"
              class="flex items-center gap-2 rounded-lg border border-secondary-200 bg-secondary-50 px-4 py-2.5 text-sm font-medium text-secondary-700 transition-all hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700"
              :class="{ 'border-primary-200 bg-primary-50 text-primary-700': categoriesOpen }"
              aria-expanded="categoriesOpen"
              aria-haspopup="true"
            >
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <span>{{ __('Categories', 'sega-woo-theme') }}</span>
              <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': categoriesOpen }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            {{-- Categories Dropdown Panel --}}
            <div
              x-show="categoriesOpen"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 -translate-y-2"
              x-transition:enter-end="opacity-100 translate-y-0"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 translate-y-0"
              x-transition:leave-end="opacity-0 -translate-y-2"
              @mouseenter="categoriesOpen = true"
              @mouseleave="categoriesOpen = false"
              class="absolute left-0 top-full z-50 mt-2 w-72"
              style="display: none;"
            >
              <x-mega-menu
                mode="menu"
                menu-location="mega_menu"
                :limit="0"
                :show-product-count="false"
                :show-thumbnails="true"
                :show-view-all="true"
                :title="__('Categories', 'sega-woo-theme')"
                class="max-h-[70vh] shadow-xl"
              />
            </div>
          </div>
        @endif
      </div>

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
          aria-label="{{ __('Search', 'sega-woo-theme') }}"
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

        {{-- Language Switcher --}}
        <x-language-switcher class="hidden sm:block" />

        {{-- Mobile Menu Toggle --}}
        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-full text-secondary-600 transition-colors hover:bg-secondary-100 hover:text-secondary-900 lg:hidden"
          aria-label="{{ __('Menu', 'sega-woo-theme') }}"
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
      {{-- Mobile Categories Button --}}
      @if (!is_front_page())
        <div class="mb-4" x-data="{ mobileCategories: false }">
          <button
            type="button"
            @click="mobileCategories = !mobileCategories"
            class="flex w-full items-center justify-between rounded-lg border border-secondary-200 bg-secondary-50 px-4 py-3 text-base font-medium text-secondary-700"
            :class="{ 'border-primary-200 bg-primary-50 text-primary-700': mobileCategories }"
          >
            <span class="flex items-center gap-2">
              <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              {{ __('Categories', 'sega-woo-theme') }}
            </span>
            <svg
              class="h-5 w-5 transition-transform duration-200"
              :class="{ 'rotate-180': mobileCategories }"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          {{-- Mobile Categories Panel --}}
          <div
            x-show="mobileCategories"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mt-2"
            style="display: none;"
          >
            <x-mega-menu
              mode="menu"
              menu-location="mega_menu"
              :limit="0"
              :show-product-count="false"
              :show-thumbnails="true"
              :show-view-all="true"
              :title="__('Categories', 'sega-woo-theme')"
              class="max-h-[50vh]"
            />
          </div>
        </div>
      @endif

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

      {{-- Mobile Language Switcher --}}
      <div class="mt-4 border-t border-secondary-100 pt-4">
        <x-language-switcher />
      </div>
    </div>
  </div>
</header>
