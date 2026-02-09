{{--
  Product Additional Information Tab Content

  Displays product attributes, weight, and dimensions in a table format.

  @param array $attributes - Array of visible product attributes
  @param string $weight - Product weight with unit
  @param string $dimensions - Product dimensions formatted string
  @param bool $hasWeight - Whether product has weight
  @param bool $hasDimensions - Whether product has dimensions
--}}

<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--additional_information">
  @if (!empty($attributes) || $hasWeight || $hasDimensions)
    <table class="woocommerce-product-attributes shop_attributes w-full text-left text-sm">
      <tbody class="divide-y divide-secondary-200">
        {{-- Product Attributes --}}
        @foreach ($attributes as $attribute)
          <tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--{{ $attribute['slug'] ?? 'attribute' }}">
            <th
              scope="row"
              class="woocommerce-product-attributes-item__label w-1/3 py-4 pr-4 align-top font-medium text-secondary-900"
            >
              {{ $attribute['name'] }}
            </th>
            <td class="woocommerce-product-attributes-item__value py-4 text-secondary-600">
              @if (!empty($attribute['values']))
                @foreach ($attribute['values'] as $index => $value)
                  @if (!empty($value['url']))
                    <a
                      href="{{ $value['url'] }}"
                      class="text-primary-600 transition-colors hover:text-primary-700 hover:underline"
                    >
                      {{ $value['name'] }}
                    </a>
                  @else
                    <span>{{ $value['name'] }}</span>
                  @endif
                  @if ($index < count($attribute['values']) - 1)
                    <span class="text-secondary-400">,</span>
                  @endif
                @endforeach
              @else
                <span class="text-secondary-400">{{ __('N/A', 'sega-woo-theme') }}</span>
              @endif
            </td>
          </tr>
        @endforeach

        {{-- Weight --}}
        @if ($hasWeight)
          <tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--weight">
            <th
              scope="row"
              class="woocommerce-product-attributes-item__label w-1/3 py-4 pr-4 align-top font-medium text-secondary-900"
            >
              {{ __('Weight', 'sega-woo-theme') }}
            </th>
            <td class="woocommerce-product-attributes-item__value py-4 text-secondary-600">
              {{ $weight }}
            </td>
          </tr>
        @endif

        {{-- Dimensions --}}
        @if ($hasDimensions)
          <tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--dimensions">
            <th
              scope="row"
              class="woocommerce-product-attributes-item__label w-1/3 py-4 pr-4 align-top font-medium text-secondary-900"
            >
              {{ __('Dimensions', 'sega-woo-theme') }}
            </th>
            <td class="woocommerce-product-attributes-item__value py-4 text-secondary-600">
              {{ $dimensions }}
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  @else
    <p class="text-secondary-500 italic">
      {{ __('No additional information available for this product.', 'sega-woo-theme') }}
    </p>
  @endif
</div>
