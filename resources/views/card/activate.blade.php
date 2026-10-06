<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Aktifkan Kartu — Provecho</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
    :root {
        --surface: #FFFFFF; --bg: #F9FAFB; --subtle: #FCFCFD;
        --border: #E5E7EB; --border-strong: #D4D7DD;
        --text: #111827; --muted: #6B7280; --faint: #9CA3AF;
        --accent: #0EA5E9; --accent-dark: #0284C7; --accent-soft: #F0F9FF;
        --ok: #15803D; --ok-soft: #F0FDF4;
        --bad: #B91C1C; --bad-soft: #FEF2F2;
        color-scheme: light;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    [x-cloak] { display: none !important; }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg); color: var(--text);
        font-size: 15px; line-height: 1.55;
        -webkit-font-smoothing: antialiased;
        padding: 28px 16px 40px;
        display: flex; justify-content: center;
    }
    .wrap { width: 100%; max-width: 440px; display: flex; flex-direction: column; gap: 20px; }

    .brand      { display: flex; align-items: center; justify-content: center; }
    .brand-mark { width: 72px; height: 72px; object-fit: contain; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; padding: 22px; }

    h1       { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
    .lead    { color: var(--muted); font-size: 14px; margin-top: 5px; }
    .card-id { display: inline-flex; align-items: center; gap: 7px; margin-top: 14px; font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 13px; font-weight: 600; background: var(--subtle); border: 1px solid var(--border); border-radius: 8px; padding: 6px 11px; color: var(--muted); }

    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; }
    input[type=text] {
        width: 100%; padding: 12px 14px;
        border: 1px solid var(--border-strong); border-radius: 10px;
        font-size: 16px; /* >=16px: cegah iOS auto-zoom saat fokus */
        font-family: inherit; color: var(--text); background: var(--surface);
        transition: border-color .12s, box-shadow .12s;
    }
    input[type=text]:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
    input::placeholder { color: var(--faint); }
    .hint { font-size: 13px; color: var(--muted); margin-top: 9px; }

    .combo      { position: relative; }
    .combo-list { position: absolute; z-index: 20; top: calc(100% + 5px); left: 0; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 11px; box-shadow: 0 10px 28px rgba(17,24,39,.12); max-height: 300px; overflow-y: auto; padding: 5px; }
    .combo-item { padding: 11px 12px; border-radius: 8px; cursor: pointer; }
    .combo-item:active, .combo-item:hover { background: #F3F4F6; }
    .combo-name { font-size: 14.5px; font-weight: 600; }
    .combo-addr { font-size: 13px; color: var(--muted); margin-top: 2px; }
    .searching  { padding: 12px; font-size: 13px; color: var(--muted); text-align: center; }

    .chosen      { display: flex; gap: 12px; align-items: flex-start; background: var(--ok-soft); border: 1px solid #BBF7D0; border-radius: 11px; padding: 14px; }
    .chosen-name { font-size: 15px; font-weight: 600; }
    .chosen-addr { font-size: 13px; color: var(--muted); margin-top: 2px; }
    .chosen svg  { color: var(--ok); margin-top: 2px; }

    .btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 14px; border-radius: 11px;
        font-size: 15px; font-weight: 600; font-family: inherit;
        cursor: pointer; border: 1px solid transparent; background: var(--accent); color: #fff;
        transition: background .12s;
    }
    .btn:hover { background: var(--accent-dark); }
    .btn:disabled { opacity: .4; cursor: not-allowed; background: var(--accent); }
    .btn-link { background: none; border: none; color: var(--accent); font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; padding: 0; width: auto; }
    .btn-link:hover { text-decoration: underline; background: none; }

    .notice      { font-size: 13px; border-radius: 9px; padding: 10px 12px; margin-top: 10px; background: var(--subtle); border: 1px solid var(--border); color: var(--muted); }
    .notice-warn { background: #FFFBEB; border-color: #FDE68A; color: #92400E; }
    .notice-bad  { background: var(--bad-soft); border-color: #FECACA; color: var(--bad); }

    .switch { margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border); }

    .verify { display: inline-flex; align-items: center; gap: 7px; font-size: 13.5px; font-weight: 600; color: var(--accent); text-decoration: none; }
    .verify:hover { text-decoration: underline; }

    .stack { display: flex; flex-direction: column; gap: 18px; }
    .error { display: flex; align-items: flex-start; gap: 9px; background: var(--bad-soft); border: 1px solid #FECACA; color: var(--bad); border-radius: 10px; padding: 11px 14px; font-size: 13.5px; font-weight: 500; }
    .foot  { text-align: center; font-size: 12.5px; color: var(--faint); }
    </style>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
</head>
<body>
<div class="wrap">

    <div class="brand">
        <img class="brand-mark" src="/img/logo.png" alt="Provecho">
    </div>

    <div class="panel" x-data="activation()">
        <h1>Aktifkan Kartu Anda</h1>
        <p class="lead">Pilih toko Anda di Google. Setelah aktif, setiap tap atau scan kartu ini akan langsung membuka halaman ulasan toko Anda.</p>
        <div class="card-id">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
            {{ $card->id }}
        </div>

        @if($errors->any())
            <div class="error" style="margin-top:18px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:1px"><circle cx="12" cy="12" r="9"/><path d="M12 8v4.5"/><path d="M12 16h.01"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('card.activate', $card->id) }}" class="stack" style="margin-top:20px">
            @csrf

            {{-- cara 1: cari nama toko --}}
            <div x-show="!chosen && mode === 'search'" x-cloak>
                <label for="q">Cari Nama Toko</label>
                <div class="combo">
                    <input type="text" id="q" x-model="query" @input.debounce.450ms="search()"
                           placeholder="Contoh: Warung Kopi Pak Budi" autocomplete="off" autocapitalize="words">

                    <div class="combo-list" x-show="results.length || loading" x-cloak @click.outside="results = []">
                        <template x-if="loading">
                            <div class="searching">Mencari…</div>
                        </template>
                        <template x-for="r in results" :key="r.place_id">
                            <div class="combo-item" @click="pick(r)">
                                <div class="combo-name" x-text="r.name"></div>
                                <div class="combo-addr" x-text="r.address"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <p class="hint" x-show="!notFound && !apiDown">Ketik nama toko seperti yang terdaftar di Google Maps.</p>

                <div class="notice" x-show="notFound" x-cloak>
                    Toko tidak ditemukan. Coba tambahkan nama kota, atau gunakan cara kedua di bawah.
                </div>
                <div class="notice notice-warn" x-show="apiDown" x-cloak>
                    Pencarian Google sedang bermasalah — bukan berarti toko Anda tidak terdaftar.
                    Silakan pakai cara kedua di bawah.
                </div>

                <div class="switch">
                    <button type="button" class="btn-link" @click="mode = 'link'">
                        Tempel link Google Maps saja
                    </button>
                </div>
            </div>

            {{-- cara 2: tempel link maps --}}
            <div x-show="!chosen && mode === 'link'" x-cloak>
                <label for="maps">Link Google Maps Toko</label>
                <input type="text" id="maps" x-model="mapsUrl" @keydown.enter.prevent="resolveLink()"
                       placeholder="Tempel link di sini" autocomplete="off" autocapitalize="off" spellcheck="false">
                <p class="hint">
                    Buka Google Maps, cari toko Anda, tekan <strong>Share</strong> lalu <strong>Copy link</strong>,
                    dan tempel di atas.
                </p>

                <div class="notice notice-bad" x-show="linkError" x-cloak x-text="linkError"></div>

                <button type="button" class="btn" style="margin-top:14px"
                        :disabled="!mapsUrl.trim() || linkLoading"
                        @click="resolveLink()"
                        x-text="linkLoading ? 'Memeriksa…' : 'Lanjut'"></button>

                <div class="switch">
                    <button type="button" class="btn-link" @click="mode = 'search'">
                        Kembali ke pencarian nama
                    </button>
                </div>
            </div>

            {{-- toko terpilih --}}
            <div x-show="chosen" x-cloak class="stack">
                <div class="chosen">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg>
                    <div>
                        <div class="chosen-name" x-text="chosen?.name || 'Toko Anda'"></div>
                        <div class="chosen-addr" x-show="chosen?.address" x-text="chosen?.address"></div>
                    </div>
                </div>

                <a class="verify" :href="chosen?.url" target="_blank" rel="noopener">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
                    Cek dulu halaman ulasannya
                </a>

                <button type="button" class="btn-link" @click="reset()">Bukan ini, pilih ulang</button>
            </div>

            <input type="hidden" name="owner_name"    :value="chosen?.name">
            <input type="hidden" name="owner_address" :value="chosen?.address">
            <input type="hidden" name="place_id"      :value="chosen?.place_id">
            <input type="hidden" name="google_url"    :value="chosen?.url">

            <button type="submit" class="btn" :disabled="!chosen">Aktifkan Kartu</button>
        </form>
    </div>

    <p class="foot">PROVECHO GOOGLE REVIEW</p>
</div>

<script>
function activation() {
    return {
        mode: 'search',
        query: '', results: [], loading: false, notFound: false, apiDown: false,
        mapsUrl: '', linkLoading: false, linkError: '',
        chosen: null,

        async search() {
            this.notFound = this.apiDown = false;
            if (this.query.trim().length < 3) { this.results = []; return; }

            this.loading = true;
            try {
                const res = await fetch(`{{ route('card.places', $card->id) }}?q=${encodeURIComponent(this.query)}`);
                if (res.ok) {
                    this.results  = (await res.json()).results || [];
                    this.notFound = this.results.length === 0;
                } else {
                    this.results = [];
                    this.apiDown = true;
                }
            } catch {
                this.results = [];
                this.apiDown = true;
            }
            this.loading = false;
        },

        async resolveLink() {
            if (!this.mapsUrl.trim() || this.linkLoading) return;

            this.linkLoading = true;
            this.linkError = '';
            try {
                const res = await fetch(`{{ route('card.resolve-maps', $card->id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    },
                    body: JSON.stringify({ url: this.mapsUrl.trim() }),
                });
                const data = await res.json();
                if (res.ok) this.chosen = data.result;
                else this.linkError = data.error || 'Link tidak bisa diproses.';
            } catch {
                this.linkError = 'Gagal menghubungi server. Periksa koneksi Anda.';
            }
            this.linkLoading = false;
        },

        pick(r) { this.chosen = r; this.results = []; },

        reset() {
            this.chosen = null;
            this.query = this.mapsUrl = this.linkError = '';
            this.results = [];
            this.notFound = this.apiDown = false;
        }
    }
}
</script>
</body>
</html>
