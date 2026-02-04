@if ($hasPrice())
  <div {{ $attributes->merge(['class' => 'price-component']) }}>
    @if ($isVariable() && $hasPriceRange())
      {{-- Variable Product with Price Range --}}
      @php $range = $priceRange() @endphp
      <div class="flex flex-wrap items-baseline gap-1">
        <span class="{{ $priceClasses() }} text-secondary-900">
          {!! $range['min_formatted'] !!}
        </span>
        <span class="{{ $priceClasses() }} text-secondary-400">&ndash;</span>
        <span class="{{ $priceClasses() }} text-secondary-900">
          {!! $range['max_formatted'] !!}
        </span>
      </div>

      {{-- Show sale badge if any variation is on sale --}}
      @if ($showBadge && $isOnSale() && $salePercentage() > 0)
        <span class="{{ $badgeClasses() }} mt-1 inline-flex items-center gap-1 rounded-full bg-red-100 font-semibold text-red-700">
          {{ sprintf(__('Up to %d%% off', 'sage'), $salePercentage()) }}
        </span>
      @endif

    @elseif ($isOnSale() && $regularPrice())
      {{-- Simple Product On Sale --}}
      <div class="flex flex-wrap items-baseline gap-2">
        {{-- Crossed-out Regular Price --}}
        <span class="{{ $regularPriceClasses() }} text-secondary-400 line-through">
          {!! $regularPrice() !!}
        </span>

        {{-- Sale Price --}}
        <span class="{{ $priceClasses() }} text-secondary-900">
          {!! $salePrice() ?: $currentPrice() !!}
        </span>

        {{-- Sale Percentage Badge --}}
        @if ($showBadge && $salePercentage() > 0)
          <span class="{{ $badgeClasses() }} rounded-full bg-red-100 font-semibold text-red-700">
            {{ sprintf(__('Save %d%%', 'sage'), $salePercentage()) }}
          </span>
        @endif
      </div>

    @else
      {{-- Regular Price (no sale) --}}
      <span class="{{ $priceClasses() }} text-secondary-900">
        {!! $currentPrice() !!}
      </span>
    @endif
  </div>
@else
  {{-- No Price Set --}}
  <div {{ $attributes->merge(['class' => 'price-component']) }}>
    <span class="{{ $priceClasses() }} text-secondary-500">
      {{ __('Price not available', 'sage') }}
    </span>
  </div>
@endif
