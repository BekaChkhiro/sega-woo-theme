<footer class="mt-auto border-t border-secondary-100 bg-secondary-50">
  {{-- Newsletter Section --}}
  <div class="border-b border-secondary-200 bg-primary-600">
    <div class="shop-container py-8 lg:py-10">
      <div class="flex flex-col items-center justify-between gap-6 lg:flex-row">
        <div class="text-center lg:text-left">
          <h3 class="text-lg font-semibold text-white lg:text-xl">
            {{ __('Subscribe to our newsletter', 'sage') }}
          </h3>
          <p class="mt-1 text-sm text-primary-100">
            {{ __('Get the latest updates on new products and upcoming sales.', 'sage') }}
          </p>
        </div>
        <form class="flex w-full max-w-md gap-2" action="#" method="post">
          <label for="footer-email" class="sr-only">{{ __('Email address', 'sage') }}</label>
          <input
            type="email"
            id="footer-email"
            name="email"
            placeholder="{{ __('Enter your email', 'sage') }}"
            required
            class="flex-1 rounded-md border-0 bg-white/10 px-4 py-3 text-sm text-white placeholder-primary-200 ring-1 ring-inset ring-white/20 transition focus:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white"
          >
          <button
            type="submit"
            class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-primary-600 transition hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-primary-600"
          >
            {{ __('Subscribe', 'sage') }}
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Main Footer Content --}}
  <div class="shop-container py-12 lg:py-16">
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
      {{-- Column 1: About / Store Info --}}
      <div class="sm:col-span-2 lg:col-span-1">
        <a href="{{ home_url('/') }}" class="inline-block text-xl font-bold text-primary-600">
          @if (has_custom_logo())
            {!! get_custom_logo() !!}
          @else
            {!! $siteName !!}
          @endif
        </a>
        <p class="mt-4 text-sm leading-relaxed text-secondary-600">
          {{ __('Your trusted marketplace for quality products. We offer a curated selection with fast shipping and exceptional customer service.', 'sage') }}
        </p>

        {{-- Social Links --}}
        <div class="mt-6 flex items-center gap-4">
          <a
            href="#"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary-200 text-secondary-600 transition hover:bg-primary-600 hover:text-white"
            aria-label="{{ __('Facebook', 'sage') }}"
          >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
            </svg>
          </a>
          <a
            href="#"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary-200 text-secondary-600 transition hover:bg-primary-600 hover:text-white"
            aria-label="{{ __('Instagram', 'sage') }}"
          >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
            </svg>
          </a>
          <a
            href="#"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary-200 text-secondary-600 transition hover:bg-primary-600 hover:text-white"
            aria-label="{{ __('Twitter / X', 'sage') }}"
          >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
            </svg>
          </a>
          <a
            href="#"
            class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary-200 text-secondary-600 transition hover:bg-primary-600 hover:text-white"
            aria-label="{{ __('Pinterest', 'sage') }}"
          >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z" />
            </svg>
          </a>
        </div>
      </div>

      {{-- Column 2: Shop Links --}}
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wider text-secondary-900">
          {{ __('Shop', 'sage') }}
        </h4>
        <ul class="mt-4 space-y-3">
          @if (function_exists('wc_get_page_permalink'))
            <li>
              <a href="{{ wc_get_page_permalink('shop') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
                {{ __('All Products', 'sage') }}
              </a>
            </li>
          @endif
          <li>
            <a href="{{ home_url('/product-category/new-arrivals') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('New Arrivals', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/product-category/featured') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Featured', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/shop/?on_sale=1') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('On Sale', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/product-category/best-sellers') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Best Sellers', 'sage') }}
            </a>
          </li>
        </ul>
      </div>

      {{-- Column 3: Customer Service --}}
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wider text-secondary-900">
          {{ __('Customer Service', 'sage') }}
        </h4>
        <ul class="mt-4 space-y-3">
          <li>
            <a href="{{ home_url('/contact') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Contact Us', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/faq') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('FAQ', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/shipping-info') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Shipping Information', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/returns') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Returns & Exchanges', 'sage') }}
            </a>
          </li>
          <li>
            <a href="{{ home_url('/track-order') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Track Your Order', 'sage') }}
            </a>
          </li>
        </ul>
      </div>

      {{-- Column 4: My Account --}}
      <div>
        <h4 class="text-sm font-semibold uppercase tracking-wider text-secondary-900">
          {{ __('My Account', 'sage') }}
        </h4>
        <ul class="mt-4 space-y-3">
          @if (function_exists('wc_get_page_permalink'))
            <li>
              <a href="{{ wc_get_page_permalink('myaccount') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
                {{ is_user_logged_in() ? __('My Dashboard', 'sage') : __('Sign In / Register', 'sage') }}
              </a>
            </li>
            <li>
              <a href="{{ wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount')) }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
                {{ __('Order History', 'sage') }}
              </a>
            </li>
            <li>
              <a href="{{ wc_get_page_permalink('cart') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
                {{ __('Shopping Cart', 'sage') }}
              </a>
            </li>
            <li>
              <a href="{{ wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount')) }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
                {{ __('Account Settings', 'sage') }}
              </a>
            </li>
          @endif
          <li>
            <a href="{{ home_url('/wishlist') }}" class="text-sm text-secondary-600 transition hover:text-primary-600">
              {{ __('Wishlist', 'sage') }}
            </a>
          </li>
        </ul>
      </div>
    </div>

    {{-- Footer Widgets (if active) --}}
    @if (is_active_sidebar('sidebar-footer'))
      <div class="mt-12 border-t border-secondary-200 pt-12">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
          @php(dynamic_sidebar('sidebar-footer'))
        </div>
      </div>
    @endif
  </div>

  {{-- Footer Bottom --}}
  <div class="border-t border-secondary-200 bg-secondary-100">
    <div class="shop-container py-6">
      <div class="flex flex-col items-center gap-6 lg:flex-row lg:justify-between">
        {{-- Copyright --}}
        <p class="text-sm text-secondary-600">
          &copy; {{ date('Y') }} {!! $siteName !!}. {{ __('All rights reserved.', 'sage') }}
        </p>

        {{-- Footer Navigation --}}
        @if (has_nav_menu('footer_navigation'))
          <nav aria-label="{{ __('Footer Navigation', 'sage') }}">
            {!! wp_nav_menu([
              'theme_location' => 'footer_navigation',
              'menu_class' => 'flex flex-wrap items-center justify-center gap-x-6 gap-y-2',
              'container' => false,
              'echo' => false,
              'depth' => 1,
              'link_before' => '<span class="text-sm text-secondary-600 transition-colors hover:text-primary-600">',
              'link_after' => '</span>',
            ]) !!}
          </nav>
        @endif

        {{-- Payment Icons --}}
        <div class="flex items-center gap-3">
          <span class="sr-only">{{ __('Accepted payment methods', 'sage') }}</span>
          {{-- Visa --}}
          <svg class="h-8 w-auto text-secondary-400" viewBox="0 0 38 24" fill="none" aria-hidden="true">
            <rect width="38" height="24" rx="3" fill="currentColor" fill-opacity="0.1"/>
            <path fill="currentColor" d="M15.5 16.5h-2.3l1.4-8.7h2.3l-1.4 8.7zm-4.4 0H8.6l-1.9-6.9c-.1-.3-.2-.5-.5-.6-.5-.3-1.3-.5-2-.6v-.2h3.6c.5 0 .9.3 1 .8l.9 4.7 2.2-5.5h2.3l-3.1 8.3zm11.3 0h2l-1.7-8.7h-1.9c-.4 0-.8.2-.9.6l-3.3 8.1h2.3l.5-1.3h2.8l.2 1.3zm-2.4-3.2l1.2-3.2.6 3.2h-1.8zm-7.5-5.5l.3-1.8c-.5-.2-1-.3-1.5-.3-1.5 0-2.5.8-2.5 1.9 0 .8.8 1.3 1.4 1.6.6.3.8.5.8.8 0 .4-.5.6-1 .6-.6 0-1.3-.2-1.7-.4l-.3 1.8c.5.2 1.2.3 1.9.3 1.7 0 2.8-.8 2.8-2 0-.6-.4-1.1-1.3-1.5-.5-.3-.9-.5-.9-.8 0-.3.3-.6.9-.6.4 0 .9.1 1.1.2z"/>
          </svg>
          {{-- Mastercard --}}
          <svg class="h-8 w-auto text-secondary-400" viewBox="0 0 38 24" fill="none" aria-hidden="true">
            <rect width="38" height="24" rx="3" fill="currentColor" fill-opacity="0.1"/>
            <circle cx="15" cy="12" r="5" fill="currentColor" fill-opacity="0.4"/>
            <circle cx="23" cy="12" r="5" fill="currentColor" fill-opacity="0.6"/>
          </svg>
          {{-- PayPal --}}
          <svg class="h-8 w-auto text-secondary-400" viewBox="0 0 38 24" fill="none" aria-hidden="true">
            <rect width="38" height="24" rx="3" fill="currentColor" fill-opacity="0.1"/>
            <path fill="currentColor" d="M23.5 7h-4.2c-.3 0-.5.2-.6.4l-1.7 10.8c0 .2.1.4.4.4h2c.3 0 .5-.2.6-.4l.5-3c0-.3.3-.4.6-.4h1.3c2.7 0 4.3-1.3 4.7-3.9.2-1.1 0-2-.5-2.6-.6-.8-1.6-1.3-3.1-1.3zm.5 3.8c-.2 1.5-1.3 1.5-2.4 1.5H21l.4-2.8c0-.2.2-.3.4-.3h.2c.7 0 1.4 0 1.8.4.2.2.3.6.2 1.2z"/>
            <path fill="currentColor" d="M13.7 7H9.5c-.3 0-.5.2-.6.4l-1.7 10.8c0 .2.1.4.4.4h2.2c.2 0 .4-.1.4-.3l.5-3.1c0-.3.3-.4.6-.4h1.3c2.7 0 4.3-1.3 4.7-3.9.2-1.1 0-2-.5-2.6-.6-.8-1.6-1.3-3.1-1.3zm.5 3.8c-.2 1.5-1.3 1.5-2.4 1.5h-.6l.4-2.8c0-.2.2-.3.4-.3h.2c.7 0 1.4 0 1.8.4.2.2.3.6.2 1.2z"/>
          </svg>
          {{-- Apple Pay --}}
          <svg class="h-8 w-auto text-secondary-400" viewBox="0 0 38 24" fill="none" aria-hidden="true">
            <rect width="38" height="24" rx="3" fill="currentColor" fill-opacity="0.1"/>
            <path fill="currentColor" d="M13.8 8.5c-.4.5-.9.8-1.5.8-.1-.6.2-1.2.5-1.6.4-.5 1-.8 1.4-.8.1.6-.1 1.2-.4 1.6zm.4 1c-.8 0-1.5.5-1.9.5-.4 0-1-.4-1.7-.4-.9 0-1.7.5-2.1 1.3-.9 1.6-.2 3.9.6 5.2.4.6.9 1.3 1.6 1.3.6 0 .9-.4 1.6-.4.8 0 1 .4 1.6.4.7 0 1.1-.6 1.5-1.2.5-.7.7-1.4.7-1.4-.3-.1-1.3-.5-1.3-2 0-1.3 1-1.9 1.1-2-.6-.9-1.6-1-1.9-1-.3 0-.6.1-.8.2zm7.3 5.5v-6h1.5c1.2 0 1.9.7 1.9 1.9 0 1.2-.7 1.9-2 1.9h-1v2.2h-.4zm.4-2.5h.9c.9 0 1.4-.5 1.4-1.4 0-.9-.5-1.4-1.4-1.4h-.9v2.8zm3.7 2.5v-4.3h.3v.7c.2-.5.6-.8 1.2-.8.1 0 .2 0 .3.1v.4c-.1 0-.2-.1-.4-.1-.5 0-.9.4-1.1 1v3h-.3zm2.2-2.1c0-1.4.8-2.2 1.8-2.2s1.8.8 1.8 2.2c0 1.4-.7 2.2-1.8 2.2s-1.8-.8-1.8-2.2zm.4 0c0 1.1.5 1.8 1.4 1.8s1.4-.7 1.4-1.8c0-1.1-.5-1.8-1.4-1.8s-1.4.7-1.4 1.8z"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
</footer>
