<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Sticker — Provecho</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&display=swap">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', sans-serif;
        background: #e8edf2;
    }

    .toolbar {
        background: #fff; border-bottom: 1px solid #E5E7EB;
        padding: 14px 24px;
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        font-family: 'Inter', sans-serif; font-size: 13px;
    }
    .toolbar .count { font-weight: 600; color: #111827; }
    .toolbar .group { display: flex; align-items: center; gap: 10px; }
    .toolbar a, .toolbar button {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 600; font-family: inherit;
        cursor: pointer; text-decoration: none; border: 1px solid transparent;
    }
    .toolbar a      { background: #fff; border-color: #D4D7DD; color: #111827; }
    .toolbar a:hover{ background: #F9FAFB; }
    .toolbar button { background: #0284C7; color: #fff; }
    .toolbar button:hover { background: #0369A1; }
    .toolbar .hint  { color: #6B7280; }

    /* ── sticker sheet: 100cm × 50cm ── */
    .sheet {
        width: 1000mm;
        height: 500mm;
        margin: 1.5rem auto;
        background: #fff;
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        grid-template-rows: repeat(5, 1fr);
        box-shadow: 0 4px 20px rgba(0,0,0,.12);
    }

@include('dashboard.cards._card-styles')
    .card-item { border-radius: 0; border: 0.2mm dashed #D4D7DD; }

    .empty-cell {
        width: 100mm;
        height: 100mm;
        border: 0.2mm dashed #D4D7DD;
    }

    /* screen: scale down to fit viewport */
    @media screen {
        .sheet {
            transform-origin: top left;
            transform: scale(0.28);
            margin-bottom: calc(-500mm * 0.72 + 1.5rem);
        }
    }

    @media print {
        .toolbar { display: none; }
        body     { background: #fff; margin: 0; padding: 0; }

        @page {
            size: 1000mm 500mm;
            margin: 0;
        }

        .sheet {
            margin: 0;
            box-shadow: none;
            page-break-after: always;
        }

        .card-item {
            border-color: #E5E7EB;
        }
    }
</style>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
</head>
<body>

<div class="toolbar">
    <span class="count">{{ $cards->count() }} sticker — {{ ceil($cards->count() / 50) }} lembar (100×50 cm)</span>
    <div class="group">
        <span class="hint">Media sticker Ritrama 100 × 50 cm, 10×5 grid</span>
        <a href="{{ route('dashboard.cards.index') }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali
        </a>
        <button onclick="window.print()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1"/><rect x="6" y="14" width="12" height="7" rx="1.5"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>
</div>

@foreach($cards->chunk(50) as $sheetCards)
<div class="sheet">
    @foreach($sheetCards as $card)
    <div class="card-item">
        <div class="qr-overlay">{!! $qrCodes[$card->id] !!}</div>
        <div class="id-overlay">{{ $card->id }}</div>
    </div>
    @endforeach

    @for($empty = $sheetCards->count(); $empty < 50; $empty++)
    <div class="empty-cell"></div>
    @endfor
</div>
@endforeach

</body>
</html>
