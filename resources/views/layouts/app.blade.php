<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Provecho</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
    :root {
        --bg: #F9FAFB;
        --surface: #FFFFFF;
        --subtle: #FCFCFD;
        --border: #E5E7EB;
        --border-strong: #D4D7DD;
        --text: #111827;
        --muted: #6B7280;
        --faint: #9CA3AF;
        --accent: #0EA5E9;
        --accent-dark: #0284C7;
        --accent-soft: #F0F9FF;
        --ok: #15803D;      --ok-soft: #F0FDF4;
        --warn: #B45309;    --warn-soft: #FFFBEB;
        --bad: #B91C1C;     --bad-soft: #FEF2F2;
        --radius: 10px;
        --sidebar-w: 240px;
        --sidebar-collapsed-w: 64px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg);
        color: var(--text);
        font-size: 14px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }
    a { color: inherit; text-decoration: none; }
    svg { flex-shrink: 0; }

    /* ── shell ── */
    .layout  { display: flex; min-height: 100vh; }
    .main    { flex: 1; display: flex; flex-direction: column; min-width: 0; margin-left: var(--sidebar-w); transition: margin-left .2s ease; }

    /* ── sidebar: fixed ── */
    .sidebar {
        width: var(--sidebar-w); flex-shrink: 0;
        background: var(--surface);
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column;
        position: fixed; top: 0; left: 0; bottom: 0;
        z-index: 40;
        transition: width .2s ease, transform .25s ease;
    }
    .sidebar-inner {
        display: flex; flex-direction: column;
        height: 100%;
        overflow-y: auto;
        padding: 0 0 14px;
    }

    /* brand */
    .brand {
        display: flex; align-items: center; gap: 11px;
        padding: 18px 20px 16px;
        border-bottom: 1px solid var(--border);
    }
    .brand-mark { width: 36px; height: 36px; object-fit: contain; }
    .brand-name { font-size: 17px; font-weight: 700; letter-spacing: -.02em; color: var(--text); }

    /* nav */
    .nav       { display: flex; flex-direction: column; gap: 2px; padding: 14px 10px 0; flex: 1; }
    .nav-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--faint); padding: 8px 10px 4px; }
    .nav a {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 12px; border-radius: 8px;
        color: var(--muted); font-weight: 500; font-size: 13.5px;
        transition: background .12s, color .12s;
    }
    .nav a:hover  { background: #F3F4F6; color: var(--text); }
    .nav a.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }

    .sidebar-foot { padding: 10px 10px 0; border-top: 1px solid var(--border); margin-top: auto; }
    .sidebar-foot button { width: 100%; }

    /* ── hamburger ── */
    .hamburger {
        display: flex;
        background: none; border: none; cursor: pointer;
        padding: 6px; border-radius: 6px; color: var(--muted);
        transition: background .12s;
    }
    .hamburger:hover { background: #F3F4F6; color: var(--text); }

    /* overlay */
    .sidebar-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 35;
        background: rgba(0,0,0,.3);
    }

    /* ── collapsed sidebar (desktop) ── */
    .layout.collapsed .sidebar {
        width: var(--sidebar-collapsed-w);
    }
    .layout.collapsed .main {
        margin-left: var(--sidebar-collapsed-w);
    }
    .layout.collapsed .brand-name,
    .layout.collapsed .nav-label,
    .layout.collapsed .nav a span,
    .layout.collapsed .sidebar-foot span,
    .layout.collapsed .sidebar-foot .btn svg ~ * {
        display: none;
    }
    .layout.collapsed .brand {
        justify-content: center;
        padding: 18px 10px 16px;
    }
    .layout.collapsed .nav {
        padding: 14px 6px 0;
    }
    .layout.collapsed .nav a {
        justify-content: center;
        padding: 10px;
        border-radius: 10px;
    }
    .layout.collapsed .nav a svg {
        width: 20px; height: 20px;
    }
    .layout.collapsed .sidebar-foot {
        padding: 10px 6px 0;
    }
    .layout.collapsed .sidebar-foot button {
        justify-content: center;
        padding: 8px;
    }

    /* topbar */
    .topbar {
        background: var(--surface); border-bottom: 1px solid var(--border);
        padding: 0 24px; min-height: 56px;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        position: sticky; top: 0; z-index: 20;
    }
    .topbar-left { display: flex; align-items: center; gap: 12px; }
    .topbar h1 { font-size: 16px; font-weight: 600; letter-spacing: -.01em; }
    .user   { display: flex; align-items: center; gap: 9px; font-size: 13px; color: var(--muted); }
    .avatar { width: 28px; height: 28px; border-radius: 50%; background: #F3F4F6; border: 1px solid var(--border); display: grid; place-items: center; font-size: 11px; font-weight: 600; color: var(--muted); }

    .content { flex: 1; padding: 24px; overflow-y: auto; }

    /* ── panels ── */
    .panel      { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .panel-head { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .panel-title{ font-size: 14px; font-weight: 600; }
    .panel-body { padding: 18px; }
    .stack      { display: flex; flex-direction: column; gap: 16px; }
    .grid-2     { display: grid; grid-template-columns: 1.05fr .95fr; gap: 16px; align-items: start; }

    /* ── stats ── */
    .stat-grid  { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; }
    .stat       { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; }
    .stat-label { font-size: 12px; font-weight: 500; color: var(--muted); display: flex; align-items: center; gap: 7px; }
    .stat-num   { font-size: 28px; font-weight: 700; letter-spacing: -.03em; margin-top: 6px; font-variant-numeric: tabular-nums; }
    .dot        { width: 7px; height: 7px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* ── table ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 10px 18px; text-align: left; font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--faint); background: var(--subtle); border-bottom: 1px solid var(--border); white-space: nowrap; }
    td { padding: 12px 18px; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: var(--subtle); }
    th.check, td.check { width: 42px; padding-right: 0; }
    .mono  { font-family: ui-monospace, 'SF Mono', Menlo, Consolas, monospace; font-size: 13px; font-weight: 600; letter-spacing: -.01em; }
    .empty { text-align: center; color: var(--faint); padding: 40px 18px; }

    /* ── badges ── */
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 99px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .badge .dot { width: 6px; height: 6px; }
    .badge-active   { background: var(--ok-soft);   color: var(--ok); }
    .badge-inactive { background: var(--warn-soft); color: var(--warn); }
    .badge-printed  { background: #EFF6FF; color: #2563EB; }
    .badge-disabled { background: var(--bad-soft);  color: var(--bad); }

    /* ── buttons ── */
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; border: 1px solid transparent; transition: background .12s, border-color .12s, color .12s; white-space: nowrap; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { background: var(--accent-dark); }
    .btn-outline { background: var(--surface); border-color: var(--border-strong); color: var(--text); }
    .btn-outline:hover { background: var(--bg); border-color: var(--faint); }
    .btn-ghost   { background: transparent; color: var(--muted); }
    .btn-ghost:hover { background: #F3F4F6; color: var(--text); }
    .btn-danger  { background: var(--surface); border-color: #FCA5A5; color: var(--bad); }
    .btn-danger:hover { background: var(--bad-soft); }
    .btn-sm { padding: 6px 11px; font-size: 12.5px; border-radius: 7px; }
    .btn:disabled, .btn:disabled:hover { opacity: .4; cursor: not-allowed; background: var(--accent); }

    /* ── forms ── */
    .field  { display: flex; flex-direction: column; gap: 6px; }
    .form-stack { display: flex; flex-direction: column; gap: 14px; }
    label   { font-size: 12.5px; font-weight: 600; color: var(--text); }
    input[type=text], input[type=url], input[type=number], input[type=password], input[type=email], select, textarea {
        width: 100%; padding: 8px 11px;
        border: 1px solid var(--border-strong); border-radius: 8px;
        font-size: 13.5px; font-family: inherit; color: var(--text); background: var(--surface);
        transition: border-color .12s, box-shadow .12s;
    }
    input:focus, select:focus, textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft); }
    input::placeholder { color: var(--faint); }
    input[type=checkbox] { width: 15px; height: 15px; accent-color: var(--accent); cursor: pointer; margin: 0; }
    .hint { font-size: 12px; color: var(--muted); }

    /* ── combobox (Places search) ── */
    .combo      { position: relative; }
    .combo-list { position: absolute; z-index: 20; top: calc(100% + 4px); left: 0; right: 0; background: var(--surface); border: 1px solid var(--border); border-radius: 9px; box-shadow: 0 8px 24px rgba(17,24,39,.09); max-height: 260px; overflow-y: auto; padding: 4px; }
    .combo-item { padding: 8px 10px; border-radius: 6px; cursor: pointer; }
    .combo-item:hover { background: #F3F4F6; }
    .combo-name { font-size: 13px; font-weight: 600; }
    .combo-addr { font-size: 12px; color: var(--muted); margin-top: 1px; }

    /* ── alerts ── */
    .alert { display: flex; align-items: flex-start; gap: 9px; padding: 11px 14px; border-radius: 9px; font-size: 13px; font-weight: 500; margin-bottom: 16px; border: 1px solid; }
    .alert-success { background: var(--ok-soft);  border-color: #BBF7D0; color: var(--ok); }
    .alert-error   { background: var(--bad-soft); border-color: #FECACA; color: var(--bad); }

    /* ── meta list ── */
    .meta     { display: flex; flex-direction: column; gap: 14px; }
    .meta-row { display: flex; flex-direction: column; gap: 3px; }
    .meta-key { font-size: 11px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--faint); }
    .meta-val { font-size: 13.5px; word-break: break-word; }
    .link     { color: var(--accent); font-weight: 500; }
    .link:hover { text-decoration: underline; }

    /* ── QR block ── */
    .qr-box   { display: flex; flex-direction: column; align-items: center; gap: 12px; }
    .qr-frame { background: #fff; border: 1px solid var(--border); border-radius: 9px; padding: 10px; line-height: 0; }
    .qr-frame svg { display: block; width: 160px; height: 160px; }
    .url-chip { font-family: ui-monospace, Menlo, Consolas, monospace; font-size: 12px; background: var(--subtle); border: 1px solid var(--border); border-radius: 7px; padding: 7px 10px; color: var(--muted); word-break: break-all; text-align: center; }

    /* ── activity log ── */
    .log-row  { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid #F3F4F6; font-size: 13px; }
    .log-row:last-child { border-bottom: none; }
    .log-time { color: var(--faint); font-size: 12px; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .log-act  { font-weight: 600; font-size: 11.5px; padding: 2px 8px; border-radius: 99px; background: #F3F4F6; color: var(--muted); }
    .log-ip   { color: var(--muted); font-size: 12px; margin-left: auto; font-variant-numeric: tabular-nums; }

    /* ── toolbar ── */
    .toolbar  { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; justify-content: space-between; }
    .toolbar-group { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

    /* ── pagination ── */
    .pagination { display: flex; gap: 4px; align-items: center; padding: 0; }
    .pagination a, .pagination span { padding: 5px 10px; border-radius: 7px; font-size: 12px; font-weight: 500; border: 1px solid var(--border); color: var(--muted); white-space: nowrap; }
    .pagination a:hover { background: var(--bg); color: var(--text); }
    .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* ── mobile ── */
    @media (max-width: 860px) {
        .main { margin-left: 0 !important; }
        .sidebar { transform: translateX(-100%); width: var(--sidebar-w) !important; }
        .sidebar.open { transform: translateX(0); }
        .sidebar-overlay.open { display: block; }
        .layout.collapsed .sidebar { width: var(--sidebar-w) !important; }
        .layout.collapsed .brand-name,
        .layout.collapsed .nav-label,
        .layout.collapsed .nav a span,
        .layout.collapsed .sidebar-foot span { display: inline; }
        .layout.collapsed .brand { justify-content: flex-start; padding: 18px 20px 16px; }
        .layout.collapsed .nav { padding: 14px 10px 0; }
        .layout.collapsed .nav a { justify-content: flex-start; padding: 9px 12px; border-radius: 8px; }
        .layout.collapsed .nav a svg { width: 17px; height: 17px; }
        .layout.collapsed .sidebar-foot { padding: 10px 10px 0; }
        .layout.collapsed .sidebar-foot button { justify-content: center; padding: 6px 11px; }
        .grid-2  { grid-template-columns: 1fr; }
        .content { padding: 16px; }
    }
    </style>
    @stack('styles')
</head>
<body>
<div class="layout">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">
            <div class="brand">
                <img class="brand-mark" src="/img/logo.png" alt="Provecho">
                <span class="brand-name">PROVECHO</span>
            </div>

            <nav class="nav">
                <span class="nav-label">Menu</span>
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('dashboard.cards.index') }}" class="{{ request()->routeIs('dashboard.cards.*') ? 'active' : '' }}">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/><path d="M6 14.5h4"/></svg>
                    <span>Kelola Card</span>
                </a>
                <a href="{{ route('dashboard.activity') }}" class="{{ request()->routeIs('dashboard.activity') ? 'active' : '' }}">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                    <span>Aktivitas</span>
                </a>
            </nav>

            <div class="sidebar-foot">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
                </button>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <div class="user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->username, 0, 1)) }}</div>
                <span>{{ auth()->user()->name ?? auth()->user()->username }}</span>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:1px"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.5 2.5 4.5-5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:1px"><circle cx="12" cy="12" r="9"/><path d="M12 8v4.5"/><path d="M12 16h.01"/></svg>
                    <div>
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    var isMobile = window.innerWidth <= 860;
    if (isMobile) {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    } else {
        document.querySelector('.layout').classList.toggle('collapsed');
        try { localStorage.setItem('sidebar', document.querySelector('.layout').classList.contains('collapsed') ? 'c' : 'e'); } catch(e) {}
    }
}
try { if (localStorage.getItem('sidebar') === 'c') document.querySelector('.layout').classList.add('collapsed'); } catch(e) {}
</script>
@stack('scripts')
</body>
</html>
