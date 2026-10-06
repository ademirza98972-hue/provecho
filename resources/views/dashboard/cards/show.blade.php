@extends('layouts.app')
@section('title', 'Detail Card')
@section('content')

@php
    $statusLabel = ['active' => 'Aktif', 'inactive' => 'Belum aktif', 'disabled' => 'Nonaktif'][$card->status];
    $isAdmin = auth()->user()->isAdmin();
    $lastScan = $scanStats['last'] ? \Illuminate\Support\Carbon::parse($scanStats['last']) : null;
@endphp

<div class="stack">

    <a href="{{ route('dashboard.cards.index') }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Daftar card
    </a>

    <div class="page-head">
        <div class="head-main">
            <h2 class="page-title {{ $card->owner_name ? '' : 'is-empty' }}">{{ $card->owner_name ?? 'Belum terhubung ke usaha' }}</h2>
            <div class="head-meta">
                <span class="mono id-chip">{{ $card->id }}</span>
                <span class="badge badge-{{ $card->status }}"><span class="dot"></span>{{ $statusLabel }}</span>
                @if($card->status === 'active')
                    <span class="head-note">sejak {{ $card->activated_at?->translatedFormat('j F Y') }}</span>
                @elseif($card->status === 'disabled')
                    <span class="head-note">sejak {{ $card->disabled_at?->translatedFormat('j F Y') }}</span>
                @elseif($card->printed_at)
                    <span class="head-note">QR dicetak {{ $card->printed_at->translatedFormat('j F Y') }}</span>
                @else
                    <span class="head-note note-info">QR belum dicetak</span>
                @endif
            </div>
        </div>
        @if($card->isActive() && $card->google_url)
        <a href="{{ $card->google_url }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
            Buka halaman ulasan
        </a>
        @endif
    </div>

    <div class="mini-kpis">
        <div class="mini-kpi">
            <span class="mini-label">Total scan</span>
            <span class="mini-value">{{ number_format($scanStats['total']) }}</span>
        </div>
        <div class="mini-kpi">
            <span class="mini-label">Scan 7 hari terakhir</span>
            <span class="mini-value">{{ number_format($scanStats['week']) }}</span>
        </div>
        <div class="mini-kpi">
            <span class="mini-label">Scan terakhir</span>
            <span class="mini-value mini-text" title="{{ $lastScan?->translatedFormat('j F Y, H:i') }}">{{ $lastScan?->diffForHumans() ?? 'Belum pernah' }}</span>
        </div>
    </div>

    <div class="detail-grid">

        <div class="stack">

            @if($card->status !== 'disabled')
            <div class="panel" x-data="placesSearch(@js($card->owner_name), @js($card->google_url), @js($card->owner_address), @js($card->place_id))">
                <div class="panel-head">
                    <div>
                        <span class="panel-title">{{ $card->isActive() ? 'Data usaha' : 'Aktifkan card' }}</span>
                        <p class="panel-sub">
                            {{ $card->isActive()
                                ? 'Ganti lokasi kalau pembeli minta lewat chat Shopee atau WhatsApp. Card fisik tidak perlu diubah.'
                                : 'Hubungkan card ke halaman ulasan Google milik pembeli sebelum dikirim.' }}
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ $card->isActive() ? route('dashboard.cards.update', $card) : route('dashboard.cards.activate', $card) }}" class="form-stack">
                        @csrf
                        @if($card->isActive()) @method('PUT') @endif

                        <div class="field combo">
                            <label for="place-search">Cari usaha di Google</label>
                            <input type="text" id="place-search" x-model="query" @input.debounce.400ms="search()"
                                   placeholder="Ketik nama usaha, lalu pilih dari daftar" autocomplete="off">
                            <div class="combo-list" x-show="results.length" x-cloak @click.outside="results = []">
                                <template x-for="r in results" :key="r.place_id">
                                    <div class="combo-item" @click="select(r)">
                                        <div class="combo-name" x-text="r.name"></div>
                                        <div class="combo-addr" x-text="r.address"></div>
                                    </div>
                                </template>
                            </div>
                            <span class="hint" x-show="!apiDown">Nama, alamat, dan link ulasan terisi otomatis.</span>
                            <span class="hint" x-show="apiDown" x-cloak style="color:var(--warn)">Pencarian Google sedang bermasalah. Pakai link Google Maps di bawah.</span>
                        </div>

                        <div class="or-sep"><span>atau</span></div>

                        <div class="field">
                            <label for="maps-url">Tempel link Google Maps</label>
                            <div class="inline-field">
                                <input type="text" id="maps-url" x-model="mapsUrl" @keydown.enter.prevent="resolveLink()"
                                       placeholder="https://maps.app.goo.gl/..." autocomplete="off">
                                <button type="button" class="btn btn-outline btn-sm"
                                        :disabled="!mapsUrl.trim() || linkLoading"
                                        @click="resolveLink()" x-text="linkLoading ? 'Memproses…' : 'Ambil data'"></button>
                            </div>
                            <span class="hint" x-show="!linkError">Link dari tombol "Bagikan" di Google Maps. Tidak memakai kuota pencarian Google.</span>
                            <span class="hint" x-show="linkError" x-cloak x-text="linkError" style="color:var(--bad)"></span>
                        </div>

                        <input type="hidden" name="place_id" x-model="placeId">
                        <input type="hidden" name="owner_address" x-model="address">

                        <div class="result-box">
                            <div class="field">
                                <label for="owner_name">Nama usaha</label>
                                <input type="text" id="owner_name" name="owner_name" x-model="ownerName" required>
                                <span class="hint" x-show="address" x-text="address"></span>
                            </div>
                            <div class="field">
                                <label for="google_url">Link ulasan Google</label>
                                <input type="url" id="google_url" name="google_url" x-model="googleUrl" required
                                       placeholder="https://search.google.com/local/writereview?placeid=...">
                                <span class="hint">Terisi otomatis. Edit manual hanya kalau perlu.</span>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">
                                {{ $card->isActive() ? 'Simpan perubahan' : 'Aktifkan card' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <span class="panel-title">Card dinonaktifkan</span>
                        <p class="panel-sub">Sejak {{ $card->disabled_at?->translatedFormat('j F Y, H:i') }}, scan card ini menampilkan halaman "Kartu tidak aktif".</p>
                    </div>
                </div>
                <div class="panel-body">
                    @unless($isAdmin)
                    <p class="hint">Card ini dinonaktifkan oleh admin Provecho. Hubungi admin kalau card perlu diaktifkan kembali.</p>
                    @else
                    <div class="choice-list">
                        <div class="choice">
                            <div>
                                <span class="choice-title">Aktifkan kembali</span>
                                <span class="choice-desc">Card kembali mengarah ke ulasan {{ $card->owner_name ?? 'usaha yang sama' }}.</span>
                            </div>
                            <form method="POST" action="{{ route('dashboard.cards.reactivate', $card) }}"
                                  onsubmit="return confirm('Aktifkan kembali card {{ $card->id }}?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Aktifkan kembali</button>
                            </form>
                        </div>
                        <div class="choice">
                            <div>
                                <span class="choice-title">Reset card</span>
                                <span class="choice-desc">Data usaha dihapus dan status kembali ke Belum aktif, jadi card bisa dipakai pembeli lain.</span>
                            </div>
                            <form method="POST" action="{{ route('dashboard.cards.reset', $card) }}"
                                  onsubmit="return confirm('Reset card {{ $card->id }}? Data usaha akan dihapus dan card kembali ke status Belum aktif.')">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">Reset</button>
                            </form>
                        </div>
                    </div>
                    @endunless
                </div>
            </div>
            @endif

            @if($isAdmin)
            <div class="panel danger-zone">
                <div class="panel-body danger-row">
                    @if($card->isActive())
                        <div>
                            <span class="choice-title">Nonaktifkan card</span>
                            <span class="choice-desc">Scan tidak lagi diarahkan ke halaman ulasan. Bisa diaktifkan kembali kapan saja.</span>
                        </div>
                        <form method="POST" action="{{ route('dashboard.cards.disable', $card) }}"
                              onsubmit="return confirm('Nonaktifkan card {{ $card->id }}? Pelanggan yang scan tidak akan diarahkan ke halaman ulasan.')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
                        </form>
                    @else
                        <div>
                            <span class="choice-title">Hapus card</span>
                            <span class="choice-desc">Card dan seluruh riwayat scan-nya dihapus permanen.</span>
                        </div>
                        <form method="POST" action="{{ route('dashboard.cards.destroy', $card) }}"
                              onsubmit="return confirm('Hapus permanen card {{ $card->id }}? Card dan semua riwayatnya tidak bisa dikembalikan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus card</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <div class="stack">

            @if($isAdmin)
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <span class="panel-title">Pemilik card</span>
                        <p class="panel-sub">Reseller yang memegang card ini bisa mengaktifkan dan mengubah datanya.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('dashboard.cards.assign') }}" class="print-row owner-row">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $card->id }}">
                        <select name="reseller" aria-label="Pemilik card">
                            <option value="none" @selected(! $card->reseller_id)>Stok admin</option>
                            @foreach($resellers as $r)
                                <option value="{{ $r->id }}" @selected($card->reseller_id === $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline btn-sm">Simpan</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">QR code</span>
                </div>
                <div class="panel-body">
                    <div class="qr-box">
                        <div class="qr-frame">{!! $qr !!}</div>
                        <div class="url-row">
                            <span class="url-chip">{{ $card->url }}</span>
                            <button type="button" class="copy-btn" title="Salin link card" aria-label="Salin link card"
                                    data-url="{{ $card->url }}"
                                    onclick="navigator.clipboard.writeText(this.dataset.url);this.classList.add('copied');setTimeout(()=>this.classList.remove('copied'),1200)">
                                <svg class="icon-copy" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                                <svg class="icon-check" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                            </button>
                        </div>
                        <p class="hint qr-hint">
                            @if($card->isActive())
                                Tap NFC atau scan QR langsung membuka halaman ulasan {{ $card->owner_name }}.
                            @elseif($isAdmin)
                                QR sudah bisa dicetak. Selama card belum aktif, scan menampilkan halaman "Kartu belum aktif".
                            @else
                                Selama card belum aktif, scan menampilkan halaman "Kartu belum aktif".
                            @endif
                        </p>
                    </div>
                    @if($isAdmin)
                    <form method="POST" action="{{ route('dashboard.cards.export.pdf') }}" class="print-row">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $card->id }}">
                        <select name="mode" aria-label="Ukuran cetak">
                            <option value="single">10×10 cm</option>
                            <option value="a4" selected>Kertas A4</option>
                            <option value="a3">Kertas A3</option>
                            <option value="sticker">Sticker</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                            Cetak QR
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Riwayat aktivitas</span>
                    <span class="hint">20 terbaru</span>
                </div>
                <div class="panel-body log-body">
                    @forelse($logs as $log)
                    <div class="log-item">
                        <span class="log-dot {{ $log->action_class }}"></span>
                        <span class="log-label">{{ $log->action_label }}</span>
                        <span class="log-device">{{ $log->device }}</span>
                        <span class="log-when" title="{{ $log->ip_address }}">{{ $log->created_at->translatedFormat('j M, H:i') }}</span>
                    </div>
                    @empty
                    <p class="hint" style="padding:12px 0">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }

.back-link { display: inline-flex; align-items: center; gap: 4px; align-self: flex-start; font-size: 13px; font-weight: 500; color: var(--muted); }
.back-link:hover { color: var(--accent); }

.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-top: -6px; }
.head-main { min-width: 0; }
.page-title { font-size: 22px; font-weight: 700; letter-spacing: -.02em; line-height: 1.25; text-wrap: balance; }
.page-title.is-empty { color: var(--faint); }
.head-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
.id-chip { font-size: 12.5px; color: var(--muted); background: #F3F4F6; padding: 3px 8px; border-radius: 6px; }
.head-note { font-size: 12.5px; color: var(--muted); }
.note-info { color: #2563EB; font-weight: 600; }
.badge-inactive .dot { background: #F59E0B; }

.mini-kpis { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
.mini-kpi { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 18px; display: flex; flex-direction: column; gap: 2px; }
.mini-label { font-size: 12.5px; color: var(--muted); font-weight: 500; }
.mini-value { font-size: 24px; font-weight: 700; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
.mini-text { font-size: 17px; line-height: 1.6; }

.detail-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 14px; align-items: start; }

.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; max-width: 60ch; }

.or-sep { display: flex; align-items: center; gap: 10px; font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--faint); }
.or-sep::before, .or-sep::after { content: ''; flex: 1; height: 1px; background: var(--border); }
.inline-field { display: flex; gap: 8px; }
.inline-field .btn { flex-shrink: 0; }
.result-box { display: flex; flex-direction: column; gap: 14px; padding: 16px; border-radius: 10px; background: var(--subtle); border: 1px solid var(--border); }

.choice-list { display: flex; flex-direction: column; }
.choice { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 12px 0; }
.choice + .choice { border-top: 1px solid #F3F4F6; }
.choice-title { display: block; font-size: 13.5px; font-weight: 600; }
.choice-desc { display: block; font-size: 12.5px; color: var(--muted); margin-top: 1px; }

.danger-zone { border-color: #FECACA; }
.danger-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }

.qr-frame { padding: 12px; border-radius: 12px; }
.url-row { display: flex; align-items: center; gap: 4px; max-width: 100%; }
.url-chip { min-width: 0; }
.copy-btn { display: inline-flex; padding: 6px; border: none; background: none; color: var(--faint); cursor: pointer; border-radius: 6px; transition: all .12s; }
.copy-btn:hover { color: var(--accent); background: var(--accent-soft); }
.copy-btn .icon-check { display: none; }
.copy-btn.copied .icon-copy { display: none; }
.copy-btn.copied .icon-check { display: block; color: var(--ok); }
.qr-hint { text-align: center; max-width: 36ch; }
.print-row { display: flex; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); }
.owner-row { margin-top: 0; padding-top: 0; border-top: 0; }
.print-row select { flex: 1; padding: 7px 10px; font-size: 13px; font-weight: 500; cursor: pointer; }

.log-body { padding-top: 6px; padding-bottom: 6px; max-height: 420px; overflow-y: auto; }
.log-item { display: flex; align-items: center; gap: 10px; padding: 9px 0; font-size: 13px; }
.log-item + .log-item { border-top: 1px solid #F3F4F6; }
.log-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: var(--faint); }
.log-dot.act-scan { background: var(--accent); }
.log-dot.act-ok   { background: var(--ok); }
.log-dot.act-info { background: #2563EB; }
.log-dot.act-warn { background: #F59E0B; }
.log-dot.act-bad  { background: var(--bad); }
.log-label { font-weight: 500; }
.log-device { font-size: 12px; color: var(--faint); }
.log-when { margin-left: auto; color: var(--muted); font-size: 12.5px; white-space: nowrap; font-variant-numeric: tabular-nums; }

@media (max-width: 1000px) {
    .detail-grid { grid-template-columns: minmax(0, 1fr); }
}
@media (max-width: 560px) {
    .mini-kpis { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
    .mini-kpi { padding: 12px; }
    .mini-value { font-size: 20px; }
    .mini-text { font-size: 13px; line-height: 1.9; }
    .choice, .danger-row { flex-direction: column; align-items: flex-start; gap: 10px; }
    .inline-field { flex-direction: column; }
}
</style>
@endpush

@push('scripts')
<script>
function placesSearch(initName, initUrl, initAddress, initPlaceId) {
    return {
        query: '', results: [], apiDown: false,
        mapsUrl: '', linkLoading: false, linkError: '',
        placeId: initPlaceId || '', address: initAddress || '',
        ownerName: initName || '', googleUrl: initUrl || '',

        async search() {
            this.apiDown = false;
            if (this.query.length < 2) { this.results = []; return; }
            try {
                const res = await fetch(`/dashboard/places/search?q=${encodeURIComponent(this.query)}`);
                if (res.ok) this.results = (await res.json()).results || [];
                else { this.results = []; this.apiDown = true; }
            } catch { this.results = []; this.apiDown = true; }
        },

        async resolveLink() {
            if (!this.mapsUrl.trim() || this.linkLoading) return;

            this.linkLoading = true;
            this.linkError = '';
            try {
                const res = await fetch(`{{ route('dashboard.places.resolve-maps') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    },
                    body: JSON.stringify({ url: this.mapsUrl.trim() }),
                });
                const data = await res.json();
                if (res.ok) { this.select(data.result); this.mapsUrl = ''; }
                else this.linkError = data.error || 'Link tidak bisa diproses.';
            } catch {
                this.linkError = 'Gagal menghubungi server.';
            }
            this.linkLoading = false;
        },

        select(r) {
            this.ownerName = r.name || this.ownerName;
            this.address   = r.address || '';
            this.placeId   = r.place_id;
            this.googleUrl = r.url;
            this.query     = r.name || this.query;
            this.results   = [];
        }
    }
}
</script>
@endpush

@endsection
