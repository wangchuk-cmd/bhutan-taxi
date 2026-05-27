@php
    $flashMessages = collect([
        'success' => session('success'),
        'error' => session('error'),
        'info' => session('info'),
    ])->filter();
@endphp

@if($flashMessages->isNotEmpty())
    <div class="system-flash-stack">
        @foreach($flashMessages as $type => $message)
            <div class="system-flash-card system-flash-{{ $type }}" role="alert">
                <div class="system-flash-icon">
                    @if($type === 'success')
                        <i class="bi bi-check-circle-fill"></i>
                    @elseif($type === 'error')
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    @else
                        <i class="bi bi-info-circle-fill"></i>
                    @endif
                </div>
                <div class="system-flash-body">
                    <div class="system-flash-title">
                        {{ ucfirst($type) }}
                    </div>
                    <div class="system-flash-message">{{ $message }}</div>
                </div>
                <button type="button" class="system-flash-close" aria-label="Dismiss" data-system-flash-close>
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-system-flash-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            const card = button.closest('.system-flash-card');
            if (card) {
                card.classList.add('system-flash-hide');
                setTimeout(function () {
                    card.remove();
                }, 220);
            }
        });
    });
});
</script>