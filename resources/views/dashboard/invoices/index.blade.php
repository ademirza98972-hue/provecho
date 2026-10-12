@extends('layouts.app')
@section('title', 'Nota')
@section('content')

<div class="panel">
    <div class="panel-head">
        <form method="GET" class="toolbar-group search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor nota, nama, atau HP" aria-label="Cari nota">
        </form>
        <a href="{{ route('dashboard.invoices.create') }}" class="btn btn-primary btn-sm">+ Buat nota</a>
    </div>
    <div class="table-wrap">
        <table class="inv-table">
            <thead><tr><th>No. nota</th><th>Tanggal</th><th>Pembeli</th><th>Card</th><th class="num">Total</th>@can('admin')<th>Dibuat oleh</th>@endcan</tr></thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="c-num"><a href="{{ route('dashboard.invoices.show', $inv) }}" class="mono link">{{ $inv->number }}</a></td>
                    <td class="c-date hint">{{ $inv->created_at->format('d/m/Y') }}</td>
                    <td class="c-buyer">{{ $inv->buyer_name }}</td>
                    <td class="c-count">{{ $inv->items->count() }}<span class="m-only"> card</span></td>
                    <td class="c-total num">{{ \App\Models\Invoice::rupiah($inv->total) }}</td>
                    @can('admin')<td class="c-user hint">{{ $inv->user?->name }}</td>@endcan
                </tr>
                @empty
                <tr><td colspan="6" class="empty">Belum ada nota.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="panel-head" style="border-top:1px solid var(--border);border-bottom:0">{{ $invoices->links('vendor.pagination.simple') }}</div>
    @endif
</div>

@push('styles')
<style>
.search-form input { width: 260px; }
.inv-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.inv-table .mono { white-space: nowrap; }
.m-only { display: none; }

/* HP: tiap nota jadi kartu ringkas, tanpa geser ke samping. */
@media (max-width: 600px) {
    .search-form { flex: 1; }
    .search-form input { width: 100%; }
    .inv-table thead { display: none; }
    .inv-table tbody tr:not(:has(.empty)) {
        display: grid; grid-template-columns: 1fr auto; gap: 2px 12px;
        padding: 12px 16px; border-bottom: 1px solid #F3F4F6;
    }
    .inv-table tbody td { padding: 0; border: 0; }
    .inv-table .c-num, .inv-table .c-buyer, .inv-table .c-user { grid-column: 1; }
    .inv-table .c-date, .inv-table .c-count, .inv-table .c-total { grid-column: 2; text-align: right; }
    .inv-table .c-date { grid-row: 1; }
    .inv-table .c-buyer { grid-row: 2; font-weight: 600; }
    .inv-table .c-total { grid-row: 2; font-weight: 700; }
    .inv-table .c-count { grid-row: 3; font-size: 12px; color: var(--muted); }
    .inv-table .c-user { grid-row: 3; font-size: 12px; }
    .m-only { display: inline; }
}
</style>
@endpush

@endsection
