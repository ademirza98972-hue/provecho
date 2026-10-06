@extends('layouts.app')
@section('title', 'Atur Tampilan Reseller')
@section('content')

<div class="stack">

    <a href="{{ route('dashboard.resellers.index') }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Daftar reseller
    </a>

    <div class="page-head">
        <div>
            <h2 class="page-title">Tampilan untuk {{ $reseller->name }}</h2>
            <p class="page-sub">Perubahan di sini sama persis dengan yang bisa diatur {{ $reseller->name }} sendiri dari menu Pengaturan.</p>
        </div>
        @if($sampleCard)
        <a href="{{ route('card.redirect', $sampleCard) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
            Lihat halaman aktivasi asli
        </a>
        @endif
    </div>

    <div class="brand-page">
        @include('dashboard._brand-form', [
            'owner'    => $reseller,
            'action'   => route('dashboard.resellers.brand.update', $reseller),
            'subtitle' => "Dipakai di halaman aktivasi semua card milik {$reseller->name}.",
        ])
    </div>

</div>

@push('styles')
<style>
.back-link { display: inline-flex; align-items: center; gap: 4px; align-self: flex-start; font-size: 13px; font-weight: 500; color: var(--muted); }
.back-link:hover { color: var(--accent); }
.page-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-top: -6px; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
.page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }
.panel-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
.brand-page { max-width: 760px; }
</style>
@endpush

@endsection
