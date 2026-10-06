<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Kartu Aktif — {{ $brand['name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
    :root {
        --surface: #FFFFFF; --bg: #F9FAFB;
        --border: #E5E7EB;
        --text: #111827; --muted: #6B7280; --faint: #9CA3AF;
        --accent: {{ $brand['color'] }}; --accent-dark: {{ $brand['dark'] }}; --accent-soft: {{ $brand['soft'] }}; --on: {{ $brand['on'] }};
        --ok: #15803D; --ok-soft: #F0FDF4;
        color-scheme: light;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg); color: var(--text);
        font-size: 15px; line-height: 1.55;
        -webkit-font-smoothing: antialiased;
        padding: 28px 16px 40px;
        display: flex; justify-content: center;
    }
    .wrap { width: 100%; max-width: 440px; display: flex; flex-direction: column; gap: 20px; align-items: center; }

    .brand-mark { width: 72px; height: 72px; object-fit: contain; }

    .panel {
        background: var(--surface); border: 1px solid var(--border); border-radius: 14px;
        padding: 28px 22px; width: 100%; text-align: center;
        display: flex; flex-direction: column; align-items: center; gap: 18px;
    }

    .check-circle {
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--ok-soft); border: 2px solid #BBF7D0;
        display: grid; place-items: center;
    }
    .check-circle svg { color: var(--ok); }

    h1 { font-size: 20px; font-weight: 700; letter-spacing: -.02em; }
    .lead { color: var(--muted); font-size: 14px; max-width: 340px; }

    .shop-name {
        font-size: 16px; font-weight: 600;
        background: var(--ok-soft); border: 1px solid #BBF7D0; border-radius: 10px;
        padding: 12px 16px; width: 100%;
    }

    .btn {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 14px; border-radius: 11px;
        font-size: 15px; font-weight: 600; font-family: inherit;
        cursor: pointer; border: none; background: var(--accent); color: var(--on);
        text-decoration: none; transition: background .12s;
    }
    .btn:hover { background: var(--accent-dark); }

    .foot { text-align: center; font-size: 12.5px; color: var(--faint); }
    </style>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
</head>
<body>
<div class="wrap">

    <img class="brand-mark" src="{{ $brand['logo'] }}" alt="{{ $brand['name'] }}">

    <div class="panel">
        <div class="check-circle">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m8 12.5 3 3 5-5.5"/></svg>
        </div>

        <h1>Kartu Berhasil Diaktifkan!</h1>
        <p class="lead">Kartu Anda sekarang aktif. Setiap kali seseorang tap atau scan kartu ini, mereka akan langsung diarahkan ke halaman ulasan toko Anda.</p>

        <div class="shop-name">{{ $card->owner_name }}</div>

        <a href="{{ $card->google_url }}" class="btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
            Buka Halaman Review
        </a>
    </div>

    <p class="foot">{{ $brand['name'] }}</p>
</div>
</body>
</html>
