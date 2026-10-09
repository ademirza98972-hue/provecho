@extends('layouts.app')
@section('title', 'Data Pembeli')
@section('content')

@php $fromActivation = session('activated'); @endphp

<div class="stack buyer-page">

    <a href="{{ route('dashboard.cards.show', $card) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Detail card
    </a>

    @if($fromActivation)
    <ol class="steps" aria-label="Langkah aktivasi">
        <li class="done"><span class="dot">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </span>Hubungkan ke Google</li>
        <li class="line" aria-hidden="true"></li>
        <li class="now" aria-current="step"><span class="dot">2</span>Data pembeli</li>
    </ol>
    @endif

    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="panel-title">Data pembeli</span>
                <p class="panel-sub">Dipakai untuk mencocokkan klaim garansi dan permintaan ganti lokasi. Semua kolom boleh dikosongkan.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="card-sum">
                <span class="mono id-chip">{{ $card->id }}</span>
                <span class="sum-name">{{ $card->owner_name ?? 'Belum terhubung ke usaha' }}</span>
            </div>

            <form method="POST" action="{{ route('dashboard.cards.buyer.update', $card) }}" class="form-stack">
                @csrf @method('PUT')

                <div class="field">
                    <label for="order_number">Nomor pesanan</label>
                    <input type="text" id="order_number" name="order_number" maxlength="50" autocomplete="off"
                           value="{{ old('order_number', $card->order_number) }}" placeholder="mis. 2410098KQ7XJ3P" @if($fromActivation) autofocus @endif>
                    <span class="hint">Nomor pesanan Shopee atau marketplace lain.</span>
                </div>

                <div class="field">
                    <label for="buyer_name">Nama pembeli</label>
                    <input type="text" id="buyer_name" name="buyer_name" maxlength="100" autocomplete="off"
                           value="{{ old('buyer_name', $card->buyer_name) }}" placeholder="mis. Budi Santoso">
                </div>

                <div class="field">
                    <label for="buyer_phone">Nomor HP / WhatsApp</label>
                    <input type="tel" id="buyer_phone" name="buyer_phone" maxlength="20" inputmode="tel" autocomplete="off"
                           value="{{ old('buyer_phone', $card->buyer_phone) }}" placeholder="mis. 0812 3456 7890">
                    @error('buyer_phone')<span class="hint err">{{ $message }}</span>@enderror
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Simpan data pembeli</button>
                    <a href="{{ route('dashboard.cards.show', $card) }}" class="btn btn-ghost">{{ $fromActivation ? 'Lewati, isi nanti' : 'Batal' }}</a>
                </div>
            </form>
        </div>
    </div>

</div>

@push('styles')
<style>
.buyer-page { max-width: 560px; }
.back-link { display: inline-flex; align-items: center; gap: 4px; align-self: flex-start; font-size: 13px; font-weight: 500; color: var(--muted); }
.back-link:hover { color: var(--accent); }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }

.steps { display: flex; align-items: center; gap: 10px; list-style: none; font-size: 13px; font-weight: 600; color: var(--muted); }
.steps li:not(.line) { display: inline-flex; align-items: center; gap: 8px; }
.steps .dot { width: 24px; height: 24px; border-radius: 50%; display: grid; place-items: center; font-size: 12px; font-weight: 700; background: #F3F4F6; color: var(--muted); }
.steps .done { color: var(--ok); }
.steps .done .dot { background: var(--ok-soft); color: var(--ok); }
.steps .now { color: var(--text); }
.steps .now .dot { background: var(--accent); color: #fff; }
.steps .line { flex: 0 0 32px; height: 2px; border-radius: 2px; background: var(--border); }

.card-sum { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; padding: 12px 14px; margin-bottom: 18px; border-radius: 10px; background: var(--subtle); border: 1px solid var(--border); }
.id-chip { font-size: 12px; color: var(--muted); background: #F3F4F6; padding: 2px 7px; border-radius: 6px; }
.sum-name { font-weight: 600; }
.actions { display: flex; gap: 8px; flex-wrap: wrap; }
.err { color: var(--bad) !important; }
input[type=tel] { width: 100%; padding: 8px 11px; border: 1px solid var(--border-strong); border-radius: 8px; font-size: 13.5px; font-family: inherit; color: var(--text); background: var(--surface); }
</style>
@endpush

@endsection
