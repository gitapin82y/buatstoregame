@props(['type' => 'primary', 'size' => '', 'outline' => false])

@php
$buttonClass = 'btn';

if ($outline) {
    $buttonClass .= ' btn-outline-' . $type;
} else {
    $buttonClass .= ' btn-' . $type;
}

if ($size) {
    $buttonClass .= ' btn-' . $size;
}
@endphp

<button {{ $attributes->merge(['class' => $buttonClass, 'type' => 'button']) }}>
    {{ $slot }}
</button>