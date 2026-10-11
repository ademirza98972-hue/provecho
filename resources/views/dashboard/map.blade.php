@extends('layouts.app')
@section('title', 'Peta Pelanggan')
@section('content')

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Peta pelanggan</h2>
            <p class="page-sub">{{ $markers->count() }} usaha memakai Provecho. Cek dulu sebelum menawarkan ke toko di lapangan.</p>
        </div>
    </div>

    <div class="map-grid">
        <div class="panel map-panel">
            <div id="map" role="region" aria-label="Peta usaha pelanggan Provecho"></div>
        </div>

        <div class="stack">
            <div class="panel" x-data="placeCheck(@js($registered))">
                <div class="panel-head">
                    <div>
                        <span class="panel-title">Cek usaha</span>
                        <p class="panel-sub">Sudah pakai Provecho atau belum? Dicocokkan lewat data Google, bukan nama.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="search-box">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        <input type="text" x-model="q" @input.debounce.450ms="search()" placeholder="Ketik nama usaha" aria-label="Nama usaha" autocomplete="off">
                    </div>
                    <p class="hint" x-show="loading" x-cloak style="margin-top:10px">Mencari…</p>
                    <p class="hint err" x-show="error" x-cloak x-text="error" style="margin-top:10px"></p>
                    <ul class="check-list" x-show="results.length" x-cloak>
                        <template x-for="r in results" :key="r.place_id">
                            <li>
                                <div class="check-main">
                                    <span class="check-name" x-text="r.name"></span>
                                    <span class="check-addr" x-text="r.address"></span>
                                </div>
                                <template x-if="known(r)">
                                    <div class="check-side">
                                        <span class="tag tag-yes" x-text="known(r).active ? 'Sudah pakai Provecho' : 'Pernah pakai (nonaktif)'"></span>
                                        <button type="button" class="btn-text" x-show="known(r).lat" @click="window.focusCard(known(r).id)">Lihat di peta</button>
                                    </div>
                                </template>
                                <template x-if="!known(r)">
                                    <div class="check-side"><span class="tag tag-no">Belum terdaftar</span></div>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Legenda</span>
                </div>
                <div class="panel-body legend">
                    <span><i class="pv-pin"><img src="/apple-touch-icon.png" alt=""></i>Card aktif</span>
                    <span><i class="pv-pin off"><img src="/apple-touch-icon.png" alt=""></i>Card nonaktif</span>
                </div>
            </div>

            @if($missing->isNotEmpty())
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <span class="panel-title">Belum ada di peta · {{ $missing->count() }}</span>
                        <p class="panel-sub">Card aktif tanpa lokasi Google. Aktifkan ulang lewat pencarian Google atau link Maps supaya muncul.</p>
                    </div>
                </div>
                <ul class="miss-list">
                    @foreach($missing as $c)
                    <li>
                        <a href="{{ route('dashboard.cards.show', $c) }}">
                            <span>{{ $c->owner_name ?? 'Tanpa nama' }}</span>
                            <span class="mono id-chip">{{ $c->id }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
<style>
[x-cloak] { display: none !important; }
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }

.map-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 14px; align-items: start; }
.map-panel { padding: 0; }
#map { height: min(72vh, 680px); min-height: 420px; width: 100%; z-index: 0; }

.pv-pin {
    position: relative; display: inline-grid; place-items: center; width: 38px; height: 38px;
    border-radius: 50%; background: #fff; border: 2px solid #0EA5E9; box-shadow: 0 4px 12px -3px rgba(11,21,38,.45);
}
.pv-pin::after {
    content: ''; position: absolute; bottom: -8px; left: 50%; translate: -50% 0;
    border: 6px solid transparent; border-top: 7px solid #0EA5E9; border-bottom: 0;
}
.pv-pin img { width: 26px; height: 26px; border-radius: 50%; }
.pv-pin.off { border-color: #9CA3AF; }
.pv-pin.off::after { border-top-color: #9CA3AF; }
.pv-pin.off img { filter: grayscale(1); opacity: .6; }
.leaflet-div-icon { background: none; border: 0; }

.pop-count { display: block; font-size: 11.5px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
.pop-row + .pop-row { margin-top: 10px; padding-top: 10px; border-top: 1px solid #F3F4F6; }

.leaflet-popup-content { margin: 12px 14px; font-family: inherit; }
.pop-name { display: block; font-weight: 700; font-size: 14px; }
.pop-addr { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
.pop-meta { display: block; font-size: 12px; margin-top: 6px; }
.pop-link { display: inline-block; margin-top: 8px; font-size: 12.5px; font-weight: 600; color: var(--accent-dark) !important; }

.locate-btn {
    display: flex; align-items: center; gap: 6px; padding: 7px 11px; border-radius: 8px; background: #fff;
    font: 600 12.5px/1 inherit; color: var(--text); border: 0; cursor: pointer; box-shadow: 0 2px 8px rgba(11,21,38,.2);
}
.locate-btn:hover { color: var(--accent-dark); }

.search-box { display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-strong); border-radius: 8px; padding: 0 10px; color: var(--faint); }
.search-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
.search-box input { border: 0 !important; box-shadow: none !important; padding: 8px 0 !important; }
.err { color: var(--bad) !important; }
.check-list { list-style: none; margin-top: 12px; display: flex; flex-direction: column; }
.check-list li { display: flex; flex-direction: column; gap: 6px; padding: 10px 0; }
.check-list li + li { border-top: 1px solid #F3F4F6; }
.check-name { display: block; font-weight: 600; font-size: 13.5px; }
.check-addr { display: block; font-size: 12px; color: var(--muted); }
.check-side { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.tag { font-size: 11.5px; font-weight: 700; padding: 3px 9px; border-radius: 99px; }
.tag-yes { background: var(--ok-soft); color: var(--ok); }
.tag-no { background: #FFF7ED; color: #C2410C; }
.btn-text { background: none; border: 0; padding: 0; font: inherit; font-size: 12.5px; font-weight: 600; color: var(--accent-dark); cursor: pointer; }
.btn-text:hover { text-decoration: underline; }

.legend { display: flex; flex-direction: column; gap: 12px; font-size: 13px; }
.legend span { display: flex; align-items: center; gap: 12px; }
.legend .pv-pin { transform: scale(.8); }

.miss-list { list-style: none; max-height: 220px; overflow-y: auto; }
.miss-list a { display: flex; justify-content: space-between; gap: 10px; padding: 9px 18px; font-size: 13px; }
.miss-list li + li a { border-top: 1px solid #F3F4F6; }
.miss-list a:hover { background: var(--subtle); color: var(--accent-dark); }
.id-chip { font-size: 11.5px; color: var(--muted); background: #F3F4F6; padding: 2px 7px; border-radius: 6px; }

@media (max-width: 1000px) {
    .map-grid { grid-template-columns: minmax(0, 1fr); }
    #map { height: 60vh; min-height: 360px; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
(function () {
    var data = @json($markers);
    var map = L.map('map', { zoomControl: true }).setView([-2.5, 117.5], 5);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    function icon(active) {
        return L.divIcon({
            className: '',
            html: '<i class="pv-pin' + (active ? '' : ' off') + '"><img src="/apple-touch-icon.png" alt=""></i>',
            iconSize: [38, 46], iconAnchor: [19, 46], popupAnchor: [0, -44]
        });
    }

    // Isi popup dibuat sebagai teks, bukan HTML, karena nama/alamat berasal dari input pengguna.
    function popup(cards) {
        var box = document.createElement("div");
        var add = function (parent, tag, cls, text) { var el = document.createElement(tag); el.className = cls; el.textContent = text; parent.appendChild(el); return el; };
        if (cards.length > 1) add(box, "span", "pop-count", cards.length + " card di lokasi ini");
        cards.forEach(function (m) {
            var row = add(box, "div", "pop-row", "");
            add(row, "b", "pop-name", m.name);
            if (m.address && cards.length === 1) add(row, "span", "pop-addr", m.address);
            add(row, "span", "pop-meta", (m.active ? "Aktif" : "Nonaktif") + " · " + m.id + (m.reseller ? " · Reseller: " + m.reseller : ""));
            add(row, "a", "pop-link", "Buka detail card →").href = m.url;
        });
        return box;
    }

    // Satu logo per lokasi; beberapa card di titik yang sama digabung dalam satu popup.
    var places = {};
    data.forEach(function (m) {
        var key = m.lat.toFixed(6) + "," + m.lng.toFixed(6);
        (places[key] = places[key] || []).push(m);
    });

    var byId = {}, bounds = [];
    Object.keys(places).forEach(function (key) {
        var cards = places[key], first = cards[0];
        var active = cards.some(function (m) { return m.active; });
        var mk = L.marker([first.lat, first.lng], { icon: icon(active), title: first.name }).bindPopup(popup(cards)).addTo(map);
        cards.forEach(function (m) { byId[m.id] = mk; });
        bounds.push([first.lat, first.lng]);
    });
    if (bounds.length) map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });

    window.focusCard = function (id) {
        var mk = byId[id];
        if (!mk) return;
        map.setView(mk.getLatLng(), 17);
        mk.openPopup();
        document.getElementById("map").scrollIntoView({ behavior: "smooth", block: "center" });
    };

    var Locate = L.Control.extend({
        options: { position: 'topright' },
        onAdd: function () {
            var b = L.DomUtil.create('button', 'locate-btn');
            b.type = 'button';
            b.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>Lokasi saya';
            L.DomEvent.disableClickPropagation(b);
            L.DomEvent.on(b, 'click', function () { map.locate({ setView: true, maxZoom: 16, enableHighAccuracy: true }); });
            return b;
        }
    });
    map.addControl(new Locate());

    var me;
    map.on('locationfound', function (e) {
        if (me) map.removeLayer(me);
        me = L.circleMarker(e.latlng, { radius: 8, color: '#fff', weight: 3, fillColor: '#2563EB', fillOpacity: 1 }).addTo(map).bindTooltip('Kamu di sini');
    });
    map.on('locationerror', function () { alert('Lokasi tidak bisa diambil. Izinkan akses lokasi di browser.'); });
})();

function placeCheck(registered) {
    return {
        q: '', results: [], loading: false, error: '',
        known(r) { return registered[r.place_id] || null; },
        async search() {
            this.error = '';
            if (this.q.trim().length < 2) { this.results = []; return; }
            this.loading = true;
            try {
                const res = await fetch(`{{ route('dashboard.places.search') }}?q=${encodeURIComponent(this.q)}`, { headers: { Accept: 'application/json' } });
                const json = await res.json();
                if (res.ok) this.results = json.results || [];
                else { this.results = []; this.error = json.error || 'Pencarian gagal.'; }
            } catch { this.results = []; this.error = 'Gagal menghubungi server.'; }
            this.loading = false;
        }
    };
}
</script>
@endpush

@endsection
