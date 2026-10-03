@extends('layouts.app')
@section('title', 'Statistik Scan')
@section('content')

@php
    $maxChart = $chartData->max() ?: 1;
@endphp

<div class="stack">

    {{-- ringkasan --}}
    <div class="stat-grid">
        <div class="stat">
            <div class="stat-label" style="color:var(--accent)"><span class="dot"></span>Scan Hari Ini</div>
            <div class="stat-num">{{ number_format($todayScans) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--ok)"><span class="dot"></span>Scan 7 Hari</div>
            <div class="stat-num">{{ number_format($weekScans) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--muted)">Total Scan</div>
            <div class="stat-num">{{ number_format($totalScans) }}</div>
        </div>
        <div class="stat">
            <div class="stat-label" style="color:var(--ok)"><span class="dot"></span>Toko Aktif</div>
            <div class="stat-num">{{ number_format($activeCards) }}</div>
        </div>
    </div>

    {{-- chart scan 7 hari --}}
    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Scan 7 Hari Terakhir</span>
        </div>
        <div class="panel-body">
            <div class="chart-bars">
                @foreach($chartData as $date => $count)
                <div class="chart-col">
                    <span class="chart-val">{{ $count }}</span>
                    <div class="chart-bar" style="height: {{ $maxChart > 0 ? round(($count / $maxChart) * 120) : 0 }}px"></div>
                    <span class="chart-label">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</span>
                    <span class="chart-date">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- tabel per toko --}}
    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">Scan per Toko</span>
            <div class="toolbar-group">
                {{ $stores->links('vendor.pagination.simple') }}
                <a href="{{ route('dashboard.stats.export') }}" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                    Export CSV
                </a>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Card</th>
                        <th>
                            <a href="{{ route('dashboard.stats', ['sort' => 'name', 'dir' => ($sort === 'name' && $dir === 'asc') ? 'desc' : 'asc']) }}" class="sort-link">
                                Nama Toko {!! $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th style="text-align:right">
                            <a href="{{ route('dashboard.stats', ['sort' => 'scan_count', 'dir' => ($sort === 'scan_count' && $dir === 'desc') ? 'asc' : 'desc']) }}" class="sort-link">
                                Total Scan {!! $sort === 'scan_count' ? ($dir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard.stats', ['sort' => 'last_scan', 'dir' => ($sort === 'last_scan' && $dir === 'desc') ? 'asc' : 'desc']) }}" class="sort-link">
                                Scan Terakhir {!! $sort === 'last_scan' ? ($dir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('dashboard.stats', ['sort' => 'activated', 'dir' => ($sort === 'activated' && $dir === 'desc') ? 'asc' : 'desc']) }}" class="sort-link">
                                Diaktifkan {!! $sort === 'activated' ? ($dir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stores as $card)
                    <tr>
                        <td><span class="mono">{{ $card->id }}</span></td>
                        <td>{{ $card->owner_name ?? '—' }}</td>
                        <td style="text-align:right">
                            <span class="scan-count {{ $card->scan_count > 0 ? 'has-scans' : '' }}">
                                {{ number_format($card->scan_count) }}
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:13px">
                            {{ $card->last_scan_at ? \Carbon\Carbon::parse($card->last_scan_at)->diffForHumans() : '—' }}
                        </td>
                        <td style="color:var(--muted);font-size:13px">
                            {{ $card->activated_at?->format('d M Y') }}
                        </td>
                        <td style="text-align:right">
                            <a href="{{ route('dashboard.cards.show', $card) }}" class="btn btn-outline btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty">Belum ada toko aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@push('styles')
<style>
.chart-bars {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: 8px;
    height: 170px;
    padding-top: 20px;
}
.chart-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex: 1;
}
.chart-bar {
    width: 100%;
    max-width: 48px;
    min-height: 4px;
    background: var(--accent);
    border-radius: 5px 5px 0 0;
    transition: height .3s;
}
.chart-val {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
    font-variant-numeric: tabular-nums;
}
.chart-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    margin-top: 4px;
}
.chart-date {
    font-size: 11px;
    color: var(--faint);
}
.sort-link {
    color: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.sort-link:hover {
    color: var(--accent);
}
.scan-count {
    font-weight: 600;
    font-variant-numeric: tabular-nums;
    color: var(--faint);
}
.scan-count.has-scans {
    color: var(--accent);
}
</style>
@endpush

@endsection
