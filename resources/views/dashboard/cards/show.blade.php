@extends('layouts.app')
@section('title', 'Card ' . $card->id)
@section('content')

@php
    $statusLabel = ['active' => 'Aktif', 'inactive' => 'Belum Aktif', 'disabled' => 'Dinonaktifkan'][$card->status];
@endphp

<div class="stack">

    <div class="toolbar">
        <a href="{{ route('dashboard.cards.index') }}" class="btn btn-ghost btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kelola Card
        </a>
        <span class="badge badge-{{ $card->status }}"><span class="dot"></span>{{ $statusLabel }}</span>
        @if($card->printed_at)
            <span class="badge badge-printed">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                Dicetak {{ $card->printed_at->format('d M Y') }}
            </span>
        @endif
    </div>

    <div class="grid-2">

        {{-- ── kolom kiri ── --}}
        <div class="stack">

            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Informasi Card</span>
                    <span class="mono" style="color:var(--muted)">{{ $card->id }}</span>
                </div>
                <div class="panel-body">
                    <div class="meta">
                        <div class="meta-row">
                            <span class="meta-key">Nama Toko</span>
                            <span class="meta-val">{{ $card->owner_name ?? '—' }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-key">Alamat</span>
                            <span class="meta-val" style="color:{{ $card->owner_address ? 'var(--muted)' : 'var(--faint)' }}">{{ $card->owner_address ?? '—' }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-key">Link Google Review</span>
                            @if($card->google_url)
                                <a href="{{ $card->google_url }}" target="_blank" rel="noopener" class="meta-val link">{{ $card->google_url }}</a>
                            @else
                                <span class="meta-val" style="color:var(--faint)">Belum diatur</span>
                            @endif
                        </div>
                        @if($card->activated_at)
                        <div class="meta-row">
                            <span class="meta-key">Diaktifkan</span>
                            <span class="meta-val" style="color:var(--muted)">{{ $card->activated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        @if($card->disabled_at)
                        <div class="meta-row">
                            <span class="meta-key">Dinonaktifkan</span>
                            <span class="meta-val" style="color:var(--muted)">{{ $card->disabled_at->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- form: aktivasi atau ubah data --}}
            @if($card->status !== 'disabled')
            <div class="panel" x-data="placesSearch(@js($card->owner_name), @js($card->google_url))">
                <div class="panel-head">
                    <span class="panel-title">{{ $card->isActive() ? 'Ubah Data Toko' : 'Aktifkan Card' }}</span>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ $card->isActive() ? route('dashboard.cards.update', $card) : route('dashboard.cards.activate', $card) }}" class="form-stack">
                        @csrf
                        @if($card->isActive()) @method('PUT') @endif

                        <div class="field combo">
                            <label for="place-search">Cari Toko di Google</label>
                            <input type="text" id="place-search" x-model="query" @input.debounce.400ms="search()"
                                   placeholder="Ketik nama toko, lalu pilih dari daftar" autocomplete="off">
                            <div class="combo-list" x-show="results.length" x-cloak @click.outside="results = []">
                                <template x-for="r in results" :key="r.place_id">
                                    <div class="combo-item" @click="select(r)">
                                        <div class="combo-name" x-text="r.name"></div>
                                        <div class="combo-addr" x-text="r.address"></div>
                                    </div>
                                </template>
                            </div>
                            <span class="hint" x-show="!apiDown">Nama dan link review akan terisi otomatis.</span>
                            <span class="hint" x-show="apiDown" x-cloak style="color:var(--warn)">
                                Pencarian Google sedang bermasalah — pakai link Maps di bawah.
                            </span>
                        </div>

                        <div class="field">
                            <label for="maps-url">Atau Tempel Link Google Maps</label>
                            <div style="display:flex;gap:8px">
                                <input type="text" id="maps-url" x-model="mapsUrl" @keydown.enter.prevent="resolveLink()"
                                       placeholder="https://maps.app.goo.gl/... atau link Maps lengkap" autocomplete="off">
                                <button type="button" class="btn btn-outline btn-sm" style="flex-shrink:0"
                                        :disabled="!mapsUrl.trim() || linkLoading"
                                        @click="resolveLink()" x-text="linkLoading ? 'Cek…' : 'Konversi'"></button>
                            </div>
                            <span class="hint" x-show="!linkError">Dikonversi jadi link review tanpa memakai kuota Places.</span>
                            <span class="hint" x-show="linkError" x-cloak x-text="linkError" style="color:var(--bad)"></span>
                        </div>

                        <input type="hidden" name="place_id" x-model="placeId">
                        <input type="hidden" name="owner_address" x-model="address">

                        <div class="field">
                            <label for="owner_name">Nama Toko</label>
                            <input type="text" id="owner_name" name="owner_name" x-model="ownerName" required>
                        </div>

                        <div class="field">
                            <label for="google_url">Link Google Review</label>
                            <input type="url" id="google_url" name="google_url" x-model="googleUrl" required
                                   placeholder="https://search.google.com/local/writereview?placeid=...">
                            <span class="hint">Bisa diisi atau diedit manual bila perlu.</span>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">
                                {{ $card->isActive() ? 'Simpan Perubahan' : 'Aktifkan Card' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="panel-head" style="border-bottom:none;border-top:1px solid var(--border)">
                    @if($card->isActive())
                    <span class="hint">Nonaktifkan agar card tidak lagi mengarah ke halaman review.</span>
                    <form method="POST" action="{{ route('dashboard.cards.disable', $card) }}"
                          onsubmit="return confirm('Nonaktifkan card {{ $card->id }}?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
                    </form>
                    @else
                    <span class="hint">Hapus card ini secara permanen.</span>
                    <form method="POST" action="{{ route('dashboard.cards.destroy', $card) }}"
                          onsubmit="return confirm('HAPUS PERMANEN card {{ $card->id }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus Card</button>
                    </form>
                    @endif
                </div>
            </div>
            @else
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Card Dinonaktifkan</span>
                </div>
                <div class="panel-body">
                    <p class="hint" style="margin-bottom:16px">Card ini dinonaktifkan pada {{ $card->disabled_at?->format('d M Y, H:i') }}. Aktifkan kembali dengan data toko yang sama, atau reset agar bisa diaktivasi ulang oleh pemilik baru.</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <form method="POST" action="{{ route('dashboard.cards.reactivate', $card) }}"
                              onsubmit="return confirm('Aktifkan kembali card {{ $card->id }}?')">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Aktifkan Kembali</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.cards.reset', $card) }}"
                              onsubmit="return confirm('Reset card {{ $card->id }}? Data toko akan dihapus dan card kembali ke status Belum Aktif.')">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">Reset Card</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.cards.destroy', $card) }}"
                              onsubmit="return confirm('HAPUS PERMANEN card {{ $card->id }}? Card dan semua log-nya akan dihapus dan tidak bisa dikembalikan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus Card</button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- ── kolom kanan ── --}}
        <div class="stack">

            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">QR Code</span>
                    <form method="POST" action="{{ route('dashboard.cards.export.pdf') }}" x-data="{ mode: 'a4' }" style="display:flex;align-items:center;gap:6px">
                        @csrf
                        <input type="hidden" name="ids[]" value="{{ $card->id }}">
                        <input type="hidden" name="mode" :value="mode">
                        <select x-model="mode" style="border:1px solid var(--border);border-radius:6px;padding:5px 8px;font-size:12px;font-weight:600;background:#fff;cursor:pointer">
                            <option value="single">10×10 cm</option>
                            <option value="a4">A4</option>
                            <option value="a3">A3</option>
                            <option value="sticker">Sticker</option>
                        </select>
                        <button type="submit" class="btn btn-outline btn-sm">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
                            Cetak
                        </button>
                    </form>
                </div>
                <div class="panel-body">
                    <div class="qr-box">
                        <div class="qr-frame">{!! $qr !!}</div>
                        <div class="url-chip">{{ $card->url }}</div>
                        <span class="hint" style="text-align:center">
                            @if($card->isActive())
                                Scan QR atau tap NFC akan diarahkan ke halaman review toko.
                            @else
                                QR sudah bisa dicetak sekarang. Selama card tidak berstatus aktif, scan akan menampilkan halaman "Kartu Belum Aktif".
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><span class="panel-title">Log Aktivitas</span></div>
                <div class="panel-body">
                    @forelse($logs as $log)
                    <div class="log-row">
                        <span class="log-time">{{ $log->created_at->format('d/m H:i') }}</span>
                        <span class="log-act">{{ $log->action }}</span>
                        <span class="log-ip">{{ $log->ip_address }}</span>
                    </div>
                    @empty
                    <p class="hint">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>[x-cloak]{display:none!important}</style>
@endpush

@push('scripts')
<script>
function placesSearch(initName, initUrl) {
    return {
        query: '', results: [], apiDown: false,
        mapsUrl: '', linkLoading: false, linkError: '',
        placeId: '', address: '',
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
