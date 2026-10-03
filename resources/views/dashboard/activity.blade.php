@extends('layouts.app')
@section('title', 'Aktivitas')
@section('content')

<div class="stack">

    {{-- filter bar --}}
    <div class="panel">
        <div class="panel-body">
            <form method="GET" action="{{ route('dashboard.activity') }}" class="filter-bar">
                <div class="field" style="flex:1;min-width:180px">
                    <label for="search">Cari</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="ID Card atau nama toko…" autocomplete="off">
                </div>

                <div class="field" style="min-width:160px">
                    <label for="card_id">Toko</label>
                    <select id="card_id" name="card_id">
                        <option value="">Semua Toko</option>
                        @foreach($activeCards as $c)
                            <option value="{{ $c->id }}" {{ request('card_id') === $c->id ? 'selected' : '' }}>
                                {{ $c->owner_name ?? $c->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="min-width:130px">
                    <label for="action">Aksi</label>
                    <select id="action" name="action">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ $act }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field" style="align-self:flex-end">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->hasAny(['search', 'card_id', 'action']))
                        <a href="{{ route('dashboard.activity') }}" class="btn btn-ghost btn-sm">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- tabel aktivitas --}}
    <div class="panel">
        <div class="panel-head">
            <span class="panel-title">
                Log Aktivitas
                <span style="font-weight:400;color:var(--muted);font-size:12px;margin-left:6px">
                    {{ number_format($logs->total()) }} total
                </span>
            </span>
            <div class="toolbar-group">
                {{ $logs->links('vendor.pagination.simple') }}
                <a href="{{ route('dashboard.activity.export') }}" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                    Export CSV
                </a>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>ID Card</th>
                        <th>Nama Toko</th>
                        <th>Aksi</th>
                        <th>IP Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap">
                            <span class="log-time">{{ $log->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td><span class="mono">{{ $log->card_id }}</span></td>
                        <td>{{ $log->card->owner_name ?? '—' }}</td>
                        <td>
                            @php
                                $actClass = match($log->action) {
                                    'scan' => 'act-scan',
                                    'activated' => 'act-ok',
                                    'updated' => 'act-info',
                                    'disabled' => 'act-bad',
                                    'reactivated' => 'act-ok',
                                    'reset' => 'act-warn',
                                    default => '',
                                };
                            @endphp
                            <span class="act-badge {{ $actClass }}">{{ $log->action }}</span>
                        </td>
                        <td style="color:var(--muted);font-size:12px;font-variant-numeric:tabular-nums">
                            {{ $log->ip_address }}
                        </td>
                        <td style="text-align:right">
                            <a href="{{ route('dashboard.cards.show', $log->card_id) }}" class="btn btn-outline btn-sm">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty">
                            @if(request()->hasAny(['search', 'card_id', 'action']))
                                Tidak ada aktivitas yang cocok dengan filter.
                            @else
                                Belum ada aktivitas tercatat.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

@push('styles')
<style>
.filter-bar {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-start;
}
.act-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.act-scan  { background: #F3F4F6; color: var(--muted); }
.act-ok    { background: var(--ok-soft); color: var(--ok); }
.act-info  { background: var(--accent-soft); color: var(--accent); }
.act-bad   { background: var(--bad-soft); color: var(--bad); }
.act-warn  { background: var(--warn-soft); color: var(--warn); }
</style>
@endpush

@endsection
