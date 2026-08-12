@props([
    'variant' => 'primary',
    'pill' => true,
    'icon' => null,
])

<span {{ $attributes->merge(['class' => 'badge bg-' . $variant . ($pill ? ' rounded-pill' : '') . ' px-2.5 py-1.5 d-inline-flex align-items-center gap-1']) }}>
    @if($icon)
        <i class="bi {{ $icon }}"></i>
    @endif
    <span>{{ $slot }}</span>
</span>
