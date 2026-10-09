@extends('layouts.app')
@section('title', 'Statistik Scan')
@section('content')

@php
    $rangeLabel = \App\Support\ScanRange::OPTIONS[$days];
    $sortLink = function (string $key, string $label, string $firstDir = 'desc') use ($sort, $dir) {
        $next = $sort === $key ? ($dir === 'asc' ? 'desc' : 'asc') : $firstDir;
        $arrow = $sort === $key ? ($dir === 'asc' ? ' ↑' : ' ↓') : '';
        $url = route('dashboard.stats', array_merge(request()->except('page'), ['sort' => $key, 'dir' => $next]));
        return '<a href="' . e($url) . '" class="sort-link' . ($sort === $key ? ' on' : '') . '">' . e($label) . $arrow . '</a>';
    };
@endphp

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Statistik scan</h2>
            <p class="page-sub">Detail scan disimpan 6 bulan. Total scan tetap menghitung scan yang lebih lama.</p>
        </div>
        @include('dashboard._range-pills', ['days' => $days, 'route' => 'dashboard.stats'])
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <span class="kpi-label">Scan {{ $rangeLabel }}</span>
            <span class="kpi-value">{{ number_format($rangeScans) }}</span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Rata-rata per hari</span>
            <span class="kpi-value">{{ number_format($rangeScans / $days, 1, ',', '.') }}</span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Usaha aktif</span>
            <span class="kpi-value">{{ number_format($activeCards) }}</span>
        </div>
        <div class="kpi">
            <span class="kpi-label">Total scan</span>
            <span class="kpi-value">{{ number_format($totalScans) }}</span>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Scan per hari · {{ $rangeLabel }} terakhir</span>
        </div>
        <div class="panel-body chart-box">
            @include('dashboard._scan-chart', ['id' => 'statsChart', 'labels' => $chartLabels, 'values' => $chartValues])
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Scan per usaha</span>
            @can('admin')
            <a href="{{ route('dashboard.stats.export') }}" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                Export CSV
            </a>
            @endcan
        </div>
        <div class="table-wrap">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>{!! $sortLink('name', 'Usaha', 'asc') !!}</th>
                        <th class="num">{!! $sortLink('range', 'Scan ' . $rangeLabel) !!}</th>
                        <th class="num">{!! $sortLink('total', 'Total scan') !!}</th>
                        <th class="col-hide">{!! $sortLink('last_scan', 'Scan terakhir') !!}</th>
                        <th class="col-hide">{!! $sortLink('activated', 'Diaktifkan') !!}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $card)
                    <tr>
                        <td>
                            <span class="biz-name">{{ $card->owner_name ?? '—' }}</span>
                            <span class="mono id-chip">{{ $card->id }}</span>
                        </td>
                        <td class="num"><span class="scan-pill {{ $card->range_count ? 'has-scans' : '' }}">{{ number_format($card->range_count) }}</span></td>
                        <td class="num total">{{ number_format($card->live_count + $card->archived_scans) }}</td>
                        <td class="col-hide muted-text">{{ $card->last_scan_at ? \Illuminate\Support\Carbon::parse($card->last_scan_at)->diffForHumans() : '—' }}</td>
                        <td class="col-hide muted-text">{{ $card->activated_at?->translatedFormat('j M Y') }}</td>
                        <td class="go">
                            <a href="{{ route('dashboard.cards.show', $card) }}" class="go-btn" aria-label="Buka detail {{ $card->id }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty">Belum ada usaha aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stores->total() > 0)
        <div class="list-foot">
            <span class="hint">Menampilkan {{ $stores->firstItem() }}–{{ $stores->lastItem() }} dari {{ number_format($stores->total()) }} usaha</span>
            {{ $stores->links('vendor.pagination.simple') }}
        </div>
        @endif
    </div>

</div>

@push('styles')
<style>
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

.kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
.kpi { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; display: flex; flex-direction: column; gap: 4px; }
.kpi-label { font-size: 12.5px; font-weight: 500; color: var(--muted); }
.kpi-value { font-size: 26px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
.chart-box { height: 280px; }

.stats-table th { padding: 9px 14px; }
.stats-table td { padding: 11px 14px; }
.stats-table .num { text-align: center; }
.sort-link { color: inherit; white-space: nowrap; }
.sort-link:hover, .sort-link.on { color: var(--accent-dark); }
.biz-name { font-weight: 600; margin-right: 8px; }
.id-chip { font-size: 11.5px; color: var(--muted); background: #F3F4F6; padding: 2px 7px; border-radius: 6px; }
.scan-pill { display: inline-block; min-width: 28px; padding: 2px 8px; font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums; border-radius: 99px; background: #F3F4F6; color: var(--faint); }
.scan-pill.has-scans { background: var(--accent-soft); color: var(--accent); }
.total { font-weight: 600; font-variant-numeric: tabular-nums; }
.muted-text { color: var(--muted); font-size: 13px; white-space: nowrap; }
.go { width: 44px; text-align: right; }
.go-btn { display: inline-flex; padding: 6px; border-radius: 7px; color: var(--faint); }
.go-btn:hover, tr:hover .go-btn { color: var(--accent); background: var(--accent-soft); }
.list-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 12px 14px; border-top: 1px solid var(--border); background: var(--subtle); }

@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 640px) {
    .col-hide { display: none; }
    .chart-box { height: 220px; }
    .kpi-value { font-size: 22px; }
}
</style>
@endpush

@endsection
