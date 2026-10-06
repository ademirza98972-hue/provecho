@extends('layouts.app')
@section('title', 'Aktivitas')
@section('content')

@php
    $filtered = request('search') || request('card_id');
    $total = $actionCounts->sum();
    $dayLabel = function ($date) {
        if ($date->isToday()) return 'Hari ini';
        if ($date->isYesterday()) return 'Kemarin';
        return $date->translatedFormat('l, j F Y');
    };
    $prevDay = null;
@endphp

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Riwayat aktivitas</h2>
            <p class="page-sub">Setiap scan, aktivasi, perubahan data, dan cetak QR tercatat di sini.</p>
        </div>
        @can('admin')
        <a href="{{ route('dashboard.activity.export') }}" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
            Export semua (CSV)
        </a>
        @endcan
    </div>

    <nav class="status-pills" aria-label="Filter jenis aktivitas">
        <a href="{{ route('dashboard.activity', request()->except('page', 'action')) }}"
           class="pill {{ !request('action') ? 'pill-active' : '' }}">
            Semua <b>{{ number_format($total) }}</b>
        </a>
        @foreach(\App\Models\CardLog::ACTIONS as $key => [$label, $cls])
            @if($actionCounts->has($key))
            <a href="{{ route('dashboard.activity', array_merge(request()->except('page'), ['action' => $key])) }}"
               class="pill {{ request('action') === $key ? 'pill-active' : '' }}">
                <span class="dot {{ $cls }}"></span>
                {{ $label }} <b>{{ number_format($actionCounts[$key]) }}</b>
            </a>
            @endif
        @endforeach
    </nav>

    <div class="panel">
        <div class="panel-head list-head">
            <form method="GET" action="{{ route('dashboard.activity') }}" class="filters">
                @if(request('action'))<input type="hidden" name="action" value="{{ request('action') }}">@endif
                <div class="search-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID card atau nama usaha" aria-label="Cari aktivitas" autocomplete="off">
                </div>
                <select name="card_id" onchange="this.form.submit()" aria-label="Pilih usaha">
                    <option value="">Semua usaha</option>
                    @foreach($activeCards as $c)
                        <option value="{{ $c->id }}" @selected(request('card_id') === $c->id)>{{ $c->owner_name ?? $c->id }}</option>
                    @endforeach
                </select>
                @if($filtered)
                    <a href="{{ route('dashboard.activity', request()->only('action')) }}" class="btn btn-ghost btn-sm">Hapus filter</a>
                @endif
            </form>
            <span class="hint">{{ number_format($logs->total()) }} aktivitas</span>
        </div>

        <div class="table-wrap">
            <table class="log-table">
                <thead>
                    <tr>
                        <th class="col-time">Jam</th>
                        <th>Aktivitas</th>
                        <th>Usaha</th>
                        <th class="col-device">Perangkat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php $day = $log->created_at->toDateString(); @endphp
                        @if($day !== $prevDay)
                            <tr class="day-row"><td colspan="5">{{ $dayLabel($log->created_at) }}</td></tr>
                            @php $prevDay = $day; @endphp
                        @endif
                        <tr>
                            <td class="col-time">{{ $log->created_at->format('H:i') }}</td>
                            <td><span class="act-badge {{ $log->action_class }}">{{ $log->action_label }}</span></td>
                            <td>
                                <div class="biz-line">
                                    @if($log->card?->owner_name)
                                        <span class="biz-name">{{ $log->card->owner_name }}</span>
                                    @else
                                        <span class="faint">Belum terhubung</span>
                                    @endif
                                    <span class="mono id-chip">{{ $log->card_id }}</span>
                                </div>
                            </td>
                            <td class="col-device" title="{{ $log->ip_address }}">{{ $log->device }}</td>
                            <td class="go">
                                @if($log->card)
                                <a href="{{ route('dashboard.cards.show', $log->card_id) }}" class="go-btn" aria-label="Buka detail {{ $log->card_id }}">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                {{ $filtered || request('action') ? 'Tidak ada aktivitas yang cocok dengan filter ini.' : 'Belum ada aktivitas tercatat.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->total() > 0)
        <div class="list-foot">
            <span class="hint">Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} aktivitas</span>
            {{ $logs->links('vendor.pagination.simple') }}
        </div>
        @endif
    </div>

</div>

@push('styles')
<style>
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

.status-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.pill {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 99px;
    font-size: 13px; font-weight: 500; color: var(--muted);
    background: var(--surface); border: 1px solid var(--border); transition: all .12s;
}
.pill:hover { border-color: var(--border-strong); color: var(--text); }
.pill b { font-weight: 700; font-variant-numeric: tabular-nums; }
.pill .dot { width: 7px; height: 7px; }
.pill-active { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); font-weight: 600; }

.dot.act-scan { background: var(--accent); }
.dot.act-ok   { background: var(--ok); }
.dot.act-info { background: #2563EB; }
.dot.act-warn { background: #F59E0B; }
.dot.act-bad  { background: var(--bad); }

.list-head { padding: 12px 14px; }
.filters { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; flex: 1; min-width: 0; }
.search-box {
    display: flex; align-items: center; gap: 8px; flex: 1; min-width: 200px; max-width: 340px;
    background: var(--surface); border: 1px solid var(--border-strong); border-radius: 8px;
    padding: 0 10px; color: var(--faint); transition: border-color .12s, box-shadow .12s;
}
.search-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); color: var(--accent); }
.search-box input { border: none !important; padding: 7px 0 !important; background: transparent !important; box-shadow: none !important; }
.filters select { width: auto; max-width: 240px; padding: 7px 10px; font-size: 13px; font-weight: 500; cursor: pointer; }

.log-table th { padding: 9px 14px; }
.log-table td { padding: 10px 14px; }
.day-row td {
    padding: 8px 14px; font-size: 12px; font-weight: 700; color: var(--text);
    background: var(--subtle); border-bottom: 1px solid var(--border);
}
tbody tr.day-row:hover { background: var(--subtle); }
.col-time { width: 64px; color: var(--muted); font-variant-numeric: tabular-nums; white-space: nowrap; }

.act-badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 99px; font-size: 12px; font-weight: 600; white-space: nowrap; }
.act-badge.act-scan { background: var(--accent-soft); color: var(--accent-dark); }
.act-badge.act-ok   { background: var(--ok-soft); color: var(--ok); }
.act-badge.act-info { background: #EFF6FF; color: #2563EB; }
.act-badge.act-bad  { background: var(--bad-soft); color: var(--bad); }
.act-badge.act-warn { background: var(--warn-soft); color: var(--warn); }

.biz-line { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.biz-name { font-weight: 600; }
.id-chip { font-size: 11.5px; color: var(--muted); background: #F3F4F6; padding: 2px 7px; border-radius: 6px; }
.col-device { color: var(--muted); font-size: 13px; white-space: nowrap; }
.faint { color: var(--faint); }

.go { width: 44px; text-align: right; }
.go-btn { display: inline-flex; padding: 6px; border-radius: 7px; color: var(--faint); transition: all .12s; }
.go-btn:hover, tr:hover .go-btn { color: var(--accent); background: var(--accent-soft); }

.list-foot {
    display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
    padding: 12px 14px; border-top: 1px solid var(--border); background: var(--subtle);
}

@media (max-width: 860px) {
    .search-box { max-width: none; flex-basis: 100%; }
}
@media (max-width: 560px) {
    .col-device { display: none; }
    .filters select { max-width: none; flex: 1; }
}
</style>
@endpush

@endsection
