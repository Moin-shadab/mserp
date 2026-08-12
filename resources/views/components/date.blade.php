@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'col' => 12,
    'min' => null,
    'max' => null,
    'helpText' => null,
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

    <div class="input-group">
        <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-event"></i></span>
        <input 
            type="date" 
            id="{{ $fieldId }}" 
            name="{{ $name }}" 
            value="{{ $val }}" 
            @if($min) min="{{ $min }}" @endif
            @if($max) max="{{ $max }}" @endif
            @if($required) required @endif
            @if($readonly) readonly @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'form-control' . ($hasError ? ' is-invalid' : '')]) }}
        >
    </div>

    @if($helpText)
        <div class="form-text text-muted small">{{ $helpText }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @endif
</div>
