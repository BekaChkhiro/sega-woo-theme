<div
  {{ $attributes->merge(['class' => 'relative h-full min-h-0 rounded-xl border border-secondary-200 bg-white shadow-sm flex flex-col']) }}
  x-data="{
    activeItem: null,
    items: @js($items),
    getActiveItem() {
      return this.items.find(i => i.id === this.activeItem);
    },
    setActive(id) {
      this.activeItem = id;
    },
    clearActive() {
      this.activeItem = null;
    },
    isActive(id) { return this.activeItem === id }
  }"
  @mouseleave="clearActive()"
>
    {{-- Header --}}
    <div class="flex items-center gap-2 border-b border-secondary-100 bg-secondary-50/50 px-4 py-3">
    <svg class="h-5 w-5 text-secondary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <h3 class="text-sm font-semibold text-secondary-900">
      {{ $title }}
    </h3>
  </div>

  @if ($hasItems())
    {{-- Items List with Flyout Container --}}
    <div class="relative flex min-h-0 flex-1" style="overflow: visible;">
      {{-- Main Items List with scroll --}}
      <nav class="flex-1 overflow-y-auto overscroll-contain" aria-label="{{ $title }}">
        <ul>
          @foreach ($items as $index => $item)
            <li>
              <a
                href="{{ $item['link'] }}"
                @if (!empty($item['target'])) target="{{ $item['target'] }}" @endif
                class="group flex items-center justify-between px-4 py-2.5 text-sm text-secondary-700 transition-colors hover:bg-primary-50 hover:text-primary-700"
                :class="{ 'bg-primary-50 text-primary-700': isActive({{ $item['id'] }}) }"
                @mouseenter="setActive({{ $item['id'] }})"
              >
                <span class="flex items-center gap-3">
                  {{-- Item Icon/Thumbnail --}}
                  @if (!empty($item['thumbnail']))
                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-secondary-100">
                      <img
                        src="{{ $item['thumbnail'] }}"
                        alt=""
                        class="h-full w-full object-cover"
                        loading="lazy"
                      >
                    </span>
                  @elseif (!empty($item['icon']))
                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-secondary-100 text-secondary-500 transition-colors group-hover:bg-primary-100 group-hover:text-primary-600" :class="{ 'bg-primary-100 text-primary-600': isActive({{ $item['id'] }}) }">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                      </svg>
                    </span>
                  @else
                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-secondary-100 text-secondary-500 transition-colors group-hover:bg-primary-100 group-hover:text-primary-600" :class="{ 'bg-primary-100 text-primary-600': isActive({{ $item['id'] }}) }">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                      </svg>
                    </span>
                  @endif

                  <span class="font-medium">{{ $item['name'] }}</span>
                </span>

                <span class="flex items-center gap-1.5">
                  @if ($showProductCount && isset($item['count']))
                    <span class="text-xs text-secondary-400 transition-colors group-hover:text-primary-500" :class="{ 'text-primary-500': isActive({{ $item['id'] }}) }">
                      ({{ $item['count'] }})
                    </span>
                  @endif

                  @if (!empty($item['hasChildren']))
                    <svg
                      class="h-4 w-4 text-secondary-400 transition-colors group-hover:text-primary-500"
                      :class="{ 'text-primary-500': isActive({{ $item['id'] }}) }"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                  @endif
                </span>
              </a>
            </li>
          @endforeach
        </ul>
      </nav>

      {{-- Flyout Panel (Absolute positioning - outside nav but inside parent) --}}
      <div
        x-show="activeItem && getActiveItem()?.hasChildren"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-x-2"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-2"
        class="absolute left-full top-0 z-[100] h-full w-72 rounded-r-xl border border-l-0 border-secondary-200 bg-white shadow-xl"
        style="display: none; margin-left: -1px;"
        @mouseenter="$event.stopPropagation()"
        @mouseleave="clearActive()"
      >
        {{-- Flyout Header --}}
        <div class="border-b border-secondary-100 px-4 py-3">
          <a
            :href="getActiveItem()?.link"
            class="flex items-center justify-between text-sm font-semibold text-secondary-900 transition-colors hover:text-primary-600"
          >
            <span>
              {{ __('All in', 'sega-woo-theme') }}
              <span x-text="getActiveItem()?.name"></span>
            </span>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>

        {{-- Children Grid --}}
        <div class="h-[calc(100%-52px)] overflow-y-auto p-3">
          <ul class="grid grid-cols-1 gap-1">
            <template x-for="child in getActiveItem()?.children" :key="child.id">
              <li>
                <a
                  :href="child.link"
                  :target="child.target || '_self'"
                  class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-secondary-600 transition-colors hover:bg-secondary-50 hover:text-primary-600"
                >
                  <template x-if="child.thumbnail">
                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-secondary-100">
                      <img
                        :src="child.thumbnail"
                        alt=""
                        class="h-full w-full object-cover"
                        loading="lazy"
                      >
                    </span>
                  </template>
                  <template x-if="!child.thumbnail">
                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-secondary-100 text-secondary-400 transition-colors group-hover:bg-primary-50 group-hover:text-primary-500">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                    </span>
                  </template>

                  <span class="flex flex-col min-w-0">
                    <span class="font-medium truncate" x-text="child.name"></span>
                    @if ($showProductCount)
                      <template x-if="child.count !== null && child.count !== undefined">
                        <span class="text-xs text-secondary-400" x-text="`${child.count} {{ __('products', 'sega-woo-theme') }}`"></span>
                      </template>
                    @endif
                  </span>
                </a>
              </li>
            </template>
          </ul>
        </div>
      </div>

    </div>

    {{-- Footer: View All Link (optional) --}}
    @if ($showViewAll)
      <div class="border-t border-secondary-100 p-3">
        <a
          href="{{ $shopUrl() }}"
          class="flex items-center justify-center gap-2 rounded-lg border border-secondary-200 bg-secondary-50 px-4 py-2.5 text-sm font-medium text-secondary-700 transition-colors hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
          {{ __('View All', 'sega-woo-theme') }}
        </a>
      </div>
    @endif
  @else
    {{-- Empty State --}}
    <div class="flex flex-1 flex-col items-center justify-center p-6 text-center">
      <svg class="mb-3 h-12 w-12 text-secondary-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <p class="text-sm text-secondary-500">
        {{ __('No items found', 'sega-woo-theme') }}
      </p>
      <a
        href="{{ $shopUrl() }}"
        class="mt-3 text-sm font-medium text-primary-600 transition-colors hover:text-primary-700"
      >
        {{ __('Browse all products', 'sega-woo-theme') }}
      </a>
    </div>
  @endif
</div>
