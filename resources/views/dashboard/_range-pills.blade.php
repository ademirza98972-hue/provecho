{{-- Pilihan rentang grafik/statistik. Param: $days, $route --}}
<nav class="range-pills" aria-label="Rentang waktu">
    @foreach(\App\Support\ScanRange::OPTIONS as $d => $label)
        <a href="{{ route($route, array_merge(request()->except('range', 'page'), ['range' => $d])) }}"
           class="{{ $days === $d ? 'on' : '' }}" @if($days === $d) aria-current="true" @endif>{{ $label }}</a>
    @endforeach
</nav>

@once
@push('styles')
<style>
.range-pills { display: inline-flex; gap: 2px; padding: 3px; border-radius: 10px; background: var(--surface); border: 1px solid var(--border); }
.range-pills a { padding: 6px 12px; border-radius: 7px; font-size: 13px; font-weight: 500; color: var(--muted); white-space: nowrap; transition: background .12s, color .12s; }
.range-pills a:hover { color: var(--text); background: #F3F4F6; }
.range-pills a.on { background: var(--accent-soft); color: var(--accent-dark); font-weight: 600; }
</style>
@endpush
@endonce
