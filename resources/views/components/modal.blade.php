<!-- resources/views/components/modal.blade.php -->
@props(['id', 'title', 'size' => '', 'staticBackdrop' => false])

@php
$modalClass = 'modal-dialog';
if ($size) {
    $modalClass .= ' modal-' . $size;
}
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" {{ $staticBackdrop ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '' }}>
    <div class="{{ $modalClass }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @else
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            @endisset
        </div>
    </div>
</div>