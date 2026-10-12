@extends('layouts.app')
@section('title', 'Detail Nota')
@section('content')

<div class="stack invoice-page">

    <a href="{{ route('dashboard.invoices.index') }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Daftar nota
    </a>

    <div class="panel">
        <div class="panel-head">
            <div>
                <span class="panel-title mono">{{ $invoice->number }}</span>
                <p class="panel-sub">
                    Card:
                    @foreach($invoice->items as $item)
                        <a href="{{ route('dashboard.cards.show', $item->card_id) }}" class="mono link">{{ $item->card_id }}</a>@if(! $loop->last), @endif
                    @endforeach
                </p>
            </div>
            <div class="head-actions">
                <a href="{{ route('dashboard.invoices.edit', $invoice) }}" class="btn btn-ghost btn-sm">Ubah</a>
                <a href="{{ $invoice->download_url }}" class="btn btn-outline btn-sm">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
                    Unduh PDF
                </a>
                @if($invoice->whatsapp_url)
                <a href="{{ $invoice->whatsapp_url }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">Kirim via WhatsApp</a>
                @endif
            </div>
        </div>
        @unless($invoice->buyer_phone)
        <p class="no-phone">Nomor HP pembeli tidak diisi, jadi nota tidak bisa dikirim lewat WhatsApp. Klik <b>Ubah</b> untuk menambahkannya.</p>
        @endunless
        <div class="panel-body preview">
            @include('dashboard.invoices._sheet')
        </div>
    </div>
</div>

@push('styles')
<style>
.invoice-page { max-width: 680px; }
.back-link { display: inline-flex; align-items: center; gap: 4px; align-self: flex-start; font-size: 13px; font-weight: 500; color: var(--muted); }
.back-link:hover { color: var(--accent); }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.head-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.no-phone { margin: 0; padding: 10px 18px; font-size: 12.5px; background: var(--warn-soft); color: var(--warn); border-bottom: 1px solid var(--border); }
.preview { background: #fff; }
.preview .nota { font-size: 13px; }
</style>
@endpush

@endsection
