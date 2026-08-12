@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => null,
    'icon' => null,
    'loadingText' => null,
    'disabled' => false,
    'onclick' => null,
])

@php
    $sizeClass = $size ? 'btn-' . $size : '';
    $variantClass = 'btn-' . $variant;
@endphp

<button 
    type="{{ $type }}" 
    @if($onclick) onclick="{{ $onclick }}" @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => 'btn ' . $variantClass . ' ' . $sizeClass . ' d-inline-flex align-items-center gap-2']) }}
    @if($loadingText) data-loading-text="{{ $loadingText }}" @endif
>
    @if($icon)
        <i class="bi {{ $icon }}"></i>
    @endif
    <span>{{ $slot }}</span>
</button>
