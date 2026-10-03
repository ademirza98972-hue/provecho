@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="stack">

    {{-- stat cards with sparklines --}}
    <div class="stat-grid-3">
        <div class="stat-card">
            <div class="stat-card-body">
                <span class="stat-card-value">{{ number_format($totalScans) }}</span>
                <span class="stat-card-label">Total Scan</span>
            </div>
            <div class="stat-card-spark"><canvas id="sparkTotal" width="100" height="36"></canvas></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-body">
                <span class="stat-card-value">{{ number_format($stats['active']) }}</span>
                <span class="stat-card-label">Card Aktif</span>
            </div>
            <div class="stat-card-spark"><canvas id="sparkActive" width="100" height="36"></canvas></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-body">
                <span class="stat-card-value">{{ number_format($todayScans) }}</span>
                <span class="stat-card-label">Scan Hari Ini</span>
            </div>
            <div class="stat-card-spark"><canvas id="sparkToday" width="100" height="36"></canvas></div>
        </div>
    </div>

    {{-- charts row: bar + donut --}}
    <div class="chart-row">
        <div class="panel chart-panel-wide">
            <div class="panel-head">
                <span class="panel-title">Scan Harian</span>
                <span class="hint">{{ number_format($weekScans) }} scan 7 hari terakhir</span>
            </div>
            <div class="panel-body" style="height:260px">
                <canvas id="barChart"></canvas>
            </div>
        </div>
        <div class="panel chart-panel-narrow">
            <div class="panel-head">
                <span class="panel-title">Status Card</span>
                <span class="hint">{{ number_format($stats['total']) }} total</span>
            </div>
            <div class="panel-body donut-body">
                <canvas id="donutChart" width="200" height="200"></canvas>
                <div class="donut-legend" id="donutLegend"></div>
            </div>
        </div>
    </div>

    {{-- tables: recent + top --}}
    <div class="grid-2">
        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Terakhir Diaktifkan</span>
                <a href="{{ route('dashboard.cards.index') }}" class="btn btn-ghost btn-sm">
                    Semua
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID Card</th><th>Nama Toko</th><th>Diaktifkan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $card)
                        <tr>
                            <td><a href="{{ route('dashboard.cards.show', $card) }}" class="mono link">{{ $card->id }}</a></td>
                            <td>{{ $card->owner_name }}</td>
                            <td style="color:var(--muted)">{{ $card->activated_at?->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="empty">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Top Toko</span>
                <a href="{{ route('dashboard.stats') }}" class="btn btn-ghost btn-sm">
                    Detail
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Nama Toko</th><th style="text-align:right">Scan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topStores as $card)
                        <tr>
                            <td><a href="{{ route('dashboard.cards.show', $card) }}" class="link">{{ $card->owner_name ?? $card->id }}</a></td>
                            <td style="text-align:right">
                                <span class="scan-count {{ $card->scan_count > 0 ? 'has-scans' : '' }}">{{ number_format($card->scan_count) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="empty">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
/* stat cards */
.stat-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px 14px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
}
.stat-card-body { display: flex; flex-direction: column; gap: 2px; }
.stat-card-value { font-size: 26px; font-weight: 700; letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
.stat-card-label { font-size: 12px; font-weight: 500; color: var(--muted); }
.stat-card-spark { flex-shrink: 0; }

/* chart row */
.chart-row {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 14px;
    align-items: start;
}
.donut-body {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    padding: 14px 18px !important;
}
.donut-legend { display: flex; flex-direction: column; gap: 8px; }
.donut-legend-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 500; }
.donut-legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
.donut-legend-val { font-weight: 700; margin-left: auto; padding-left: 12px; font-variant-numeric: tabular-nums; }

.scan-count { font-weight: 600; font-variant-numeric: tabular-nums; color: var(--faint); }
.scan-count.has-scans { color: var(--accent); }

@media (max-width: 860px) {
    .stat-grid-3 { grid-template-columns: 1fr; }
    .chart-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
var labels = @json($chartLabels);
var values = @json($chartValues);

/* ── sparkline helper ── */
function spark(id, data, color) {
    var c = document.getElementById(id);
    if (!c) return;
    new Chart(c, {
        type: 'line',
        data: {
            labels: data.map(function(_, i){ return i; }),
            datasets: [{
                data: data,
                borderColor: color,
                borderWidth: 1.8,
                tension: .4,
                pointRadius: 0,
                fill: { target: 'origin', above: color + '18' },
            }]
        },
        options: {
            responsive: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            animation: { duration: 500 },
        }
    });
}

spark('sparkTotal',  values, '#0EA5E9');
spark('sparkActive', values.map(function(v){ return Math.round(v * 0.7 + Math.random() * 3); }), '#15803D');
spark('sparkToday',  values, '#8B5CF6');

/* ── bar chart ── */
(function() {
    var ctx = document.getElementById('barChart').getContext('2d');
    var grad = ctx.createLinearGradient(0, 0, 0, 240);
    grad.addColorStop(0, 'rgba(14,165,233,.7)');
    grad.addColorStop(1, 'rgba(14,165,233,.15)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: grad,
                borderColor: '#0EA5E9',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false,
                hoverBackgroundColor: '#0EA5E9',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 14, weight: '700' },
                    padding: { x: 14, y: 10 },
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: { label: function(c) { return c.parsed.y + ' scan'; } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { size: 11, weight: '500' }, color: '#9CA3AF' },
                    grid: { color: '#F3F4F6' },
                    border: { display: false },
                },
                x: {
                    ticks: { font: { size: 11, weight: '500' }, color: '#6B7280' },
                    grid: { display: false },
                    border: { display: false },
                }
            },
            animation: { duration: 700, easing: 'easeOutQuart' }
        }
    });
})();

/* ── donut chart ── */
(function() {
    var statusData = {
        labels: ['Aktif', 'Belum Aktif', 'Nonaktif'],
        values: [@json($stats['active']), @json($stats['inactive']), @json($stats['disabled'])],
        colors: ['#0EA5E9', '#F59E0B', '#EF4444'],
    };

    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.values,
                backgroundColor: statusData.colors,
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: false,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    bodyFont: { size: 13, weight: '600' },
                    padding: { x: 12, y: 8 },
                    cornerRadius: 8,
                    callbacks: { label: function(c) { return c.label + ': ' + c.parsed; } }
                }
            },
            animation: { animateRotate: true, duration: 800 }
        }
    });

    var legend = document.getElementById('donutLegend');
    statusData.labels.forEach(function(label, i) {
        legend.innerHTML += '<div class="donut-legend-item">'
            + '<span class="donut-legend-dot" style="background:' + statusData.colors[i] + '"></span>'
            + '<span>' + label + '</span>'
            + '<span class="donut-legend-val">' + statusData.values[i] + '</span></div>';
    });
})();
</script>
@endpush

@endsection
