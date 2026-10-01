<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Provecho</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
    :root {
        --surface: #FFFFFF; --bg: #F9FAFB;
        --border: #E5E7EB; --border-strong: #D4D7DD;
        --text: #111827; --muted: #6B7280; --faint: #9CA3AF;
        --accent: #1B8C3D; --accent-dark: #15702F; --accent-soft: #ECFDF5;
        --bad: #B91C1C; --bad-soft: #FEF2F2;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg); color: var(--text); font-size: 14px; line-height: 1.5;
        min-height: 100vh; display: grid; place-items: center; padding: 24px;
        -webkit-font-smoothing: antialiased;
    }
    .box { width: 100%; max-width: 372px; display: flex; flex-direction: column; gap: 22px; }

    .brand      { display: flex; align-items: center; gap: 10px; justify-content: center; }
    .brand-mark { width: 34px; height: 34px; object-fit: contain; }
    .brand-name { font-size: 19px; font-weight: 600; letter-spacing: -.02em; }

    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 26px; }
    h1     { font-size: 16px; font-weight: 600; letter-spacing: -.01em; }
    .sub   { font-size: 13px; color: var(--muted); margin-top: 3px; margin-bottom: 20px; }

    .form-stack { display: flex; flex-direction: column; gap: 14px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    label  { font-size: 12.5px; font-weight: 600; }
    input  {
        width: 100%; padding: 9px 11px; border: 1px solid var(--border-strong); border-radius: 8px;
        font-size: 13.5px; font-family: inherit; color: var(--text); background: var(--surface);
        transition: border-color .12s, box-shadow .12s;
    }
    input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
    input::placeholder { color: var(--faint); }

    button {
        width: 100%; padding: 10px; margin-top: 2px;
        background: var(--accent); color: #fff; border: none; border-radius: 8px;
        font-size: 13.5px; font-weight: 600; font-family: inherit; cursor: pointer;
        transition: background .12s;
    }
    button:hover { background: var(--accent-dark); }

    .error { display: flex; align-items: flex-start; gap: 8px; background: var(--bad-soft); border: 1px solid #FECACA; color: var(--bad); border-radius: 9px; padding: 10px 13px; font-size: 13px; font-weight: 500; margin-bottom: 16px; }
    .foot  { text-align: center; font-size: 12px; color: var(--faint); }
    </style>
</head>
<body>
<div class="box">
    <div class="brand">
        <img class="brand-mark" src="/img/logo.png" alt="Provecho">
        <div class="brand-name">Provecho</div>
    </div>

    <div class="panel">
        <h1>Masuk ke Dashboard</h1>
        <p class="sub">Kelola card Google Review Anda.</p>

        @if($errors->any())
            <div class="error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:1px"><circle cx="12" cy="12" r="9"/><path d="M12 8v4.5"/><path d="M12 16h.01"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="form-stack">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                       autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="admin">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            <button type="submit">Masuk</button>
        </form>
    </div>

    <p class="foot">Provecho &mdash; Google Review Card</p>
</div>
</body>
</html>
