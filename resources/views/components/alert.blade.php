@props([
  'type' => null,
  'message' => null,
])

@php($class = match ($type) {
  'success' => 'border-success-200 bg-success-50 text-success-800',
  'caution' => 'border-warning-200 bg-warning-50 text-warning-800',
  'warning' => 'border-error-200 bg-error-50 text-error-800',
  'info' => 'border-primary-200 bg-primary-50 text-primary-800',
  default => 'border-secondary-200 bg-secondary-50 text-secondary-800',
})

<div {{ $attributes->merge(['class' => "rounded-lg border px-4 py-3 {$class}"]) }} role="alert">
  {!! $message ?? $slot !!}
</div>
