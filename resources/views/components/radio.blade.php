@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'selected' => null,
    'options' => [],
    'inline' => true,
    'required' => false,
    'disabled' => false,
    'col' => 12,
    'helpText' => null,
])

@php
    $fieldId = $id ?? 'field_' . \Illuminate\Support\Str::slug($name, '_');
    $selectedValue = old($name, $value ?? $selected);
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div class="col-md-{{ $col }}">
    @if($label)
        <label class="form-label fw-semibold d-block" style="font-size:0.85rem;">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="pt-1">
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
                $radId = $fieldId . '_rad_' . \Illuminate\Support\Str::slug($val, '_');
                $isRadSel = (string)$val === (string)$selectedValue;
            @endphp
            <div class="form-check {{ $inline ? 'form-check-inline' : 'mb-2' }}">
                <input 
                    type="radio" 
                    id="{{ $radId }}" 
                    name="{{ $name }}" 
                    value="{{ $val }}" 
                    {{ $isRadSel ? 'checked' : '' }}
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    {{ $attributes->merge(['class' => 'form-check-input']) }}
                >
                <label class="form-check-label" for="{{ $radId }}">{{ $lbl }}</label>
            </div>
        @endforeach
    </div>

    @if($helpText)
        <div class="form-text text-muted small">{{ $helpText }}</div>
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block">{{ $errors->first($name) }}</div>
    @endif
</div>
