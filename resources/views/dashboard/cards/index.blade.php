@extends('layouts.app')
@section('title', 'Kelola Card')
@section('content')

<div class="stack">

    {{-- status pills --}}
    <div class="status-pills">
        <a href="{{ route('dashboard.cards.index', request()->except('status', 'page')) }}"
           class="pill {{ !request('status') ? 'pill-active' : '' }}">
            Semua <b>{{ $counts['total'] }}</b>
        </a>
        <a href="{{ route('dashboard.cards.index', array_merge(request()->except('page'), ['status' => 'active'])) }}"
           class="pill pill-ok {{ request('status') === 'active' ? 'pill-active' : '' }}">
            <span class="dot"></span> Aktif <b>{{ $counts['active'] }}</b>
        </a>
        <a href="{{ route('dashboard.cards.index', array_merge(request()->except('page'), ['status' => 'inactive'])) }}"
           class="pill pill-warn {{ request('status') === 'inactive' ? 'pill-active' : '' }}">
            <span class="dot"></span> Belum Aktif <b>{{ $counts['inactive'] }}</b>
        </a>
        <a href="{{ route('dashboard.cards.index', array_merge(request()->except('page'), ['status' => 'disabled'])) }}"
           class="pill pill-bad {{ request('status') === 'disabled' ? 'pill-active' : '' }}">
            <span class="dot"></span> Nonaktif <b>{{ $counts['disabled'] }}</b>
        </a>
    </div>

    {{-- toolbar: search + generate --}}
    <div class="card-toolbar">
        <form method="GET" action="{{ route('dashboard.cards.index') }}" class="toolbar-group">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID atau nama toko...">
            </div>
            <select name="generated" class="gen-filter" onchange="this.form.submit()">
                <option value="">Generate: Semua</option>
                <option value="today" {{ request('generated') === 'today' ? 'selected' : '' }}>Hari ini</option>
                <option value="week" {{ request('generated') === 'week' ? 'selected' : '' }}>7 hari</option>
                <option value="month" {{ request('generated') === 'month' ? 'selected' : '' }}>30 hari</option>
            </select>
            <select name="per_page" onchange="this.form.submit()">
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            @if(request('search') || request('generated'))
                <a href="{{ route('dashboard.cards.index', request()->only('status', 'per_page')) }}" class="btn btn-ghost btn-sm">Reset</a>
            @endif
        </form>

        <form method="POST" action="{{ route('dashboard.cards.generate') }}" class="toolbar-group">
            @csrf
            <input type="number" name="count" min="1" max="500" value="10" style="width:72px" aria-label="Jumlah card">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Generate
            </button>
        </form>
    </div>

    {{-- table + bulk actions --}}
    <div x-data="cardTable(@js($cards->pluck('id')))">
    <form method="POST" action="{{ route('dashboard.cards.export.pdf') }}">
        @csrf
        <input type="hidden" name="mode" :value="mode">

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">
                    <span x-show="!sel.length">{{ $cards->total() }} card</span>
                    <span x-show="sel.length" x-cloak><span x-text="sel.length"></span> dipilih</span>
                </span>

                <div class="toolbar-group">
                    {{ $cards->links('vendor.pagination.simple') }}
                </div>

                <div class="toolbar-group" x-show="sel.length" x-cloak>
                    <button type="button" class="btn btn-ghost btn-sm" @click="sel = []">Batal</button>
                    <select x-model="mode" style="border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:12px;font-weight:600;background:#fff;cursor:pointer">
                        <option value="single">10x10 cm</option>
                        <option value="a4">A4</option>
                        <option value="a3">A3</option>
                        <option value="sticker">Sticker</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                        Cetak
                    </button>
                    <button type="button" class="btn btn-danger btn-sm"
                            @click="if(confirm('HAPUS ' + sel.length + ' card? Tidak bisa dikembalikan.')) { $refs.deleteForm.submit() }">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                        Hapus
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th class="check">
                                <input type="checkbox" aria-label="Pilih semua"
                                       :checked="ids.length > 0 && sel.length === ids.length"
                                       @change="sel = $event.target.checked ? [...ids] : []">
                            </th>
                            <th>ID Card</th>
                            <th>Status</th>
                            <th>Nama Toko</th>
                            <th style="text-align:center">Scan</th>
                            <th>Digenerate</th>
                            <th>Diaktifkan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                        <tr>
                            <td class="check">
                                <input type="checkbox" name="ids[]" value="{{ $card->id }}" x-model="sel">
                            </td>
                            <td>
                                <div class="id-cell">
                                    <a href="{{ route('dashboard.cards.show', $card) }}" class="mono link">{{ $card->id }}</a>
                                    <button type="button" class="copy-btn" title="Copy URL"
                                            onclick="navigator.clipboard.writeText('{{ $card->url }}');this.classList.add('copied');setTimeout(()=>this.classList.remove('copied'),1200)">
                                        <svg class="icon-copy" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                                        <svg class="icon-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="status-cell">
                                    <span class="badge badge-{{ $card->status }}">
                                        <span class="dot"></span>
                                        {{ ['active' => 'Aktif', 'inactive' => 'Belum aktif', 'disabled' => 'Nonaktif'][$card->status] }}
                                    </span>
                                    @if($card->printed_at)
                                        <span class="badge badge-printed" title="Dicetak {{ $card->printed_at->format('d M Y') }}">
                                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="{{ !$card->owner_name ? 'faint' : '' }}">{{ $card->owner_name ?? '—' }}</td>
                            <td style="text-align:center">
                                <span class="scan-pill {{ $card->scan_count > 0 ? 'has-scans' : '' }}">{{ $card->scan_count }}</span>
                            </td>
                            <td class="muted-text">{{ $card->created_at->format('d M Y') }}</td>
                            <td class="muted-text">{{ $card->activated_at?->format('d M Y') ?? '—' }}</td>
                            <td style="text-align:right">
                                <a href="{{ route('dashboard.cards.show', $card) }}" class="btn btn-ghost btn-sm">
                                    Detail
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="empty">Tidak ada card yang cocok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </form>

    <form x-ref="deleteForm" method="POST" action="{{ route('dashboard.cards.bulk-destroy') }}" style="display:none">
        @csrf
        <template x-for="id in sel"><input type="hidden" name="ids[]" :value="id"></template>
    </form>
    </div>

</div>

@push('styles')
<style>
[x-cloak]{display:none!important}

/* status pills */
.status-pills {
    display: flex; gap: 8px; flex-wrap: wrap;
}
.pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 99px;
    font-size: 13px; font-weight: 500; color: var(--muted);
    background: var(--surface); border: 1px solid var(--border);
    transition: all .12s;
}
.pill:hover { border-color: var(--border-strong); color: var(--text); }
.pill b { font-weight: 700; font-variant-numeric: tabular-nums; }
.pill .dot { width: 6px; height: 6px; }
.pill-ok .dot { background: var(--ok); }
.pill-warn .dot { background: var(--warn); }
.pill-bad .dot { background: var(--bad); }
.pill-active { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); font-weight: 600; }
.pill-active.pill-ok { border-color: var(--ok); background: var(--ok-soft); color: var(--ok); }
.pill-active.pill-warn { border-color: var(--warn); background: var(--warn-soft); color: var(--warn); }
.pill-active.pill-bad { border-color: var(--bad); background: var(--bad-soft); color: var(--bad); }

/* toolbar */
.card-toolbar {
    display: flex; gap: 10px; flex-wrap: wrap; align-items: center; justify-content: space-between;
}
.search-box {
    display: flex; align-items: center; gap: 8px;
    background: var(--surface); border: 1px solid var(--border-strong); border-radius: 8px;
    padding: 0 10px; transition: border-color .12s, box-shadow .12s;
    color: var(--faint);
}
.search-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); color: var(--accent); }
.search-box input {
    border: none !important; padding: 7px 0 !important; background: transparent !important;
    box-shadow: none !important; width: 200px;
}
.search-box input:focus { box-shadow: none !important; }
.card-toolbar select {
    padding: 7px 8px; border: 1px solid var(--border-strong); border-radius: 8px;
    font-size: 13px; font-weight: 500; background: var(--surface); cursor: pointer;
    width: auto;
}
.card-toolbar select[name="per_page"] { width: 68px; }
.card-toolbar .gen-filter { width: auto; min-width: 0; }

/* compact table */
.compact-table th { padding: 8px 14px; }
.compact-table td { padding: 8px 14px; }

/* id cell with copy */
.id-cell { display: flex; align-items: center; gap: 6px; }
.copy-btn {
    display: inline-flex; padding: 3px; border: none; background: none;
    color: var(--faint); cursor: pointer; border-radius: 4px; transition: all .12s;
}
.copy-btn:hover { color: var(--accent); background: var(--accent-soft); }
.copy-btn .icon-check { display: none; }
.copy-btn.copied .icon-copy { display: none; }
.copy-btn.copied .icon-check { display: block; color: var(--ok); }

/* scan pill */
.scan-pill {
    display: inline-block; min-width: 24px; padding: 1px 7px;
    font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums;
    border-radius: 99px; background: #F3F4F6; color: var(--faint); text-align: center;
}
.scan-pill.has-scans { background: var(--accent-soft); color: var(--accent); }

.status-cell { display: flex; align-items: center; gap: 4px; }
.faint { color: var(--faint); }
.muted-text { color: var(--muted); }

@media (max-width: 860px) {
    .search-box input { width: 140px; }
    .card-toolbar { gap: 8px; }
}
</style>
@endpush

@push('scripts')
<script>
function cardTable(ids) {
    return { ids: ids, sel: [], mode: 'a4' };
}
</script>
@endpush

@endsection
