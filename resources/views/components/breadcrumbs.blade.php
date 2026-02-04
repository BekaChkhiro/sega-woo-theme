@props([
  'items' => [],
  'showSchema' => true,
  'showHomeIcon' => true,
])

@if (!empty($items))
  {{-- Breadcrumb Navigation --}}
  <nav aria-label="{{ __('Breadcrumb', 'sage') }}" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-2 text-sm" role="list">
      @foreach ($items as $index => $crumb)
        <li class="flex items-center">
          @if ($index > 0)
            {{-- Separator --}}
            <svg
              class="mx-2 h-4 w-4 flex-shrink-0 text-secondary-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          @endif

          @if (!empty($crumb['url']))
            <a
              href="{{ $crumb['url'] }}"
              class="inline-flex items-center gap-1.5 text-secondary-600 transition-colors hover:text-primary-600 hover:underline"
            >
              @if ($index === 0 && $showHomeIcon)
                {{-- Home icon for first item --}}
                <svg
                  class="h-4 w-4 flex-shrink-0"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                  aria-hidden="true"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="sr-only sm:not-sr-only">{{ $crumb['label'] }}</span>
              @else
                {{ $crumb['label'] }}
              @endif
            </a>
          @else
            {{-- Current page (no link) --}}
            <span class="font-medium text-secondary-900" aria-current="page">
              {{ $crumb['label'] }}
            </span>
          @endif
        </li>
      @endforeach
    </ol>
  </nav>

  {{-- Schema.org Structured Data for SEO --}}
  @if ($showSchema)
    <script type="application/ld+json">
      {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->map(function ($crumb, $index) {
          $item = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['label'],
          ];

          if (!empty($crumb['url'])) {
            $item['item'] = $crumb['url'];
          }

          return $item;
        })->values()->all(),
      ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
  @endif
@endif
