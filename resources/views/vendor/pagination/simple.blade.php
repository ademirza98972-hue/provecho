@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span>Sebelumnya</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
    @endif

    <span style="border-color:transparent">Hal. {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
    @else
        <span>Berikutnya</span>
    @endif
</div>
@endif
