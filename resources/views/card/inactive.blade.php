<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tidak Aktif — Provecho</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #F9FAFB; color: #111827; font-size: 14px; line-height: 1.5;
        min-height: 100vh; display: grid; place-items: center; padding: 24px;
        -webkit-font-smoothing: antialiased;
    }
    .box { width: 100%; max-width: 360px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 18px; }
    .icon { width: 52px; height: 52px; border-radius: 14px; background: #FFFBEB; color: #B45309; display: grid; place-items: center; }
    h1 { font-size: 18px; font-weight: 600; letter-spacing: -.01em; }
    p  { color: #6B7280; font-size: 13.5px; }
    .brand { display: flex; align-items: center; gap: 8px; margin-top: 10px; font-size: 12px; color: #9CA3AF; }
    .brand-mark { width: 20px; height: 20px; object-fit: contain; }
    </style>
</head>
<body>
<div class="box">
    <div class="icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.3 3.9 2.4 17.5A2 2 0 0 0 4.1 20.5h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
            <path d="M12 9v4"/><path d="M12 17h.01"/>
        </svg>
    </div>
    <div>
        <h1>{{ $card ? 'Kartu Tidak Aktif' : 'Kartu Tidak Dikenali' }}</h1>
        <p style="margin-top:6px">
            @if($card)
                Kartu ini sudah dinonaktifkan. Silakan hubungi pihak toko untuk informasi lebih lanjut.
            @else
                Kode kartu ini tidak terdaftar. Periksa kembali kode yang tertera pada kartu.
            @endif
        </p>
    </div>
    <div class="brand">
        <img class="brand-mark" src="/img/logo.png" alt="Provecho">
        Provecho &mdash; Google Review Card
    </div>
</div>
</body>
</html>
