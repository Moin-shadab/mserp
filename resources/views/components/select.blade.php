@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'selected' => null,
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'col' => 12,
    'icon' => null,
    'helpText' => null,
])

@php
    $fieldId = $id ?? 'field_' . \Illuminate\Support\Str::slug($name, '_');
    $selectedValue = old($name, $value ?? $selected);
    $defaultPlaceholder = $placeholder ?? '-- Select ' . ($label ?? 'Option') . ' --';
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

    <select 
        id="{{ $fieldId }}" 
        name="{{ $name }}" 
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => 'form-select' . ($hasError ? ' is-invalid' : '')]) }}
    >
        @if($defaultPlaceholder !== false)
            <option value="">{{ $defaultPlaceholder }}</option>
        @endif

        @foreach($options as $optKey => $optVal)
            @php
                if (is_object($optVal)) {
                    $val = $optVal->value ?? $optVal->id ?? $optKey;
                    $lbl = $optVal->label ?? $optVal->name ?? $val;
                } elseif (is_array($optVal)) {
                    $val = $optVal['value'] ?? $optVal['id'] ?? $optKey;
                    $lbl = $optVal['label'] ?? $optVal['name'] ?? $val;
                } else {
                    $val = is_numeric($optKey) ? $optVal : $optKey;
                    $lbl = $optVal;
                }
                $isSel = (string)$val === (string)$selectedValue;
            @endphp
            <option value="{{ $val }}" {{ $isSel ? 'selected' : '' }}>{{ $lbl }}</option>
        @endforeach
    </select>

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
