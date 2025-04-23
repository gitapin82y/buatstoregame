<!-- resources/views/components/select.blade.php -->
@props(['disabled' => false, 'error' => ''])

<div class="mb-3">
    @isset($label)
        <label for="{{ $attributes->get('id') }}" class="form-label">{{ $label }}</label>
    @endisset
    
    <select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-select' . ($error ? ' is-invalid' : '')]) !!}>
        {{ $slot }}
    </select>
    
    @if($error)
        <div class="invalid-feedback">
            {{ $error }}
        </div>
    @endif
    
    @isset($help)
        <div class="form-text">{{ $help }}</div>
    @endisset
</div>