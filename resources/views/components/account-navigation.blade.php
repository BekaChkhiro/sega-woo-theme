{{--
  Component: Account Navigation
  Description: Reusable navigation component for WooCommerce My Account pages

  Props:
    - layout: 'vertical' (default) or 'horizontal'
    - showIcons: true (default) or false

  Usage:
    <x-account-navigation />
    <x-account-navigation layout="horizontal" />
    <x-account-navigation :show-icons="false" />
--}}

@if (!empty($menuItems))
  <nav
    {{ $attributes->merge(['class' => $layout === 'vertical' ? 'sticky top-8 rounded-xl border border-secondary-200 bg-white p-4' : '']) }}
    aria-label="{{ __('Account navigation', 'sage') }}"
  >
    <ul class="woocommerce-MyAccount-navigation {{ $getContainerClasses() }}">
      @foreach ($menuItems as $endpoint => $label)
        <li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--{{ esc_attr($endpoint) }}">
          <a
            href="{{ esc_url($getEndpointUrl($endpoint)) }}"
            class="{{ $getItemClasses($endpoint) }}"
            @if ($isActive($endpoint)) aria-current="page" @endif
          >
            @if ($showIcons)
              <span class="{{ $getIconClasses($endpoint) }}">
                {!! $getIcon($endpoint) !!}
              </span>
            @endif
            <span>{{ esc_html($label) }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  </nav>
@endif
