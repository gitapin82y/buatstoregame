<div {{ $attributes->merge(['class' => 'card shadow-sm mb-4']) }}>
    @isset($header)
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ $header }}</h5>
            @isset($headerActions)
                <div>
                    {{ $headerActions }}
                </div>
            @endisset
        </div>
    @endisset
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>