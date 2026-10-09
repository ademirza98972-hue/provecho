@extends('layouts.app')
@section('title', 'Kelola Card')
@section('content')

@php
    $isAdmin = auth()->user()->isAdmin();
    $pills = array_values(array_filter([
        ['key' => null,        'label' => 'Semua',         'n' => $counts['total'],     'tone' => ''],
        ['key' => 'active',    'label' => 'Aktif',         'n' => $counts['active'],    'tone' => 'ok'],
        ['key' => 'inactive',  'label' => 'Belum aktif',   'n' => $counts['inactive'],  'tone' => 'warn'],
        $isAdmin ? ['key' => 'unprinted', 'label' => 'Belum dicetak', 'n' => $counts['unprinted'], 'tone' => 'info'] : null,
        ['key' => 'disabled',  'label' => 'Nonaktif',      'n' => $counts['disabled'],  'tone' => 'bad'],
    ]));
    $statusLabel = ['active' => 'Aktif', 'inactive' => 'Belum aktif', 'disabled' => 'Nonaktif'];
    $filtered = request('search') || request('generated') || request('reseller');
    $cols = $isAdmin ? 8 : 6;
@endphp

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Daftar card</h2>
            <p class="page-sub">
                {{ $isAdmin
                    ? 'Generate ID baru, cetak QR, berikan ke reseller, lalu aktifkan card sebelum dikirim ke pembeli.'
                    : 'Card dari Provecho untuk kamu jual. Aktifkan card dengan data usaha pembeli sebelum diserahkan.' }}
            </p>
        </div>
        @if($isAdmin)
        <form method="POST" action="{{ route('dashboard.cards.generate') }}" class="gen-form">
            @csrf
            <label for="gen-count">Generate</label>
            <input id="gen-count" type="number" name="count" min="1" max="500" value="10">
            <span>card baru</span>
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Generate
            </button>
        </form>
        @endif
    </div>

    <nav class="status-pills" aria-label="Filter status">
        @foreach($pills as $p)
            <a href="{{ route('dashboard.cards.index', $p['key'] ? array_merge(request()->except('page', 'status'), ['status' => $p['key']]) : request()->except('page', 'status')) }}"
               class="pill {{ $p['tone'] ? 'pill-' . $p['tone'] : '' }} {{ request('status') == $p['key'] ? 'pill-active' : '' }}">
                @if($p['tone'])<span class="dot"></span>@endif
                {{ $p['label'] }} <b>{{ number_format($p['n']) }}</b>
            </a>
        @endforeach
    </nav>

    <div x-data="cardTable(@js($cards->pluck('id')))">
    <div class="panel">

        <div class="panel-head list-head" x-show="!sel.length">
            <form method="GET" action="{{ route('dashboard.cards.index') }}" class="filters">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <div class="search-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID, nama usaha, atau alamat" aria-label="Cari card">
                </div>
                <select name="generated" onchange="this.form.submit()" aria-label="Tanggal dibuat">
                    <option value="">Dibuat: kapan saja</option>
                    <option value="today" @selected(request('generated') === 'today')>Dibuat hari ini</option>
                    <option value="week" @selected(request('generated') === 'week')>7 hari terakhir</option>
                    <option value="month" @selected(request('generated') === 'month')>30 hari terakhir</option>
                </select>
                @if($isAdmin)
                <select name="reseller" onchange="this.form.submit()" aria-label="Pemilik card">
                    <option value="">Semua pemilik</option>
                    <option value="none" @selected(request('reseller') === 'none')>Stok admin</option>
                    @foreach($resellers as $r)
                        <option value="{{ $r->id }}" @selected(request('reseller') == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
                @endif
                <select name="per_page" onchange="this.form.submit()" aria-label="Baris per halaman">
                    @foreach([20, 50, 100] as $n)
                        <option value="{{ $n }}" @selected($cards->perPage() == $n)>{{ $n }} / hal</option>
                    @endforeach
                </select>
                @if($filtered)
                    <a href="{{ route('dashboard.cards.index', request()->only('status', 'per_page')) }}" class="btn btn-ghost btn-sm">Hapus filter</a>
                @endif
            </form>
            <span class="hint">{{ number_format($cards->total()) }} card</span>
        </div>

        @if($isAdmin)
        <div class="panel-head sel-bar" x-show="sel.length" x-cloak>
            <span class="sel-count"><b x-text="sel.length"></b> card dipilih</span>
            <button type="button" class="btn btn-ghost btn-sm" @click="sel = []">Batal</button>
            <form method="POST" action="{{ route('dashboard.cards.assign') }}" class="sel-actions assign-form">
                @csrf
                <template x-for="id in sel"><input type="hidden" name="ids[]" :value="id"></template>
                <select name="reseller" required aria-label="Berikan ke">
                    <option value="" disabled selected>Berikan ke…</option>
                    @foreach($resellers as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                    <option value="none">Tarik ke stok admin</option>
                </select>
                <button type="submit" class="btn btn-outline btn-sm">Simpan</button>
            </form>
            <form method="POST" action="{{ route('dashboard.cards.export.pdf') }}" class="sel-actions">
                @csrf
                <template x-for="id in sel"><input type="hidden" name="ids[]" :value="id"></template>
                <select name="mode" x-model="mode" aria-label="Ukuran cetak">
                    <option value="single">10×10 cm</option>
                    <option value="a4">Kertas A4</option>
                    <option value="a3">Kertas A3</option>
                    <option value="sticker">Sticker</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                    Cetak QR
                </button>
                <button type="button" class="btn btn-danger btn-sm"
                        @click="if (confirm('Hapus ' + sel.length + ' card? Card yang sudah dicetak otomatis dilewati. Card lainnya beserta riwayat scan-nya terhapus dan tidak bisa dikembalikan.')) $refs.deleteForm.submit()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                    Hapus
                </button>
            </form>
        </div>
        @endif

        <div class="table-wrap">
            <table class="card-table">
                <thead>
                    <tr>
                        @if($isAdmin)
                        <th class="check">
                            <input type="checkbox" aria-label="Pilih semua di halaman ini"
                                   :checked="ids.length > 0 && sel.length === ids.length"
                                   @change="sel = $event.target.checked ? [...ids] : []">
                        </th>
                        @endif
                        <th>ID Card</th>
                        <th>Usaha</th>
                        @if($isAdmin)<th class="col-owner">Pemilik</th>@endif
                        <th>Status</th>
                        <th class="num">Scan</th>
                        <th class="col-created">Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                    <tr :class="sel.includes('{{ $card->id }}') && 'is-sel'">
                        @if($isAdmin)
                        <td class="check">
                            <input type="checkbox" value="{{ $card->id }}" x-model="sel" aria-label="Pilih {{ $card->id }}">
                        </td>
                        @endif
                        <td>
                            <div class="id-cell">
                                <a href="{{ route('dashboard.cards.show', $card) }}" class="mono link">{{ $card->id }}</a>
                                <button type="button" class="copy-btn" title="Salin link card" aria-label="Salin link {{ $card->id }}"
                                        data-url="{{ $card->url }}"
                                        onclick="navigator.clipboard.writeText(this.dataset.url);this.classList.add('copied');setTimeout(()=>this.classList.remove('copied'),1200)">
                                    <svg class="icon-copy" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                                    <svg class="icon-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="biz-cell">
                            @if($card->owner_name)
                                <span class="biz-name">{{ $card->owner_name }}</span>
                                @if($card->owner_address)<span class="biz-addr">{{ $card->owner_address }}</span>@endif
                            @else
                                <span class="faint">Belum terhubung</span>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td class="col-owner">
                            @if($card->reseller)
                                <span class="owner-chip">{{ $card->reseller->name }}</span>
                            @else
                                <span class="faint">Stok admin</span>
                            @endif
                        </td>
                        @endif
                        <td>
                            <span class="badge badge-{{ $card->status }}"><span class="dot"></span>{{ $statusLabel[$card->status] }}</span>
                            <span class="status-note">
                                @if($card->status === 'active')
                                    sejak {{ $card->activated_at?->translatedFormat('j M Y') }}
                                @elseif($card->status === 'disabled')
                                    sejak {{ $card->disabled_at?->translatedFormat('j M Y') }}
                                @elseif(! $isAdmin)
                                    Siap diaktifkan
                                @elseif($card->printed_at)
                                    Dicetak {{ $card->printed_at->translatedFormat('j M') }}
                                @else
                                    <span class="note-warn">Belum dicetak</span>
                                @endif
                            </span>
                        </td>
                        <td class="num">
                            <span class="scan-pill {{ $card->total_scans > 0 ? 'has-scans' : '' }}">{{ number_format($card->total_scans) }}</span>
                        </td>
                        <td class="col-created muted-text">{{ $card->created_at->translatedFormat('j M Y') }}</td>
                        <td class="go">
                            <a href="{{ route('dashboard.cards.show', $card) }}" class="go-btn" aria-label="Buka detail {{ $card->id }}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $cols }}" class="empty">
                            @if($filtered || request('status'))
                                Tidak ada card yang cocok dengan filter ini.
                            @elseif($isAdmin)
                                Belum ada card. Generate card pertama lewat tombol di atas.
                            @else
                                Belum ada card untuk kamu. Hubungi admin Provecho untuk mendapatkan card.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cards->total() > 0)
        <div class="list-foot">
            <span class="hint">Menampilkan {{ $cards->firstItem() }}–{{ $cards->lastItem() }} dari {{ number_format($cards->total()) }} card</span>
            {{ $cards->links('vendor.pagination.simple') }}
        </div>
        @endif
    </div>

    @if($isAdmin)
    <form x-ref="deleteForm" method="POST" action="{{ route('dashboard.cards.bulk-destroy') }}" hidden>
        @csrf
        <template x-for="id in sel"><input type="hidden" name="ids[]" :value="id"></template>
    </form>
    @endif
    </div>

</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }

.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

.gen-form {
    display: flex; align-items: center; gap: 8px; padding: 6px 6px 6px 14px;
    background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
}
.gen-form label, .gen-form span { font-size: 13px; font-weight: 500; color: var(--muted); }
.gen-form input { width: 64px !important; padding: 6px 8px !important; text-align: center; font-weight: 700; font-variant-numeric: tabular-nums; }

.status-pills { display: flex; gap: 8px; flex-wrap: wrap; }
.pill {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 99px;
    font-size: 13px; font-weight: 500; color: var(--muted);
    background: var(--surface); border: 1px solid var(--border); transition: all .12s;
}
.pill:hover { border-color: var(--border-strong); color: var(--text); }
.pill b { font-weight: 700; font-variant-numeric: tabular-nums; }
.pill .dot { width: 6px; height: 6px; }
.pill-ok .dot { background: var(--ok); }
.pill-warn .dot { background: #F59E0B; }
.pill-info .dot { background: #2563EB; }
.pill-bad .dot { background: var(--bad); }
.pill-active { border-color: var(--accent); background: var(--accent-soft); color: var(--accent); font-weight: 600; }
.pill-active.pill-ok { border-color: var(--ok); background: var(--ok-soft); color: var(--ok); }
.pill-active.pill-warn { border-color: #F59E0B; background: var(--warn-soft); color: var(--warn); }
.pill-active.pill-info { border-color: #2563EB; background: #EFF6FF; color: #2563EB; }
.pill-active.pill-bad { border-color: var(--bad); background: var(--bad-soft); color: var(--bad); }

.list-head { padding: 12px 14px; min-height: 61px; }
.filters { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; flex: 1; min-width: 0; }
.search-box {
    display: flex; align-items: center; gap: 8px; flex: 1; min-width: 200px; max-width: 340px;
    background: var(--surface); border: 1px solid var(--border-strong); border-radius: 8px;
    padding: 0 10px; color: var(--faint); transition: border-color .12s, box-shadow .12s;
}
.search-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); color: var(--accent); }
.search-box input { border: none !important; padding: 7px 0 !important; background: transparent !important; box-shadow: none !important; }
.filters select, .sel-actions select { width: auto; padding: 7px 10px; font-size: 13px; font-weight: 500; cursor: pointer; }

.sel-bar { background: var(--accent-soft); border-bottom-color: #BAE6FD; padding: 12px 14px; min-height: 61px; }
.sel-count { font-size: 13.5px; color: var(--accent-dark); }
.sel-count b { font-variant-numeric: tabular-nums; }
.sel-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

.card-table th { padding: 9px 14px; }
.card-table td { padding: 11px 14px; }
.card-table tbody tr { transition: background .1s; }
.card-table tbody tr.is-sel { background: #F5FBFF; }
.card-table .num { text-align: center; }

.id-cell { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.copy-btn {
    display: inline-flex; padding: 4px; border: none; background: none;
    color: var(--faint); cursor: pointer; border-radius: 5px; transition: all .12s;
}
.copy-btn:hover { color: var(--accent); background: var(--accent-soft); }
.copy-btn .icon-check { display: none; }
.copy-btn.copied .icon-copy { display: none; }
.copy-btn.copied .icon-check { display: block; color: var(--ok); }

.biz-cell { min-width: 200px; max-width: 320px; }
.biz-name { display: block; font-weight: 600; }
.biz-addr { display: block; font-size: 12px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.badge-inactive .dot { background: #F59E0B; }
.status-note { display: block; font-size: 11.5px; color: var(--muted); margin-top: 4px; padding-left: 2px; white-space: nowrap; }
.note-warn { color: #2563EB; font-weight: 600; }

.scan-pill {
    display: inline-block; min-width: 28px; padding: 2px 8px;
    font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums;
    border-radius: 99px; background: #F3F4F6; color: var(--faint); text-align: center;
}
.scan-pill.has-scans { background: var(--accent-soft); color: var(--accent); }

.col-created { white-space: nowrap; }
.col-owner { white-space: nowrap; }
.owner-chip { font-size: 12px; font-weight: 600; color: #6D28D9; background: #F5F3FF; padding: 3px 9px; border-radius: 99px; }
.assign-form { padding-right: 8px; margin-right: auto; border-right: 1px solid #BAE6FD; }
.go { width: 44px; text-align: right; }
.go-btn {
    display: inline-flex; padding: 6px; border-radius: 7px; color: var(--faint); transition: all .12s;
}
.go-btn:hover, tr:hover .go-btn { color: var(--accent); background: var(--accent-soft); }

.faint { color: var(--faint); }
.muted-text { color: var(--muted); }

.list-foot {
    display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
    padding: 12px 14px; border-top: 1px solid var(--border); background: var(--subtle);
}

@media (max-width: 860px) {
    .search-box { max-width: none; flex-basis: 100%; }
    .col-created { display: none; }
}
@media (max-width: 560px) {
    .gen-form { width: 100%; }
    .gen-form button { margin-left: auto; }
    .biz-cell { min-width: 160px; }
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
