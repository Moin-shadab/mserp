@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'col' => 12,
    'icon' => null,
    'helpText' => null,
    'step' => null,
    'min' => null,
    'max' => null,
    'autocomplete' => null,
])

@php
    $fieldId = $id ?? 'field_' . \Illuminate\Support\Str::slug($name, '_');
    $val = old($name, $value);
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div class="col-md-{{ $col }}">
    @if($label)
        <label for="{{ $fieldId }}" class="form-label fw-semibold" style="font-size:0.85rem;">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    @if($icon)
        <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="bi {{ $icon }}"></i></span>
    @endif

    <input 
        type="{{ $type }}" 
        id="{{ $fieldId }}" 
        name="{{ $name }}" 
        value="{{ $val }}" 
        placeholder="{{ $placeholder }}" 
        @if($step) step="{{ $step }}" @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($required) required @endif
        @if($readonly) readonly @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}
    >

    @if($icon)
        </div>
    @endif

    @if($helpText)
        <div class="form-text text-muted small">{{ $helpText }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @endif
</div>
