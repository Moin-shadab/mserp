@props([
    'name',
    'id' => null,
    'label',
    'value' => 1,
    'checked' => false,
    'switch' => true,
    'disabled' => false,
    'col' => 12,
    'helpText' => null,
])

@php
    $fieldId = $id ?? 'field_' . \Illuminate\Support\Str::slug($name, '_');
    $isChecked = old($name, $checked) == $value || old($name, $checked) === true || old($name, $checked) === '1';
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div class="col-md-{{ $col }}">
    <div class="form-check {{ $switch ? 'form-switch' : '' }} pt-1">
        <input 
            type="checkbox" 
            id="{{ $fieldId }}" 
            name="{{ $name }}" 
            value="{{ $value }}" 
            {{ $switch ? 'role=switch' : '' }}
            {{ $isChecked ? 'checked' : '' }}
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => 'form-check-input']) }}
        >
        <label class="form-check-label fw-semibold" for="{{ $fieldId }}" style="font-size:0.875rem;">
            {{ $label }}
        </label>
    </div>

    @if($helpText)
        <div class="form-text text-muted small ms-4">{{ $helpText }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block ms-4">{{ $errors->first($name) }}</div>
    @endif
</div>
