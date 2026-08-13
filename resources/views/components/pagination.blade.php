@props(['links' => null])

@if ($links && $links->hasPages())
    <div class="mt-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
        <p class="text-sm text-charcoal-500">
            Showing {{ $links->firstItem() }} – {{ $links->lastItem() }} of {{ $links->total() }} entries
        </p>
        <div class="flex items-center gap-1">
            {{ $links->links() }}
        </div>
    </div>
@endif
