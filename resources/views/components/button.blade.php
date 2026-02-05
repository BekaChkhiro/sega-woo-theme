@props([
  'variant' => 'primary',
  'size' => 'md',
  'type' => 'button',
  'fullWidth' => false,
  'rounded' => false,
  'loading' => false,
  'disabled' => false,
  'href' => null,
  'iconLeft' => null,
  'iconRight' => null,
])

@if ($isLink())
  <a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => $allClasses()]) }}
    @if ($disabled) aria-disabled="true" tabindex="-1" @endif
  >
    @if ($iconLeft)
      <span class="{{ $iconSizeClasses() }} flex-shrink-0">{!! $iconLeft !!}</span>
    @endif

    {{ $slot }}

    @if ($iconRight)
      <span class="{{ $iconSizeClasses() }} flex-shrink-0">{!! $iconRight !!}</span>
    @endif
  </a>
@else
  <button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $allClasses()]) }}
    @if ($disabled) disabled @endif
  >
    @if ($loading)
      <svg class="{{ $iconSizeClasses() }} flex-shrink-0 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    @elseif ($iconLeft)
      <span class="{{ $iconSizeClasses() }} flex-shrink-0">{!! $iconLeft !!}</span>
    @endif

    <span @if ($loading) class="opacity-0" @endif>{{ $slot }}</span>

    @if (!$loading && $iconRight)
      <span class="{{ $iconSizeClasses() }} flex-shrink-0">{!! $iconRight !!}</span>
    @endif
  </button>
@endif
