@props(['type' => 'primary'])

<span {{ $attributes->merge(['class' => 'badge bg-' . $type]) }}>
    {{ $slot }}
</span>

<!-- resources/views/components/input.blade.php -->
@props(['disabled' => false, 'error' => ''])

<div class="mb-3">
    @isset($label)
        <label for="{{ $attributes->get('id') }}" class="form-label">{{ $label }}</label>
    @endisset
    
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control' . ($error ? ' is-invalid' : '')]) !!}>
    
    @if($error)
        <div class="invalid-feedback">
            {{ $error }}
        </div>
    @endif
    
    @isset($help)
        <div class="form-text">{{ $help }}</div>
    @endisset
</div>