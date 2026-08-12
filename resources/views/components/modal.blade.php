@props([
    'id',
    'title',
    'size' => 'lg',
    'centered' => true,
    'scrollable' => false,
    'formId' => null,
    'onsubmit' => null,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="{{ $id }}-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @if($formId)
                <form id="{{ $formId }}" @if($onsubmit) onsubmit="{{ $onsubmit }}" @endif>
            @endif

            <div class="modal-body p-4" id="{{ $id }}-body">
                {{ $slot }}
            </div>

            <div class="modal-footer bg-light px-4">
                @if(isset($footer))
                    {{ $footer }}
                @else
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Save Changes</button>
                @endif
            </div>

            @if($formId)
                </form>
            @endif
        </div>
    </div>
</div>
