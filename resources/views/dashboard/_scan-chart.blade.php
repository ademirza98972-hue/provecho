{{-- Grafik scan harian. Param: $id, $labels, $values. Batang untuk ≤ 30 hari, garis untuk rentang lebih panjang. --}}
<canvas id="{{ $id }}" role="img" aria-label="Grafik scan per hari"></canvas>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
function scanChart(id, labels, values) {
    var ctx = document.getElementById(id).getContext('2d');
    var bars = values.length <= 30, last = values.length - 1;

    var soft = ctx.createLinearGradient(0, 0, 0, 240);
    soft.addColorStop(0, 'rgba(14,165,233,.45)');
    soft.addColorStop(1, 'rgba(14,165,233,.08)');
    var strong = ctx.createLinearGradient(0, 0, 0, 240);
    strong.addColorStop(0, '#0EA5E9');
    strong.addColorStop(1, '#22C55E');

    new Chart(ctx, {
        type: bars ? 'bar' : 'line',
        data: {
            labels: labels,
            datasets: [bars ? {
                data: values,
                backgroundColor: values.map(function (_, i) { return i === last ? strong : soft; }),
                hoverBackgroundColor: '#0EA5E9',
                borderRadius: values.length > 7 ? 4 : 7,
                borderSkipped: false,
                maxBarThickness: 56,
            } : {
                data: values,
                borderColor: '#0EA5E9', borderWidth: 2, tension: .3,
                fill: true, backgroundColor: soft,
                pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: '#0EA5E9',
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
                    callbacks: { label: function (c) { return c.parsed.y + ' scan'; } }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 }, color: '#9CA3AF' }, grid: { color: '#F3F4F6' }, border: { display: false } },
                x: { ticks: { font: { size: 11, weight: '500' }, color: '#6B7280', maxRotation: 0, autoSkip: true, maxTicksLimit: bars ? 10 : 8 }, grid: { display: false }, border: { display: false } }
            },
            animation: { duration: 600, easing: 'easeOutQuart' }
        }
    });
}
</script>
@endpush
@endonce

@push('scripts')
<script>scanChart(@js($id), @js($labels), @js($values));</script>
@endpush
