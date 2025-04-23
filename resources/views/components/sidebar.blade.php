@props(['active' => false])

@php
$classes = $active ? 'list-group-item list-group-item-action bg-transparent text-white active' : 'list-group-item list-group-item-action bg-transparent text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>