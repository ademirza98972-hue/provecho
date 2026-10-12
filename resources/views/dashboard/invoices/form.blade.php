@extends('layouts.app')
@php $editing = $invoice->exists; @endphp
@section('title', $editing ? 'Ubah Nota' : 'Buat Nota')
@section('content')

<div class="stack invoice-form" x-data="{ ids: @js(old('card_ids', $ids)), price: @js((int) old('price', $invoice->items->first()?->price ?? 0)), discount: @js((int) old('discount', $invoice->discount ?? 0)),
    count() { return new Set(this.ids.toUpperCase().split(/[\s,;]+/).filter(Boolean)).size },
    rupiah(n) { return 'Rp' + n.toLocaleString('id-ID') } }">

    @php $back = $editing ? route('dashboard.invoices.show', $invoice) : route('dashboard.invoices.index'); @endphp
    <a href="{{ $back }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        {{ $editing ? 'Detail nota' : 'Daftar nota' }}
    </a>

    <form method="POST" action="{{ $editing ? route('dashboard.invoices.update', $invoice) : route('dashboard.invoices.store') }}" class="panel">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="panel-head">
            <div>
                <span class="panel-title">{{ $editing ? 'Ubah nota ' . $invoice->number : 'Nota penjualan offline' }}</span>
                <p class="panel-sub">Data pembeli otomatis tersimpan ke tiap card, dan nomor nota jadi nomor pesanannya.{{ $editing ? ' Card yang dihapus dari nota ikut dikosongkan data pembelinya.' : '' }}</p>
            </div>
        </div>
        <div class="panel-body form-stack">
            <div class="field">
                <label for="card_ids">ID card yang dijual</label>
                <textarea id="card_ids" name="card_ids" rows="4" x-model="ids" class="mono" placeholder="PV7K3M9Q&#10;PV2XH8RT" required @if(! $ids) autofocus @endif></textarea>
                <span class="hint">Satu ID per baris, atau pisahkan dengan koma. <b x-text="count()"></b> card.</span>
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="buyer_name">Nama pembeli</label>
                    <input type="text" id="buyer_name" name="buyer_name" maxlength="100" required value="{{ old('buyer_name', $invoice->buyer_name) }}" placeholder="mis. Budi Santoso" @if($ids) autofocus @endif>
                </div>
                <div class="field">
                    <label for="buyer_phone">Nomor HP / WhatsApp</label>
                    <input type="tel" id="buyer_phone" name="buyer_phone" maxlength="20" inputmode="tel" value="{{ old('buyer_phone', $invoice->buyer_phone) }}" placeholder="mis. 0812 3456 7890">
                    <span class="hint">Diisi agar nota bisa dikirim lewat WhatsApp.</span>
                </div>
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="price">Harga per card (Rp)</label>
                    <input type="number" id="price" name="price" min="0" step="500" required x-model.number="price">
                </div>
                <div class="field">
                    <label for="discount">Diskon total (Rp)</label>
                    <input type="number" id="discount" name="discount" min="0" step="500" x-model.number="discount">
                </div>
            </div>

            <div class="field">
                <label for="payment_method">Metode bayar</label>
                <select id="payment_method" name="payment_method" required>
                    @foreach(\App\Models\Invoice::PAYMENT_METHODS as $k => $v)
                        <option value="{{ $k }}" @selected(old('payment_method', $invoice->payment_method ?? 'tunai') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="notes">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="2" maxlength="500" placeholder="mis. Garansi 30 hari, pasang di meja kasir">{{ old('notes', $invoice->notes) }}</textarea>
            </div>

            <div class="total-box">
                <span>Total</span>
                <b x-text="rupiah(Math.max(0, count() * (price || 0) - (discount || 0)))"></b>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">{{ $editing ? 'Simpan perubahan' : 'Simpan & buat nota' }}</button>
                <a href="{{ $back }}" class="btn btn-ghost">Batal</a>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.invoice-form { max-width: 620px; }
.back-link { display: inline-flex; align-items: center; gap: 4px; align-self: flex-start; font-size: 13px; font-weight: 500; color: var(--muted); }
.back-link:hover { color: var(--accent); }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 560px) { .row-2 { grid-template-columns: 1fr; } }
.total-box { display: flex; justify-content: space-between; align-items: baseline; padding: 12px 14px; border-radius: 10px; background: var(--accent-soft); font-weight: 600; }
.total-box b { font-size: 20px; font-variant-numeric: tabular-nums; }
.actions { display: flex; gap: 8px; flex-wrap: wrap; }
input[type=tel] { width: 100%; padding: 8px 11px; border: 1px solid var(--border-strong); border-radius: 8px; font-size: 13.5px; font-family: inherit; color: var(--text); background: var(--surface); }
</style>
@endpush

@endsection
