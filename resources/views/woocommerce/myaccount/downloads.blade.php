{{--
  Template: My Account Downloads
  Description: Displays the customer's available downloads
  @see woocommerce/templates/myaccount/downloads.php
  @version 7.8.0
--}}

@php
  // Variables passed by WooCommerce:
  // $downloads - array of download items
  // $has_downloads - boolean

  do_action('woocommerce_before_account_downloads', $has_downloads);
@endphp

@if ($has_downloads)
  {{-- Header --}}
  <div class="mb-6">
    <h2 class="text-xl font-semibold text-secondary-900">
      {{ __('Your Downloads', 'sage') }}
    </h2>
    <p class="mt-1 text-sm text-secondary-600">
      {{ __('Access your purchased digital products here.', 'sage') }}
    </p>
  </div>

  @php do_action('woocommerce_before_available_downloads'); @endphp

  {{-- Desktop Table View --}}
  <div class="hidden overflow-hidden rounded-xl border border-secondary-200 bg-white md:block">
    <table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details w-full">
      <thead>
        <tr class="border-b border-secondary-200 bg-secondary-50">
          @foreach (wc_get_account_downloads_columns() as $column_id => $column_name)
            <th
              scope="col"
              class="woocommerce-table__header woocommerce-table__header-{{ esc_attr($column_id) }} px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-secondary-600 {{ $column_id === 'download-actions' ? 'text-right' : '' }}"
            >
              <span class="nobr">{{ esc_html($column_name) }}</span>
            </th>
          @endforeach
        </tr>
      </thead>

      <tbody class="divide-y divide-secondary-100">
        @foreach ($downloads as $download)
          <tr class="transition-colors hover:bg-secondary-50">
            @foreach (wc_get_account_downloads_columns() as $column_id => $column_name)
              <td
                class="woocommerce-table__cell woocommerce-table__cell-{{ esc_attr($column_id) }} px-6 py-4 {{ $column_id === 'download-actions' ? 'text-right' : '' }}"
                data-title="{{ esc_attr($column_name) }}"
              >
                @if (has_action('woocommerce_account_downloads_column_' . $column_id))
                  @php do_action('woocommerce_account_downloads_column_' . $column_id, $download); @endphp

                @elseif ('download-product' === $column_id)
                  <div class="flex items-center gap-3">
                    {{-- Product Image --}}
                    @php
                      $product = wc_get_product($download['product_id']);
                      $thumbnail = $product ? $product->get_image(['48', '48'], ['class' => 'rounded-lg']) : '';
                    @endphp
                    @if ($thumbnail)
                      <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg border border-secondary-200 bg-secondary-100">
                        @if ($download['product_url'])
                          <a href="{{ esc_url($download['product_url']) }}">
                            {!! $thumbnail !!}
                          </a>
                        @else
                          {!! $thumbnail !!}
                        @endif
                      </div>
                    @endif

                    <div>
                      @if ($download['product_url'])
                        <a
                          href="{{ esc_url($download['product_url']) }}"
                          class="font-medium text-secondary-900 transition-colors hover:text-primary-600"
                        >
                          {{ esc_html($download['product_name']) }}
                        </a>
                      @else
                        <span class="font-medium text-secondary-900">{{ esc_html($download['product_name']) }}</span>
                      @endif

                      {{-- File name --}}
                      <p class="mt-0.5 text-sm text-secondary-500">{{ esc_html($download['file']['name']) }}</p>
                    </div>
                  </div>

                @elseif ('download-file' === $column_id)
                  <span class="text-sm text-secondary-700">{{ esc_html($download['file']['name']) }}</span>

                @elseif ('download-remaining' === $column_id)
                  @if (is_numeric($download['downloads_remaining']))
                    <span class="inline-flex items-center rounded-full bg-secondary-100 px-2.5 py-1 text-xs font-medium text-secondary-700">
                      {{ esc_html($download['downloads_remaining']) }} {{ __('remaining', 'sage') }}
                    </span>
                  @else
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                      {{ __('Unlimited', 'sage') }}
                    </span>
                  @endif

                @elseif ('download-expires' === $column_id)
                  @if (!empty($download['access_expires']))
                    <time
                      datetime="{{ esc_attr(date('Y-m-d', strtotime($download['access_expires']))) }}"
                      class="text-sm text-secondary-600"
                    >
                      {{ esc_html(date_i18n(get_option('date_format'), strtotime($download['access_expires']))) }}
                    </time>
                  @else
                    <span class="text-sm text-secondary-500">{{ __('Never', 'sage') }}</span>
                  @endif

                @elseif ('download-actions' === $column_id)
                  <a
                    href="{{ esc_url($download['download_url']) }}"
                    class="woocommerce-MyAccount-downloads-file button inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ wc_wp_theme_get_element_class_name('button') }}"
                  >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {{ esc_html($download['download_name']) }}
                  </a>
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Mobile Card View --}}
  <div class="space-y-4 md:hidden">
    @foreach ($downloads as $download)
      @php
        $product = wc_get_product($download['product_id']);
        $thumbnail = $product ? $product->get_image(['64', '64'], ['class' => 'rounded-lg']) : '';
      @endphp

      <div class="rounded-xl border border-secondary-200 bg-white p-4">
        {{-- Product Info --}}
        <div class="mb-4 flex items-start gap-4">
          @if ($thumbnail)
            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border border-secondary-200 bg-secondary-100">
              @if ($download['product_url'])
                <a href="{{ esc_url($download['product_url']) }}">
                  {!! $thumbnail !!}
                </a>
              @else
                {!! $thumbnail !!}
              @endif
            </div>
          @endif

          <div class="min-w-0 flex-1">
            @if ($download['product_url'])
              <a
                href="{{ esc_url($download['product_url']) }}"
                class="font-semibold text-secondary-900 transition-colors hover:text-primary-600"
              >
                {{ esc_html($download['product_name']) }}
              </a>
            @else
              <span class="font-semibold text-secondary-900">{{ esc_html($download['product_name']) }}</span>
            @endif

            <p class="mt-1 text-sm text-secondary-500">{{ esc_html($download['file']['name']) }}</p>
          </div>
        </div>

        {{-- Download Info --}}
        <div class="mb-4 grid grid-cols-2 gap-4 border-t border-secondary-100 pt-4 text-sm">
          <div>
            <span class="text-secondary-500">{{ __('Remaining:', 'sage') }}</span>
            @if (is_numeric($download['downloads_remaining']))
              <span class="ml-1 font-medium text-secondary-900">{{ esc_html($download['downloads_remaining']) }}</span>
            @else
              <span class="ml-1 font-medium text-green-600">{{ __('Unlimited', 'sage') }}</span>
            @endif
          </div>

          <div>
            <span class="text-secondary-500">{{ __('Expires:', 'sage') }}</span>
            @if (!empty($download['access_expires']))
              <span class="ml-1 font-medium text-secondary-900">
                {{ esc_html(date_i18n(get_option('date_format'), strtotime($download['access_expires']))) }}
              </span>
            @else
              <span class="ml-1 font-medium text-secondary-900">{{ __('Never', 'sage') }}</span>
            @endif
          </div>
        </div>

        {{-- Download Button --}}
        <a
          href="{{ esc_url($download['download_url']) }}"
          class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition-all hover:bg-primary-700"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          {{ __('Download', 'sage') }}
        </a>
      </div>
    @endforeach
  </div>

  @php do_action('woocommerce_after_available_downloads'); @endphp

@else
  {{-- Empty State --}}
  <div class="flex flex-col items-center justify-center py-12 text-center">
    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-secondary-100">
      <svg class="h-10 w-10 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
      </svg>
    </div>
    <h3 class="mb-2 text-lg font-semibold text-secondary-900">
      {{ __('No downloads yet', 'sage') }}
    </h3>
    <p class="mb-6 max-w-sm text-secondary-600">
      {{ __("You haven't purchased any downloadable products yet. Browse our products to find digital items.", 'sage') }}
    </p>
    <a
      href="{{ esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))) }}"
      class="woocommerce-Button wc-forward button inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98]"
    >
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
      </svg>
      {{ __('Browse products', 'sage') }}
    </a>
  </div>
@endif

@php do_action('woocommerce_after_account_downloads', $has_downloads); @endphp
