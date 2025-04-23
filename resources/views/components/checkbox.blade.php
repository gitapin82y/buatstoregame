<!-- resources/views/components/checkbox.blade.php -->
@props(['disabled' => false, 'error' => ''])

<div class="mb-3 form-check">
    <input type="checkbox" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-check-input' . ($error ? ' is-invalid' : '')]) !!}>
    
    @isset($label)
        <label class="form-check-label" for="{{ $attributes->get('id') }}">{{ $label }}</label>
    @endisset
    
    @if($error)
        <div class="invalid-feedback">
            {{ $error }}
        </div>
    @endif
</div>