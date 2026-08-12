@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'shadow' => 'sm',
    'border' => true,
    'headerClass' => 'bg-white py-3',
    'bodyClass' => 'p-4',
])

<div {{ $attributes->merge(['class' => 'card ' . ($border ? '' : 'border-0 ') . 'shadow-' . $shadow . ' mb-4']) }}>
    @if($title || isset($headerActions))
        <div class="card-header {{ $headerClass }} d-flex justify-content-between align-items-center">
            <div>
                @if($title)
                    <h5 class="fw-bold mb-0 text-primary">
                        @if($icon) <i class="bi {{ $icon }} me-2"></i> @endif
                        {{ $title }}
                    </h5>
                @endif
                @if($subtitle)
                    <p class="text-muted small mb-0 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($headerActions))
                <div>
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer bg-light px-4 py-3">
            {{ $footer }}
        </div>
    @endif
</div>
