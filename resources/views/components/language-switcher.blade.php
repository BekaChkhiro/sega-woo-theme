@if ($hasMultipleLanguages())
    <div
        x-data="{ langOpen: false }"
        @click.away="langOpen = false"
        class="relative"
        {{ $attributes }}
    >
        {{-- Language Toggle Button --}}
        <button
            @click="langOpen = !langOpen"
            type="button"
            class="flex h-10 items-center gap-1.5 rounded-full px-3 text-sm font-semibold text-secondary-700 transition-colors hover:bg-secondary-100 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
            :class="{ 'bg-secondary-100 text-secondary-900': langOpen }"
            :aria-expanded="langOpen"
            aria-haspopup="true"
            aria-label="{{ __('Select language', 'sega-woo-theme') }}"
        >
            <span class="uppercase">{{ $currentLanguage()['display_code'] }}</span>
            <svg
                class="h-4 w-4 transition-transform duration-200"
                :class="{ 'rotate-180': langOpen }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Language Dropdown --}}
        <div
            x-show="langOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute right-0 top-full z-dropdown mt-2 min-w-32 overflow-hidden rounded-xl border border-secondary-200 bg-white py-1 shadow-lg"
            x-cloak
        >
            @foreach ($languages() as $lang)
                <a
                    href="{{ $lang['url'] }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $lang['active'] ? 'bg-primary-50 font-semibold text-primary-700' : 'text-secondary-700 hover:bg-secondary-50 hover:text-secondary-900' }}"
                    @if ($lang['active']) aria-current="true" @endif
                >
                    <span class="w-8 font-semibold uppercase {{ $lang['active'] ? 'text-primary-600' : 'text-secondary-500' }}">
                        {{ $lang['display_code'] }}
                    </span>
                    <span>{{ $lang['name'] }}</span>
                    @if ($lang['active'])
                        <svg class="ml-auto h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endif
