<!doctype html>
<html @php(language_attributes()) class="scroll-smooth">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#4f46e5">
    @php(do_action('get_header'))
    @php(wp_head())

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body @php(body_class('antialiased bg-white text-secondary-900'))>
    @php(wp_body_open())

    <div id="app" class="flex min-h-screen flex-col">
      {{-- Skip to content link for accessibility --}}
      <a
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-primary-600 focus:px-4 focus:py-2 focus:text-white focus:outline-none"
        href="#main"
      >
        {{ __('Skip to content', 'sage') }}
      </a>

      {{-- Header --}}
      @include('sections.header')

      {{-- Search Popup --}}
      <x-search-popup />

      {{-- Main Content Area --}}
      <div class="flex-1">
        @hasSection('hero')
          @yield('hero')
        @endif

        <div class="@yield('container-class', 'shop-container py-8 lg:py-12')">
          @hasSection('breadcrumbs')
            <div class="mb-6">
              @yield('breadcrumbs')
            </div>
          @endif

          @hasSection('page-header')
            @yield('page-header')
          @endif

          @hasSection('sidebar')
            {{-- Layout with sidebar on left --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4 lg:gap-12">
              <aside class="order-2 lg:order-1 lg:col-span-1" role="complementary" aria-label="{{ __('Sidebar', 'sage') }}">
                @yield('sidebar')
              </aside>

              <main id="main" class="order-1 lg:order-2 lg:col-span-3">
                @yield('content')
              </main>
            </div>
          @else
            {{-- Full width layout --}}
            <main id="main">
              @yield('content')
            </main>
          @endif
        </div>
      </div>

      {{-- Footer --}}
      @include('sections.footer')
    </div>

    {{-- Toast Notifications --}}
    @include('partials.toast')

    @php(do_action('get_footer'))
    @php(wp_footer())
  </body>
</html>
