@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')

@php
    $user = auth()->user();
    $appUrl = rtrim(config('app.url'), '/');
    $urlOk = str_starts_with($appUrl, 'https://') && ! preg_match('/localhost|127\.0\.0\.1|ngrok/i', $appUrl);
    $locales = ['id' => 'Indonesia', 'en' => 'Inggris'];
@endphp

<div class="stack">

    <div class="page-head">
        <div>
            <h2 class="page-title">Pengaturan</h2>
            <p class="page-sub">{{ $user->isAdmin() ? 'Akun admin dan informasi sistem Provecho.' : 'Akun kamu dan tampilan halaman aktivasi untuk pembelimu.' }}</p>
        </div>
    </div>

    <div class="detail-grid">

        <div class="stack">

        @cannot('admin')
            @include('dashboard._brand-form', ['owner' => $user, 'action' => route('dashboard.settings.brand'), 'subtitle' => 'Halaman yang dilihat pembeli saat scan card milikmu. Fungsi card tetap sama, hanya tampilannya yang berubah.'])
        @endcannot

        <div class="panel" x-data="{ show: false }">
            <div class="panel-head">
                <div>
                    <span class="panel-title">Ganti password</span>
                    <p class="panel-sub">Minimal 8 karakter. Pakai password yang tidak dipakai di akun lain.</p>
                </div>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('dashboard.settings.password') }}" class="form-stack pw-form">
                    @csrf @method('PUT')

                    <div class="field">
                        <label for="current_password">Password lama</label>
                        <input :type="show ? 'text' : 'password'" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                        @error('current_password')<span class="hint err">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="password">Password baru</label>
                        <input :type="show ? 'text' : 'password'" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                        @error('password')<span class="hint err">{{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Ulangi password baru</label>
                        <input :type="show ? 'text' : 'password'" type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
                    </div>

                    <label class="check-line">
                        <input type="checkbox" x-model="show">
                        Tampilkan password
                    </label>

                    <div>
                        <button type="submit" class="btn btn-primary">Simpan password</button>
                    </div>
                </form>
            </div>
        </div>

        </div>

        <div class="stack">

            <div class="panel">
                <div class="panel-head"><span class="panel-title">Akun</span></div>
                <div class="panel-body">
                    <div class="account">
                        <span class="account-ava">{{ strtoupper(mb_substr($user->name ?? $user->username, 0, 1)) }}</span>
                        <div>
                            <span class="account-name">{{ $user->name ?? $user->username }}</span>
                            <span class="account-user">Login sebagai <b>{{ $user->username }}</b> · {{ $user->isAdmin() ? 'Admin' : 'Reseller' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @can('admin')
            <div class="panel">
                <div class="panel-head"><span class="panel-title">Informasi sistem</span></div>
                <div class="panel-body">
                    <dl class="sys-list">
                        <div class="sys-row sys-col">
                            <dt>Alamat link card</dt>
                            <dd><span class="mono url-chip">{{ $appUrl }}/c/…</span></dd>
                            <dd class="sys-note {{ $urlOk ? '' : 'warn' }}">
                                {{ $urlOk
                                    ? 'QR dan chip NFC yang dicetak mengarah ke alamat ini.'
                                    : 'Alamat ini bukan domain HTTPS publik. Jangan cetak QR sebelum APP_URL di .env diganti ke domain asli.' }}
                            </dd>
                        </div>
                        <div class="sys-row">
                            <dt>Mode</dt>
                            <dd>
                                @if(app()->isProduction())
                                    <span class="badge badge-active"><span class="dot"></span>Production</span>
                                @else
                                    <span class="badge badge-inactive"><span class="dot"></span>{{ ucfirst(app()->environment()) }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="sys-row">
                            <dt>Bahasa tanggal</dt>
                            <dd>{{ $locales[app()->getLocale()] ?? app()->getLocale() }}</dd>
                        </div>
                        <div class="sys-row">
                            <dt>Zona waktu</dt>
                            <dd>{{ config('app.timezone') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @endcan

        </div>
    </div>
</div>

@push('styles')
<style>
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }

.detail-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 14px; align-items: start; }

.pw-form { max-width: 420px; }
.err { color: var(--bad) !important; }
.check-line { display: inline-flex; align-items: center; gap: 8px; font-weight: 500; color: var(--muted); cursor: pointer; }

.account { display: flex; align-items: center; gap: 12px; }
.account-ava {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; display: grid; place-items: center;
    font-size: 16px; font-weight: 700; color: #fff; background: linear-gradient(135deg, var(--accent), #22C55E);
}
.account-name { display: block; font-size: 15px; font-weight: 600; }
.account-user { display: block; font-size: 12.5px; color: var(--muted); }
.account-user b { font-weight: 600; color: var(--text); }

.sys-list { display: flex; flex-direction: column; }
.sys-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 11px 0; }
.sys-row + .sys-row { border-top: 1px solid #F3F4F6; }
.sys-row:first-child { padding-top: 0; }
.sys-row:last-child { padding-bottom: 0; }
.sys-col { flex-direction: column; align-items: stretch; gap: 6px; }
.sys-row dt { font-size: 12.5px; color: var(--muted); font-weight: 500; }
.sys-row dd { font-size: 13.5px; font-weight: 600; }
.sys-row .url-chip { display: block; text-align: left; }
.sys-note { font-size: 12px !important; font-weight: 400 !important; color: var(--muted); }
.sys-note.warn { color: var(--warn); font-weight: 600 !important; }
.badge-inactive .dot { background: #F59E0B; }


@media (max-width: 1000px) {
    .detail-grid { grid-template-columns: minmax(0, 1fr); }
}
</style>
@endpush


@endsection
