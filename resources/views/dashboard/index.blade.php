@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

@php
    $diff = function (int $now, int $prev, string $than) {
        $d = $now - $prev;
        return [
            'cls'  => $d > 0 ? 'up' : ($d < 0 ? 'down' : 'flat'),
            'text' => $d === 0 ? "sama dengan {$than}" : ($d > 0 ? '+' : '−') . number_format(abs($d)) . " dari {$than}",
        ];
    };
    $dToday = $diff($todayScans, $yesterdayScans, 'kemarin');
    $rangeLabel = \App\Support\ScanRange::OPTIONS[$days];
    $dRange = $prevRangeScans === null ? null : $diff($rangeScans, $prevRangeScans, "{$rangeLabel} sebelumnya");

    $total = max($stats['total'], 1);
    $status = [
        ['key' => 'active',   'label' => 'Aktif',       'n' => $stats['active'],   'color' => 'var(--ok)',   'desc' => 'Terhubung ke halaman ulasan Google'],
        ['key' => 'inactive', 'label' => 'Belum aktif', 'n' => $stats['inactive'], 'color' => '#F59E0B',     'desc' => auth()->user()->isAdmin() ? $stats['unprinted'] . ' di antaranya belum dicetak' : 'Siap diaktifkan untuk pembeli'],
        ['key' => 'disabled', 'label' => 'Nonaktif',    'n' => $stats['disabled'], 'color' => 'var(--bad)',  'desc' => 'Dimatikan dari dashboard'],
    ];
    $maxScan = max($topStores->max('scan_count') ?? 0, 1);
@endphp

<div class="stack">

    <div class="dash-head">
        <div>
            <h2 class="dash-hello">Ringkasan Provecho</h2>
            <p class="dash-date">{{ now()->translatedFormat('l, j F Y') }}</p>
        </div>
        @include('dashboard._range-pills', ['days' => $days, 'route' => 'dashboard.index'])
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Scan hari ini</span>
                <span class="kpi-icon" style="--c:var(--accent)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
            </div>
            <span class="kpi-value">{{ number_format($todayScans) }}</span>
            <span class="kpi-delta {{ $dToday['cls'] }}">{{ $dToday['text'] }}</span>
        </div>
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Scan {{ $rangeLabel }}</span>
                <span class="kpi-icon" style="--c:#8B5CF6"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span>
            </div>
            <span class="kpi-value">{{ number_format($rangeScans) }}</span>
            @if($dRange)
                <span class="kpi-delta {{ $dRange['cls'] }}">{{ $dRange['text'] }}</span>
            @else
                <span class="kpi-sub">Rentang terpanjang yang disimpan</span>
            @endif
        </div>
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Total scan</span>
                <span class="kpi-icon" style="--c:#0D9488"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 8L9 4l-3 8H2"/></svg></span>
            </div>
            <span class="kpi-value">{{ number_format($totalScans) }}</span>
            <span class="kpi-sub">Sejak card pertama dipakai</span>
        </div>
        <div class="kpi">
            <div class="kpi-top">
                <span class="kpi-label">Card aktif</span>
                <span class="kpi-icon" style="--c:var(--ok)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="m8 12 3 3 5-5"/></svg></span>
            </div>
            <span class="kpi-value">{{ number_format($stats['active']) }}</span>
            <span class="kpi-sub">dari {{ number_format($stats['total']) }} card terdaftar</span>
        </div>
    </div>

    <div class="dash-row">
        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Scan {{ $rangeLabel }} terakhir</span>
                <span class="hint">Rata-rata {{ number_format($rangeScans / $days, 1, ',', '.') }} scan per hari</span>
            </div>
            <div class="panel-body chart-box">
                @include('dashboard._scan-chart', ['id' => 'scanChart', 'labels' => $chartLabels, 'values' => $chartValues])
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Status card</span>
                <span class="hint">{{ number_format($stats['total']) }} card</span>
            </div>
            <div class="panel-body">
                <div class="status-bar" aria-hidden="true">
                    @foreach($status as $s)
                        @if($s['n'] > 0)
                            <span style="width:{{ $s['n'] / $total * 100 }}%;background:{{ $s['color'] }}"></span>
                        @endif
                    @endforeach
                </div>
                <div class="status-list">
                    @foreach($status as $s)
                    <a href="{{ route('dashboard.cards.index', ['status' => $s['key']]) }}" class="status-row">
                        <span class="status-dot" style="background:{{ $s['color'] }}"></span>
                        <span class="status-text">
                            <span class="status-label">{{ $s['label'] }}</span>
                            <span class="status-desc">{{ $s['desc'] }}</span>
                        </span>
                        <span class="status-num">{{ number_format($s['n']) }}</span>
                        <svg class="status-go" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="dash-row">
        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Terakhir diaktifkan</span>
                <a href="{{ route('dashboard.cards.index', ['status' => 'active']) }}" class="btn btn-ghost btn-sm">
                    Semua
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Usaha</th><th>ID Card</th><th style="text-align:right">Diaktifkan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $card)
                        <tr>
                            <td>
                                <a href="{{ route('dashboard.cards.show', $card) }}" class="biz">
                                    <span class="biz-ava">{{ strtoupper(mb_substr($card->owner_name ?? '?', 0, 1)) }}</span>
                                    <span class="biz-name">{{ $card->owner_name }}</span>
                                </a>
                            </td>
                            <td><span class="mono id-chip">{{ $card->id }}</span></td>
                            <td class="when" title="{{ $card->activated_at?->translatedFormat('j F Y, H:i') }}">{{ $card->activated_at?->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty">Belum ada card yang diaktifkan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Paling banyak di-scan · {{ $rangeLabel }}</span>
                <a href="{{ route('dashboard.stats', ['range' => $days]) }}" class="btn btn-ghost btn-sm">
                    Detail
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="panel-body">
                @forelse($topStores as $i => $card)
                <a href="{{ route('dashboard.cards.show', $card) }}" class="rank">
                    <span class="rank-no">{{ $i + 1 }}</span>
                    <span class="rank-main">
                        <span class="rank-line">
                            <span class="rank-name">{{ $card->owner_name ?? $card->id }}</span>
                            <span class="rank-num">{{ number_format($card->scan_count) }} <small>scan</small></span>
                        </span>
                        <span class="rank-track"><span style="width:{{ $card->scan_count / $maxScan * 100 }}%"></span></span>
                    </span>
                </a>
                @empty
                <p class="empty" style="padding:24px 0">Belum ada card aktif</p>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
.dash-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.dash-hello { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.dash-date { font-size: 13px; color: var(--muted); margin-top: 2px; }

.kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
.kpi {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 16px 18px; display: flex; flex-direction: column; gap: 4px;
}
.kpi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.kpi-label { font-size: 12.5px; font-weight: 500; color: var(--muted); }
.kpi-icon {
    width: 30px; height: 30px; border-radius: 8px; display: grid; place-items: center; color: var(--c);
    background: color-mix(in srgb, var(--c) 12%, transparent);
}
.kpi-icon svg { width: 16px; height: 16px; }
.kpi-value { font-size: 28px; font-weight: 700; letter-spacing: -.03em; line-height: 1.15; font-variant-numeric: tabular-nums; margin-top: 2px; }
.kpi-sub, .kpi-delta { font-size: 12px; color: var(--muted); }
.kpi-delta { align-self: flex-start; font-weight: 600; padding: 2px 8px; border-radius: 99px; background: #F3F4F6; font-variant-numeric: tabular-nums; }
.kpi-delta.up   { background: var(--ok-soft);  color: var(--ok); }
.kpi-delta.down { background: var(--bad-soft); color: var(--bad); }

.dash-row { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 14px; align-items: stretch; }
.chart-box { height: 270px; }

.status-bar { display: flex; height: 10px; border-radius: 99px; overflow: hidden; background: #F3F4F6; gap: 2px; }
.status-bar span { display: block; min-width: 4px; }
.status-list { display: flex; flex-direction: column; margin-top: 14px; }
.status-row {
    display: flex; align-items: center; gap: 12px; padding: 11px 8px; margin: 0 -8px;
    border-radius: 8px; transition: background .12s;
}
.status-row + .status-row { border-top: 1px solid #F3F4F6; }
.status-row:hover { background: var(--subtle); }
.status-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.status-text { display: flex; flex-direction: column; min-width: 0; flex: 1; }
.status-label { font-size: 13.5px; font-weight: 600; }
.status-desc { font-size: 12px; color: var(--muted); }
.status-num { font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums; }
.status-go { color: var(--faint); transition: transform .12s, color .12s; }
.status-row:hover .status-go { color: var(--accent); transform: translateX(2px); }

.biz { display: flex; align-items: center; gap: 10px; min-width: 0; }
.biz-ava {
    width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0; display: grid; place-items: center;
    font-size: 12.5px; font-weight: 700; color: var(--accent-dark); background: var(--accent-soft);
}
.biz-name { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.biz:hover .biz-name { color: var(--accent); }
.id-chip { font-size: 12px; color: var(--muted); background: #F3F4F6; padding: 2px 7px; border-radius: 6px; }
.when { text-align: right; color: var(--muted); white-space: nowrap; font-size: 13px; }

.rank { display: flex; align-items: center; gap: 12px; padding: 9px 0; }
.rank + .rank { border-top: 1px solid #F3F4F6; }
.rank-no {
    width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center;
    font-size: 12px; font-weight: 700; color: var(--muted); background: #F3F4F6;
}
.rank:first-child .rank-no { color: #fff; background: var(--accent); }
.rank-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 6px; }
.rank-line { display: flex; justify-content: space-between; gap: 10px; align-items: baseline; }
.rank-name { font-size: 13.5px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.rank:hover .rank-name { color: var(--accent); }
.rank-num { font-size: 13.5px; font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
.rank-num small { font-size: 11.5px; font-weight: 500; color: var(--faint); }
.rank-track { height: 6px; border-radius: 99px; background: #F3F4F6; overflow: hidden; }
.rank-track span { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--accent), #22C55E); }

@media (max-width: 1100px) {
    .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .dash-row { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 480px) {
    .kpi { padding: 14px; }
    .kpi-value { font-size: 24px; }
    .kpi-grid { gap: 10px; }
    .chart-box { height: 220px; }
    th:nth-child(2), td:nth-child(2) { display: none; }
    .biz-name { white-space: normal; line-height: 1.35; }
    td { padding: 11px 14px; }
}
</style>
@endpush

@endsection
