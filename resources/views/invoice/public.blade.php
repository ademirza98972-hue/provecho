<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Nota {{ $invoice->number }} — {{ $brand['name'] }}</title>
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
<style>
* { box-sizing: border-box; }
body { margin: 0; background: #F3F4F6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #111827; }
.wrap { max-width: 640px; margin: 0 auto; padding: 16px 16px 40px; }
.paper { background: #fff; border-radius: 12px; padding: 22px 18px; box-shadow: 0 1px 3px rgba(17,24,39,.08); }
.paper .nota { font-size: 13px; }
.actions { margin-top: 16px; display: flex; flex-direction: column; gap: 8px; }
.btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 13px 16px; border-radius: 10px; font-weight: 600; font-size: 15px; text-decoration: none; background: {{ $brand['color'] }}; color: {{ $brand['on'] }}; }
.hint { font-size: 12.5px; color: #6B7280; text-align: center; line-height: 1.5; }
</style>
</head>
<body>
<div class="wrap">
    <div class="paper">
        @include('dashboard.invoices._sheet')
    </div>
    <div class="actions">
        <a href="{{ $invoice->download_url }}" class="btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
            Unduh PDF
        </a>
        <p class="hint">Dibuka dari WhatsApp dan file tidak terunduh? Ketuk menu ⋮ di pojok kanan atas, pilih <b>Buka di Chrome</b> atau <b>Buka di browser</b>, lalu ketuk Unduh PDF lagi.</p>
    </div>
</div>
</body>
</html>
