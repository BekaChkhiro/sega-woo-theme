@props(['class' => ''])

<div
  {{ $attributes->merge(['class' => 'star-rating-component flex flex-wrap items-center ' . $containerClasses() . ' ' . $class]) }}
  @if ($hasRating())
    itemprop="aggregateRating"
    itemscope
    itemtype="http://schema.org/AggregateRating"
  @endif
>
  {{-- Star Display --}}
  <div class="flex items-center {{ $containerClasses() }}" role="img" aria-label="{{ sprintf(__('%s out of 5 stars', 'sega-woo-theme'), $formattedRating()) }}">
    {{-- Full Stars --}}
    @for ($i = 0; $i < $fullStars(); $i++)
      <svg class="{{ $starClasses() }} text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
    @endfor

    {{-- Half Star (if applicable) --}}
    @if ($hasHalfStar())
      <svg class="{{ $starClasses() }}" viewBox="0 0 20 20" aria-hidden="true">
        <defs>
          <linearGradient id="half-star-{{ $product?->get_id() ?? uniqid() }}">
            <stop offset="50%" stop-color="#facc15" />
            <stop offset="50%" stop-color="#d1d5db" />
          </linearGradient>
        </defs>
        <path
          fill="url(#half-star-{{ $product?->get_id() ?? uniqid() }})"
          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
        />
      </svg>
    @endif

    {{-- Rounded-up Full Star (for ratings >= X.75) --}}
    @php
      $decimal = $rating - floor($rating);
      $showRoundedStar = $decimal >= 0.75;
    @endphp
    @if ($showRoundedStar)
      <svg class="{{ $starClasses() }} text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
    @endif

    {{-- Empty Stars --}}
    @for ($i = 0; $i < $emptyStars(); $i++)
      <svg class="{{ $starClasses() }} text-secondary-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
      </svg>
    @endfor
  </div>

  {{-- Schema.org Metadata (hidden) --}}
  @if ($hasRating())
    <meta itemprop="ratingValue" content="{{ $formattedRating() }}" />
    <meta itemprop="bestRating" content="5" />
    <meta itemprop="worstRating" content="1" />
    <meta itemprop="ratingCount" content="{{ $count }}" />
    @if ($reviewCount > 0)
      <meta itemprop="reviewCount" content="{{ $reviewCount }}" />
    @endif
  @endif

  {{-- Rating Count Display --}}
  @if ($showCount && $hasRating())
    <span class="{{ $countClasses() }} text-secondary-500">
      ({{ $count }})
    </span>
  @endif

  {{-- Review Link --}}
  @if ($showLink && $reviewsEnabled())
    @if ($hasRating())
      <a href="{{ $linkUrl }}" class="{{ $linkClasses() }} text-primary-600 hover:text-primary-700 transition-colors">
        {{ $getLinkText() }}
      </a>
    @else
      <a href="{{ $linkUrl }}" class="{{ $linkClasses() }} text-primary-600 hover:text-primary-700 transition-colors">
        {{ $getLinkText() }}
      </a>
    @endif
  @endif
</div>

{{-- No Ratings State (optional - only if explicitly rendering component with no ratings) --}}
@if (!$hasRating() && !$showLink)
  {{-- Component shows empty stars by default, which is handled above --}}
@endif
