<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Card — Provecho</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800;900&family=Inter:wght@500;600&display=swap">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', sans-serif;
        background: #e8edf2;
    }

    /* ── toolbar (screen only) ── */
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
    .toolbar button { background: #1A73E8; color: #fff; }
    .toolbar button:hover { background: #1558B0; }
    .toolbar .hint  { color: #6B7280; }

    /* ── A4 sheet ── */
    .page {
        width: 210mm;
        margin: 1.5rem auto;
        background: #fff;
        padding: 10mm;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5mm;
        box-shadow: 0 4px 20px rgba(0,0,0,.12);
    }

    /* ── individual card ── */
    .card-item {
        width: 95mm;
        height: 95mm;
        position: relative;
        border-radius: 5mm;
        overflow: hidden;
    }

    /* 4 colored quadrant corners */
    .c-tl { position:absolute; top:0;    left:0;  width:50%; height:50%; background:#4285F4; }
    .c-tr { position:absolute; top:0;    right:0; width:50%; height:50%; background:#EA4335; }
    .c-bl { position:absolute; bottom:0; left:0;  width:50%; height:50%; background:#34A853; }
    .c-br { position:absolute; bottom:0; right:0; width:50%; height:50%; background:#FBBC04; }

    /* white radial center — corners stay saturated */
    .c-center {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle 56mm at 50% 50%, #fff 0 71%, transparent 100%);
    }

    /* content layer */
    .c-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 7mm 6mm 5mm;
        gap: 0;
    }

    .g-logo    { width: 10mm; height: 10mm; margin-bottom: 2mm; }
    .c-eyebrow { font-family:'Nunito',sans-serif; font-size:6pt; font-weight:700; color:#1a1a2e; opacity:.7; letter-spacing:.02em; margin-bottom:0.5mm; }
    .c-title   { font-family:'Nunito',sans-serif; font-size:13pt; font-weight:900; color:#1a1a2e; line-height:1.1; margin-bottom:1.5mm; }

    .stars     { display:flex; gap:.8mm; margin-bottom:3mm; }
    .stars svg { width:3.5mm; height:3.5mm; }

    .c-divider {
        width: 72%;
        height: 0.2mm;
        background: linear-gradient(90deg, transparent, rgba(0,0,0,.15), transparent);
        margin-bottom: 3mm;
    }

    .c-bottom  { display:flex; align-items:center; justify-content:center; gap:3mm; width:100%; }

    .tap-block, .qr-block { display:flex; flex-direction:column; align-items:center; gap:1mm; }
    .nfc-icon  { width: 9mm; height: 9mm; }

    .qr-svg    { width: 16mm; height: 16mm; }
    .qr-svg svg { width: 100%; height: 100%; }

    .b-label   { font-family:'Inter',sans-serif; font-size:4.5pt; font-weight:600; color:#6B7280; letter-spacing:.08em; text-transform:uppercase; }
    .atau      { font-family:'Nunito',sans-serif; font-size:5pt; font-weight:700; color:#9CA3AF; letter-spacing:.06em; }

    .c-id      { font-family:'Inter',sans-serif; font-size:4pt; font-weight:500; color:#9CA3AF; margin-top:2mm; letter-spacing:.06em; }

    /* print */
    @media print {
        .toolbar { display: none; }
        body     { background: #fff; }
        .page    { margin: 0; box-shadow: none; padding: 5mm; width: 100%; }
        .card-item { page-break-inside: avoid; border-radius: 4mm; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <span class="count">{{ $cards->count() }} card siap cetak</span>
    <div class="group">
        <span class="hint">Ukuran kertas A4, 2 card per baris</span>
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

<div class="page">
    @foreach($cards as $card)
    <div class="card-item">
        {{-- corners --}}
        <div class="c-tl"></div>
        <div class="c-tr"></div>
        <div class="c-bl"></div>
        <div class="c-br"></div>
        <div class="c-center"></div>

        <div class="c-content">
            {{-- Google G logo --}}
            <svg class="g-logo" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>

            <div class="c-eyebrow">Bantu Kami Dengan</div>
            <div class="c-title">Google Review</div>

            <div class="stars">
                @for($s=0;$s<5;$s++)
                <svg viewBox="0 0 20 20" fill="#FBBC04"><path d="M10 1l2.39 4.84 5.35.78-3.87 3.77.91 5.32L10 13.27l-4.78 2.51.91-5.32L2.26 6.62l5.35-.78z"/></svg>
                @endfor
            </div>

            <div class="c-divider"></div>

            <div class="c-bottom">
                {{-- NFC tap icon --}}
                <div class="tap-block">
                    <svg class="nfc-icon" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="12" y="6" width="14" height="24" rx="3" fill="#1a1a2e" opacity=".8"/>
                        <rect x="14" y="9" width="10" height="16" rx="1.5" fill="white" opacity=".9"/>
                        <circle cx="19" cy="28" r="1.2" fill="white" opacity=".6"/>
                        <path d="M29 14 Q34 18 34 22 Q34 26 29 30" stroke="#4285F4" stroke-width="2.2" stroke-linecap="round" fill="none"/>
                        <path d="M31.5 12 Q38.5 17 38.5 22 Q38.5 27 31.5 32" stroke="#4285F4" stroke-width="2.2" stroke-linecap="round" fill="none" opacity=".45"/>
                    </svg>
                    <span class="b-label">Tempelkan HP</span>
                </div>

                <span class="atau">ATAU</span>

                {{-- QR code (SVG inline dari backend) --}}
                <div class="qr-block">
                    <div class="qr-svg">{!! $qrCodes[$card->id] !!}</div>
                    <span class="b-label">Scan QR</span>
                </div>
            </div>

            <div class="c-id">{{ $card->id }}</div>
        </div>
    </div>
    @endforeach

    @if($cards->count() % 2 === 1)
    <div style="width:95mm;height:95mm"></div>
    @endif
</div>

</body>
</html>
