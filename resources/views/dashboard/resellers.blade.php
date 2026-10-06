@extends('layouts.app')
@section('title', 'Reseller')
@section('content')

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Reseller</h2>
            <p class="page-sub">Buat akun reseller, lalu berikan card dari halaman Kelola Card. Reseller hanya bisa melihat dan mengaktifkan card miliknya.</p>
        </div>
    </div>

    <div class="detail-grid">

        <div class="panel">
            <div class="panel-head">
                <span class="panel-title">Daftar reseller</span>
                <span class="hint">{{ $resellers->count() }} reseller</span>
            </div>
            <div class="table-wrap">
                <table class="res-table">
                    <thead>
                        <tr>
                            <th>Reseller</th>
                            <th class="num">Card</th>
                            <th class="num">Aktif</th>
                            <th class="num">Scan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resellers as $r)
                        <tr x-data="{ pw: false }">
                            <td>
                                <div class="res-cell">
                                    <span class="res-ava">{{ strtoupper(mb_substr($r->name, 0, 1)) }}</span>
                                    <div>
                                        <span class="res-name">{{ $r->name }}</span>
                                        <span class="res-user">{{ $r->username }}</span>
                                    </div>
                                </div>
                                <form x-show="pw" x-cloak method="POST" action="{{ route('dashboard.resellers.password', $r) }}" class="pw-inline">
                                    @csrf @method('PUT')
                                    <input type="text" name="password" minlength="8" required placeholder="Password baru (min. 8 karakter)" aria-label="Password baru {{ $r->name }}" autocomplete="off">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    <button type="button" class="btn btn-ghost btn-sm" @click="pw = false">Batal</button>
                                </form>
                            </td>
                            <td class="num"><b>{{ number_format($r->cards_count) }}</b></td>
                            <td class="num">{{ number_format($r->active_count) }}</td>
                            <td class="num">{{ number_format($scans[$r->id] ?? 0) }}</td>
                            <td>
                                <div class="acts">
                                <a href="{{ route('dashboard.cards.index', ['reseller' => $r->id]) }}" class="btn btn-ghost btn-sm">Lihat card</a>
                                <button type="button" class="btn btn-ghost btn-sm" @click="pw = !pw">Ganti password</button>
                                <form method="POST" action="{{ route('dashboard.resellers.destroy', $r) }}"
                                      onsubmit="return confirm('Hapus akun {{ $r->name }}? {{ $r->cards_count }} card-nya kembali ke stok admin dan tetap berfungsi.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm del">Hapus</button>
                                </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="empty">Belum ada reseller. Buat akun pertama lewat form di samping.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="stack">

        @if($resellers->isNotEmpty())
        <div class="panel" x-data="{ text: @js(old('ids_text', '')) }">
            <div class="panel-head">
                <div>
                    <span class="panel-title">Kirim card ke reseller</span>
                    <p class="panel-sub">Ambil kartu fisik dari stok, ketik atau scan ID yang tercetak di setiap kartu, lalu simpan.</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('dashboard.resellers.assign-ids') }}" class="form-stack">
                    @csrf
                    <div class="field">
                        <label for="a-reseller">Reseller tujuan</label>
                        <select id="a-reseller" name="reseller_id" required>
                            <option value="" disabled @selected(! old('reseller_id'))>Pilih reseller</option>
                            @foreach($resellers as $r)
                                <option value="{{ $r->id }}" @selected(old('reseller_id') == $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <div class="label-row">
                            <label for="a-ids">ID card</label>
                            <span class="id-count" x-text="(text.split(/[\s,;]+/).filter(Boolean).length) + ' ID terbaca'"></span>
                        </div>
                        <textarea id="a-ids" name="ids_text" rows="7" required x-model="text"
                                  class="mono ids-box" placeholder="PV7K2MQX&#10;PV3ABCDE&#10;PV9XYZ12"></textarea>
                        <span class="hint">Satu ID per baris. Link hasil scan QR juga bisa ditempel langsung.</span>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Pindahkan ke reseller</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="panel">
            <div class="panel-head">
                <div>
                    <span class="panel-title">Tambah reseller</span>
                    <p class="panel-sub">Kirim username dan password ke reseller. Mereka login di halaman yang sama dengan kamu.</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('dashboard.resellers.store') }}" class="form-stack">
                    @csrf
                    <div class="field">
                        <label for="r-name">Nama reseller</label>
                        <input type="text" id="r-name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="mis. Budi Banjarmasin">
                    </div>
                    <div class="field">
                        <label for="r-username">Username</label>
                        <input type="text" id="r-username" name="username" value="{{ old('username') }}" required maxlength="50" pattern="[A-Za-z0-9_\-]+" placeholder="mis. budi" autocomplete="off">
                        <span class="hint">Huruf, angka, strip, atau garis bawah. Dipakai untuk login.</span>
                    </div>
                    <div class="field">
                        <label for="r-password">Password</label>
                        <input type="text" id="r-password" name="password" required minlength="8" autocomplete="off">
                        <span class="hint">Minimal 8 karakter. Reseller bisa menggantinya sendiri di Pengaturan.</span>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Buat akun reseller</button>
                    </div>
                </form>
            </div>
        </div>

        </div>

    </div>
</div>

@push('styles')
<style>
[x-cloak] { display: none !important; }
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; max-width: 70ch; }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.detail-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 14px; align-items: start; }

.label-row { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; }
.id-count { font-size: 12px; font-weight: 600; color: var(--accent-dark); font-variant-numeric: tabular-nums; }
.ids-box { resize: vertical; min-height: 140px; line-height: 1.6; }
.res-table td { padding: 12px 14px; }
.res-table th { padding: 9px 14px; }
.res-table .num { text-align: center; font-variant-numeric: tabular-nums; }
.res-cell { display: flex; align-items: center; gap: 10px; }
.res-ava {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; display: grid; place-items: center;
    font-size: 13px; font-weight: 700; color: #6D28D9; background: #F5F3FF;
}
.res-name { display: block; font-weight: 600; }
.res-user { display: block; font-size: 12px; color: var(--muted); font-family: ui-monospace, Menlo, Consolas, monospace; }
.acts { display: flex; justify-content: flex-end; gap: 2px; flex-wrap: wrap; }
.acts .del:hover { color: var(--bad); background: var(--bad-soft); }
.pw-inline { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
.pw-inline input { flex: 1; min-width: 180px; }

@media (max-width: 1000px) {
    .detail-grid { grid-template-columns: minmax(0, 1fr); }
}
</style>
@endpush

@endsection
