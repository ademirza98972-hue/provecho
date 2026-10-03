<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provecho — Google Review Card</title>
    <meta name="description" content="Card NFC + QR Code yang langsung membuka Google Review toko kamu. Tap sekali, review masuk. Rp 50.000, sekali bayar.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
    :root {
        --white: #FFFFFF;
        --bg:   #F8FAFD;
        --fg:   #0B1526;
        --fg2:  #4B5971;
        --fg3:  #8A96A8;
        --blue: #0EA5E9;
        --blue2:#0284C7;
        --blue-bg: #EFF8FF;
        --blue-border: #BAE6FD;
        --green:#10B981;
        --green-bg:#ECFDF5;
        --border: #E4EAF2;
        --shadow-sm: 0 1px 3px rgba(11,21,38,.06), 0 1px 2px rgba(11,21,38,.04);
        --shadow: 0 4px 16px rgba(11,21,38,.07);
        --shadow-lg: 0 16px 48px rgba(11,21,38,.10);
        --r: 10px;
        --r-lg: 16px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }
    [x-cloak] { display: none !important }
    html { font-family: 'Inter', system-ui, sans-serif; color: var(--fg); -webkit-font-smoothing: antialiased; line-height: 1.6 }
    body { background: var(--white); overflow-x: hidden }
    img { max-width: 100%; display: block }
    a { text-decoration: none; color: inherit }
    button { font: inherit; border: none; background: none; cursor: pointer }
    h1,h2,h3,h4,h5 { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -.025em; line-height: 1.15; text-wrap: balance }

    .w  { width: 100%; max-width: 1060px; margin: 0 auto; padding: 0 24px }
    .wn { width: 100%; max-width:  680px; margin: 0 auto; padding: 0 24px }

    /* ─── NAV ─── */
    .nav {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        background: rgba(255,255,255,.88); backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
        border-bottom: 1px solid var(--border);
    }
    .nav-inner {
        max-width: 1060px; margin: 0 auto; padding: 0 24px;
        height: 58px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .nav-logo {
        display: flex; align-items: center; gap: 8px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 16px; color: var(--fg); letter-spacing: -.02em;
    }
    .nav-logo img { width: 28px; height: 28px }
    .nav-links { display: flex; align-items: center; gap: 2px }
    .nav-links a { font-size: 14px; font-weight: 500; color: var(--fg2); padding: 7px 13px; border-radius: 7px; transition: color .15s, background .15s }
    .nav-links a:hover { color: var(--fg); background: var(--bg) }
    .nav-cta {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--fg); color: #fff; font-size: 13.5px; font-weight: 700;
        padding: 8px 20px; border-radius: 999px; transition: opacity .15s;
        font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -.01em;
    }
    .nav-cta:hover { opacity: .85 }
    .nav-toggle { display: none; color: var(--fg); padding: 4px }

    /* ─── HERO ─── */
    .hero {
        padding: 96px 0 0;
        background: linear-gradient(180deg, #EFF8FF 0%, #F8FAFD 38%, #FFFFFF 65%);
        text-align: center;
    }
    .pill {
        display: inline-flex; align-items: center; gap: 8px;
        border: 1px solid var(--blue-border); background: var(--blue-bg);
        color: var(--blue2); font-size: 12.5px; font-weight: 600;
        padding: 5px 14px; border-radius: 999px; margin-bottom: 28px;
    }
    .pill svg { width: 12px; height: 12px; flex-shrink: 0 }
    .hero h1 {
        font-size: clamp(38px, 5.5vw, 66px); font-weight: 900; color: var(--fg);
        line-height: 1.04; letter-spacing: -.035em; max-width: 760px; margin: 0 auto 18px;
    }
    .hero h1 .accent { color: var(--blue) }
    .hero-sub {
        font-size: 17px; color: var(--fg2); line-height: 1.7;
        max-width: 440px; margin: 0 auto 32px;
    }
    .hero-btns { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px }
    .btn-primary {
        display: inline-flex; align-items: center; gap: 8px;
        background: var(--blue); color: #fff; font-size: 15px; font-weight: 700;
        padding: 13px 28px; border-radius: var(--r); transition: background .15s, transform .15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-primary:hover { background: var(--blue2); transform: translateY(-1px) }
    .btn-secondary {
        display: inline-flex; align-items: center; gap: 8px;
        border: 1.5px solid var(--border); color: var(--fg2); font-size: 15px; font-weight: 600;
        padding: 12px 24px; border-radius: var(--r); transition: border-color .15s, color .15s;
    }
    .btn-secondary:hover { border-color: var(--fg3); color: var(--fg) }
    .hero-note { font-size: 12.5px; color: var(--fg3); display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 56px }
    .hero-note span { display: flex; align-items: center; gap: 5px }
    .hero-note svg { width: 13px; height: 13px; color: var(--green) }

    .hero-img-wrap {
        max-width: 740px; margin: 0 auto;
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--r-lg) var(--r-lg) 0 0;
        box-shadow: 0 -2px 0 0 var(--border), var(--shadow-lg);
        overflow: hidden;
    }
    .hero-img-bar {
        padding: 12px 16px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 6px;
    }
    .hero-img-bar span { width: 10px; height: 10px; border-radius: 50% }
    .dot-r { background: #FF5F56 }
    .dot-y { background: #FFBD2E }
    .dot-g { background: #27C93F }
    .hero-img-wrap img { width: 100%; display: block }

    /* ─── DIVIDER ─── */
    .divider { height: 1px; background: var(--border); margin: 0 }

    /* ─── STATS ─── */
    .stats { background: var(--white); padding: 40px 0 }
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); text-align: center }
    .stat { padding: 4px 0; position: relative }
    .stat:not(:last-child)::after { content:''; position: absolute; right: 0; top: 10%; height: 80%; width: 1px; background: var(--border) }
    .stat-n { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 800; color: var(--fg); letter-spacing: -.03em; line-height: 1.1; margin-bottom: 4px }
    .stat-n .blue { color: var(--blue) }
    .stat-n .green { color: var(--green) }
    .stat-l { font-size: 12.5px; color: var(--fg3); font-weight: 500 }

    /* ─── SECTION BASE ─── */
    .sec { padding: 88px 0 }
    .sec-label {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 700; color: var(--blue); text-transform: uppercase; letter-spacing: .1em;
        margin-bottom: 12px;
    }
    .sec-label::before { content:''; width: 20px; height: 2px; background: var(--blue); border-radius: 2px }
    .sec-title { font-size: clamp(24px,3.2vw,38px); font-weight: 800; color: var(--fg); margin-bottom: 12px }
    .sec-sub { font-size: 15.5px; color: var(--fg2); line-height: 1.7 }
    .sec-head { margin-bottom: 52px }
    .sec-head.ctr { text-align: center }
    .sec-head.ctr .sec-sub { max-width: 480px; margin: 0 auto }

    /* ─── FEATURES ─── */
    .feat-bg { background: var(--bg) }
    .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden }
    .feat { background: var(--white); padding: 30px 28px; transition: background .2s }
    .feat:hover { background: var(--bg) }
    .feat-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--blue-bg); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: var(--blue) }
    .feat-icon svg { width: 20px; height: 20px }
    .feat-icon.g { background: var(--green-bg); color: var(--green) }
    .feat h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; color: var(--fg) }
    .feat p { font-size: 13.5px; color: var(--fg2); line-height: 1.65 }

    /* ─── HOW ─── */
    .how-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 40px; position: relative }
    .how-grid::before { content:''; position: absolute; top: 24px; left: calc(16.7% + 20px); right: calc(16.7% + 20px); height: 1px; background: var(--border) }
    .how-step { text-align: center }
    .how-num {
        width: 48px; height: 48px; border-radius: 50%;
        border: 1.5px solid var(--border); background: var(--white);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 800; color: var(--fg3);
        margin: 0 auto 20px; position: relative; z-index: 1;
    }
    .how-step.active .how-num { border-color: var(--blue); color: var(--blue); box-shadow: 0 0 0 6px var(--blue-bg) }
    .how-step h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px }
    .how-step p { font-size: 14px; color: var(--fg2); line-height: 1.65 }

    /* ─── PRODUCT SPLIT ─── */
    .split { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center }
    .split.rev .split-img { order: -1 }
    .split-text .tag { display: inline-flex; align-items: center; gap: 6px; background: var(--green-bg); color: var(--green); font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: .07em }
    .split-text h2 { font-size: clamp(22px,2.8vw,34px); font-weight: 800; margin-bottom: 14px }
    .split-text p { font-size: 15px; color: var(--fg2); line-height: 1.75; margin-bottom: 24px }
    .check-list { list-style: none; display: flex; flex-direction: column; gap: 10px }
    .check-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--fg2) }
    .check-list li svg { width: 16px; height: 16px; color: var(--green); flex-shrink: 0; margin-top: 3px }
    .split-img { border-radius: var(--r-lg); overflow: hidden; box-shadow: var(--shadow-lg) }
    .split-img img { width: 100%; display: block }

    /* ─── COMPARISON ─── */
    .comp-bg { background: var(--fg) }
    .comp-bg .sec-label { color: var(--blue) }
    .comp-bg .sec-label::before { background: var(--blue) }
    .comp-bg .sec-title { color: #fff }
    .comp-bg .sec-sub { color: rgba(255,255,255,.45) }
    .comp-table { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 780px; margin: 0 auto }
    .comp-col { border-radius: var(--r-lg); padding: 32px 28px }
    .comp-col.plain { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08) }
    .comp-col.prov { background: rgba(14,165,233,.1); border: 1px solid rgba(14,165,233,.25); position: relative; overflow: hidden }
    .comp-col.prov::before { content:''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(14,165,233,.05), transparent) }
    .comp-col h3 { font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 4px }
    .comp-sub { font-size: 12px; color: rgba(255,255,255,.3); margin-bottom: 22px }
    .comp-rows { display: flex; flex-direction: column; gap: 11px }
    .comp-row { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: rgba(255,255,255,.6); line-height: 1.5 }
    .comp-row svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 2px }
    .comp-col.plain .comp-row svg { color: #EF4444; opacity: .7 }
    .comp-col.prov .comp-row svg { color: var(--green) }
    .comp-price { margin-top: 22px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,.08); font-size: 12.5px; color: rgba(255,255,255,.3) }
    .comp-price strong { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 800; color: #fff; letter-spacing: -.03em; display: block; margin-bottom: 2px }

    /* ─── PRICING ─── */
    .price-wrap { max-width: 440px; margin: 0 auto }
    .price-card {
        border-radius: var(--r-lg); padding: 44px 36px;
        border: 1.5px solid var(--blue-border);
        background: linear-gradient(135deg, var(--blue-bg) 0%, var(--white) 60%);
        box-shadow: var(--shadow-lg);
    }
    .price-badge { font-size: 11px; font-weight: 700; color: var(--blue); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 20px }
    .price-amount { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 52px; font-weight: 900; letter-spacing: -.04em; line-height: 1; color: var(--fg) }
    .price-amount small { font-size: 18px; font-weight: 500; color: var(--fg3); vertical-align: super; margin-top: 8px; display: inline-block }
    .price-note { font-size: 13px; color: var(--fg3); margin: 8px 0 28px }
    .price-list { list-style: none; display: flex; flex-direction: column; gap: 11px; margin-bottom: 28px }
    .price-list li { display: flex; align-items: center; gap: 10px; font-size: 14.5px; color: var(--fg2) }
    .price-list li svg { width: 17px; height: 17px; color: var(--green); flex-shrink: 0 }
    .btn-full {
        display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
        background: var(--fg); color: #fff; font-size: 15px; font-weight: 700; padding: 15px;
        border-radius: var(--r); transition: opacity .15s; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .btn-full:hover { opacity: .85 }
    .price-hint { margin-top: 14px; font-size: 12px; color: var(--fg3); text-align: center }

    /* ─── TESTIMONIALS ─── */
    .testi-bg { background: var(--bg) }
    .testi-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px }
    .testi-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 24px }
    .testi-stars { display: flex; gap: 2px; margin-bottom: 12px }
    .testi-stars svg { width: 13px; height: 13px; fill: #FBBF24; color: #FBBF24 }
    .testi-text { font-size: 14px; color: var(--fg2); line-height: 1.7; margin-bottom: 18px }
    .testi-author { display: flex; align-items: center; gap: 10px }
    .testi-av { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg,var(--blue),var(--green)); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0 }
    .testi-name { font-size: 13px; font-weight: 600; color: var(--fg) }
    .testi-role { font-size: 11.5px; color: var(--fg3) }

    /* ─── FAQ ─── */
    .faq-list { max-width: 620px; margin: 0 auto; display: flex; flex-direction: column; gap: 6px }
    .faq-item { border: 1px solid var(--border); border-radius: var(--r); overflow: hidden; transition: border-color .2s }
    .faq-item.open { border-color: var(--blue) }
    .faq-q { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 17px 20px; font-size: 14.5px; font-weight: 600; text-align: left; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: color .15s; color: var(--fg) }
    .faq-item.open .faq-q { color: var(--blue) }
    .faq-ic { width: 26px; height: 26px; border-radius: 50%; background: var(--bg); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .25s; color: var(--fg3) }
    .faq-ic svg { width: 13px; height: 13px }
    .faq-item.open .faq-ic { background: var(--blue-bg); color: var(--blue); transform: rotate(45deg) }
    .faq-a { overflow: hidden; max-height: 0; opacity: 0; transition: max-height .4s cubic-bezier(.16,1,.3,1), opacity .3s, padding .3s }
    .faq-item.open .faq-a { max-height: 300px; opacity: 1; padding: 0 20px 18px }
    .faq-a p { font-size: 14px; color: var(--fg2); line-height: 1.75 }

    /* ─── CTA ─── */
    .cta-sec { padding: 88px 0 }
    .cta-box { background: var(--fg); border-radius: 20px; padding: 72px 40px; text-align: center; position: relative; overflow: hidden }
    .cta-box::before { content:''; position: absolute; top: -120px; right: -120px; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(14,165,233,.12), transparent 65%); pointer-events: none }
    .cta-box::after  { content:''; position: absolute; bottom: -100px; left: -80px; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(16,185,129,.08), transparent 65%); pointer-events: none }
    .cta-box h2 { font-size: clamp(24px,3.6vw,38px); font-weight: 800; color: #fff; margin-bottom: 12px; position: relative }
    .cta-box p { font-size: 16px; color: rgba(255,255,255,.45); max-width: 380px; margin: 0 auto 32px; position: relative }
    .btn-white { display: inline-flex; align-items: center; gap: 9px; background: #fff; color: var(--fg); font-size: 15px; font-weight: 800; padding: 15px 36px; border-radius: var(--r); transition: transform .15s, box-shadow .15s; font-family: 'Plus Jakarta Sans', sans-serif; position: relative }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.18) }
    .cta-note { margin-top: 16px; font-size: 12px; color: rgba(255,255,255,.2); position: relative }

    /* ─── FOOTER ─── */
    .foot { background: var(--fg); border-top: 1px solid rgba(255,255,255,.06); padding: 36px 0 }
    .foot-inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap }
    .foot-brand { display: flex; align-items: center; gap: 8px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 15px; color: #fff }
    .foot-brand img { width: 22px; height: 22px }
    .foot-links { display: flex; gap: 20px; flex-wrap: wrap }
    .foot-links a { font-size: 13px; color: rgba(255,255,255,.3); transition: color .15s }
    .foot-links a:hover { color: rgba(255,255,255,.7) }
    .foot-copy { margin-top: 24px; font-size: 11.5px; color: rgba(255,255,255,.15); text-align: center }

    /* ─── WA FLOAT ─── */
    .wa { position: fixed; bottom: 24px; right: 24px; z-index: 99 }
    .wa-btn { width: 52px; height: 52px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 18px rgba(37,211,102,.35); transition: transform .2s }
    .wa-btn:hover { transform: scale(1.1) }
    .wa-btn svg { width: 26px; height: 26px; color: #fff }

    /* ─── REVEAL ─── */
    @keyframes up { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:none } }
    .hi   { animation: up .65s ease both }
    .hi-1 { animation-delay: .05s } .hi-2 { animation-delay: .13s } .hi-3 { animation-delay: .21s } .hi-4 { animation-delay: .29s }
    .rv { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease }
    .rv.vis { opacity: 1; transform: none }
    .rv-1 { transition-delay: .08s } .rv-2 { transition-delay: .16s } .rv-3 { transition-delay: .24s }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 900px) {
        .feat-grid { grid-template-columns: repeat(2,1fr) }
        .testi-grid { grid-template-columns: repeat(2,1fr) }
        .split { grid-template-columns: 1fr; gap: 36px }
        .split.rev .split-img { order: unset }
        .how-grid { grid-template-columns: 1fr; gap: 28px }
        .how-grid::before { display: none }
        .comp-table { grid-template-columns: 1fr }
        .stats-grid { grid-template-columns: repeat(2,1fr) }
        .stat:nth-child(2)::after { display: none }
    }
    @media (max-width: 640px) {
        .nav-links { display: none }
        .nav.open .nav-links {
            display: flex; flex-direction: column;
            position: absolute; top: 58px; left: 12px; right: 12px;
            background: rgba(255,255,255,.97); backdrop-filter: blur(20px);
            border: 1px solid var(--border); border-radius: 12px; padding: 8px;
            box-shadow: var(--shadow-lg);
        }
        .nav.open .nav-links a { padding: 12px 14px; border-radius: 8px }
        .nav-toggle { display: block }
        .hero { padding: 80px 0 0 }
        .sec { padding: 60px 0 }
        .cta-box { padding: 48px 20px }
        .price-card { padding: 32px 22px }
        .feat-grid { grid-template-columns: 1fr }
        .testi-grid { grid-template-columns: 1fr }
        .foot-inner { flex-direction: column; text-align: center }
        .foot-links { justify-content: center }
    }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav" x-data="{open:false}" :class="{open:open}">
    <div class="nav-inner">
        <a href="#" class="nav-logo">
            <img src="/img/logo.png" alt="Provecho">
            Provecho
        </a>
        <div class="nav-links">
            <a href="#cara-kerja" @click="open=false">Cara Kerja</a>
            <a href="#fitur" @click="open=false">Fitur</a>
            <a href="#harga" @click="open=false">Harga</a>
            <a href="#faq" @click="open=false">FAQ</a>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="nav-cta" target="_blank">
                Pesan Sekarang
            </a>
            <button class="nav-toggle" @click="open=!open" aria-label="Menu">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h12M4 10h12M4 14h12"/></svg>
            </button>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="w">
        <div class="pill hi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            Card NFC + QR Code untuk Google Review
        </div>
        <h1 class="hi hi-1">
            Pelanggan tap HP.<br>
            <span class="accent">Review langsung masuk.</span>
        </h1>
        <p class="hero-sub hi hi-2">
            Card akrilik premium dengan NFC dan QR Code yang membuka halaman Google Review toko kamu secara otomatis. Tanpa install, tanpa langkah rumit.
        </p>
        <div class="hero-btns hi hi-3">
            <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="btn-primary" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pesan via WhatsApp
            </a>
            <a href="#cara-kerja" class="btn-secondary">
                Lihat cara kerjanya
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="hero-note hi hi-4">
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                Sekali bayar, pakai selamanya
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                Tanpa biaya bulanan
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                Setup 30 detik
            </span>
        </div>
        <div class="hero-img-wrap hi">
            <div class="hero-img-bar">
                <span class="dot-r"></span>
                <span class="dot-y"></span>
                <span class="dot-g"></span>
            </div>
            <img src="/img/landing.jpg" alt="Provecho Google Review Card">
        </div>
    </div>
</section>

<!-- STATS -->
<div class="divider"></div>
<div class="stats">
    <div class="w">
        <div class="stats-grid">
            <div class="stat rv">
                <div class="stat-n"><span class="blue">3</span> detik</div>
                <div class="stat-l">dari tap ke Google Review terbuka</div>
            </div>
            <div class="stat rv rv-1">
                <div class="stat-n">Rp 50.000</div>
                <div class="stat-l">harga per card, bayar sekali</div>
            </div>
            <div class="stat rv rv-2">
                <div class="stat-n"><span class="green">Rp 0</span></div>
                <div class="stat-l">biaya berlangganan selamanya</div>
            </div>
            <div class="stat rv rv-3">
                <div class="stat-n">∞</div>
                <div class="stat-l">masa aktif card tanpa expired</div>
            </div>
        </div>
    </div>
</div>
<div class="divider"></div>

<!-- CARA KERJA -->
<section class="sec" id="cara-kerja">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label">Cara Kerja</div>
            <h2 class="sec-title">Tiga langkah. Beres.</h2>
            <p class="sec-sub">Tidak perlu pengetahuan teknis. Tidak perlu install apa pun. Siapa pun bisa setup sendiri.</p>
        </div>
        <div class="how-grid">
            <div class="how-step active rv">
                <div class="how-num">1</div>
                <h3>Pesan & Terima Card</h3>
                <p>Hubungi kami via WhatsApp, pilih jumlah card. Card akrilik dikirim ke alamat kamu dalam 2–5 hari kerja. Bisa bayar transfer atau QRIS.</p>
            </div>
            <div class="how-step rv rv-1">
                <div class="how-num">2</div>
                <h3>Aktivasi dalam 30 Detik</h3>
                <p>Scan QR Code di card, cari nama usaha di Google, pilih — selesai. Atau paste link Google Maps langsung. Tidak perlu hubungi siapa pun.</p>
            </div>
            <div class="how-step rv rv-2">
                <div class="how-num">3</div>
                <h3>Taruh & Biarkan Bekerja</h3>
                <p>Letakkan di meja kasir. Pelanggan tap NFC atau scan QR — Google Review langsung terbuka. Review mengalir sendiri tiap hari.</p>
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="sec feat-bg" id="fitur">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label">Fitur</div>
            <h2 class="sec-title">Semua yang kamu butuhkan, sudah ada.</h2>
            <p class="sec-sub">Tidak ada fitur premium yang dikunci. Tidak ada upsell. Beli card, semua langsung bisa dipakai.</p>
        </div>
        <div class="feat-grid rv">
            <div class="feat">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/><path d="M8.5 7C9.5 5.5 11 5 12 5s2.5.5 3.5 2"/></svg>
                </div>
                <h3>NFC Tap — Tap Sekali, Langsung Terbuka</h3>
                <p>Dekatkan HP ke card, Google Review langsung terbuka otomatis. Tidak perlu buka aplikasi atau ketik nama toko.</p>
            </div>
            <div class="feat">
                <div class="feat-icon g">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                </div>
                <h3>QR Code — Untuk Semua Jenis HP</h3>
                <p>HP tidak ada NFC? Tidak masalah. QR Code beresolusi tinggi tersedia di card yang sama, kompatibel dengan semua perangkat.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <h3>Link Bisa Diganti Kapan Saja</h3>
                <p>Pindah lokasi atau ingin ganti profil Google? Link bisa diubah sendiri kapan saja. Card yang sama, tapi tujuan berbeda.</p>
            </div>
            <div class="feat">
                <div class="feat-icon g">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Akrilik Premium, Kesan Profesional</h3>
                <p>Material akrilik hitam tebal 5mm dengan holder transparan. Terlihat elegan di meja kasir dan memberi kesan usaha yang kredibel.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Aktivasi Sendiri, Tanpa Bantuan</h3>
                <p>Setup kurang dari 30 detik. Panduan lengkap sudah tersedia. Tidak perlu kirim data atau menunggu bantuan dari kami untuk memulai.</p>
            </div>
            <div class="feat">
                <div class="feat-icon g">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <h3>Support WhatsApp Langsung</h3>
                <p>Ada pertanyaan atau masalah? Chat langsung ke WhatsApp kami. Respons cepat, oleh manusia bukan bot, dan gratis untuk semua pembeli.</p>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCT SHOWCASE -->
<section class="sec">
    <div class="w">
        <div class="split rv">
            <div class="split-text">
                <div class="tag">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    Desain Card
                </div>
                <h2>Card yang langsung dimengerti pelanggan.</h2>
                <p>Desain card dirancang agar pelanggan langsung tahu apa yang harus dilakukan — tanpa perlu dijelaskan. Tap NFC atau scan QR, pilih salah satu.</p>
                <ul class="check-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Instruksi "Tap Your Phone" dan "Scan QR" tercetak jelas
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Branding Google resmi — pelanggan langsung percaya
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        QR Code resolusi tinggi, tidak blur meski dari jauh
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Holder transparan included, langsung bisa dipajang
                    </li>
                </ul>
            </div>
            <div class="split-img">
                <img src="/img/landing.jpg" alt="Desain Provecho Card">
            </div>
        </div>
    </div>
</section>

<!-- COMPARISON -->
<section class="sec comp-bg" id="kenapa">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label">Perbandingan</div>
            <h2 class="sec-title" style="color:#fff">Bukan card NFC yang biasa itu.</h2>
            <p class="sec-sub">Beda Rp 5.000–10.000 dari card marketplace — tapi hasilnya jauh berbeda.</p>
        </div>
        <div class="comp-table">
            <div class="comp-col plain rv">
                <h3>Card NFC Marketplace</h3>
                <div class="comp-sub">Yang dijual seharga Rp 40.000–45.000</div>
                <div class="comp-rows">
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Plastik tipis, mudah lecet dan kotor
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Tidak ada QR Code backup
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Setup rumit, harus kirim data ke penjual
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Desain generic, tidak meyakinkan pelanggan
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                        Tidak ada support setelah transaksi selesai
                    </div>
                </div>
                <div class="comp-price">Harga sekitar Rp 40.000–45.000</div>
            </div>
            <div class="comp-col prov rv rv-1">
                <h3>Provecho Google Review Card</h3>
                <div class="comp-sub">Dirancang khusus untuk dapat ulasan Google</div>
                <div class="comp-rows">
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Akrilik hitam premium 5mm + holder transparan
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        NFC dan QR Code dalam satu card
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Aktivasi sendiri dalam 30 detik tanpa kirim data
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Desain dengan branding Google yang meyakinkan
                    </div>
                    <div class="comp-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Support WhatsApp aktif untuk semua pembeli
                    </div>
                </div>
                <div class="comp-price">
                    <strong>Rp 50.000</strong>
                    Sekali bayar, aktif selamanya
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="sec" id="harga">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label">Harga</div>
            <h2 class="sec-title">Satu harga. Semua termasuk.</h2>
            <p class="sec-sub">Tidak ada biaya tersembunyi, tidak ada langganan, tidak ada upsell. Bayar sekali, pakai selamanya.</p>
        </div>
        <div class="price-wrap rv">
            <div class="price-card">
                <div class="price-badge">Google Review Card</div>
                <div class="price-amount"><small>Rp </small>50.000</div>
                <div class="price-note">per card · sekali bayar · aktif selamanya</div>
                <ul class="price-list">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Card akrilik premium 5mm + holder transparan
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        NFC chip + QR Code resolusi tinggi
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Link bisa diganti kapan saja, gratis
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Aktivasi sendiri, panduan tersedia
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Support WhatsApp after-purchase
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Gratis ongkir untuk order 3 card ke atas
                    </li>
                </ul>
                <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="btn-full" target="_blank">
                    Pesan via WhatsApp
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <div class="price-hint">Harga lebih hemat untuk order banyak — tanya via WA</div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="sec testi-bg">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label">Testimoni</div>
            <h2 class="sec-title">Yang pemilik usaha bilang.</h2>
            <p class="sec-sub">Dari cafe, restoran, barbershop, hingga toko ritel — mereka sudah pakai Provecho.</p>
        </div>
        <div class="testi-grid">
            @php $testis = [
                ['t'=>'Sebelum punya card ini Google Review kami cuma 12. Sekarang sudah 40+ dalam sebulan. Pelanggan senang karena gampang banget.','n'=>'Kak Rina','r'=>'Pemilik Cafe Mungil','i'=>'KR'],
                ['t'=>'Simple banget. Taruh di kasir, pelanggan tap sendiri. Tidak perlu minta-minta lagi. Review masuk terus tiap hari.','n'=>'Pak Budi','r'=>'Barbershop Budi & Bros','i'=>'PB'],
                ['t'=>'Awalnya ragu, tapi setelah coba ternyata gampang. Setup 5 menit langsung jalan. Worth it banget untuk harganya.','n'=>'Mbak Sari','r'=>'Toko Oleh-Oleh Sari','i'=>'MS'],
                ['t'=>'Udah order 3 card untuk 3 cabang. Semua jalan lancar. Pelanggan tidak perlu cari nama toko dulu di Google.','n'=>'Mas Doni','r'=>'Warung Makan Doni Jaya','i'=>'MD'],
                ['t'=>'Card-nya kelihatan premium. Pelanggan yang lihat pasti nanya ini apaan — jadi conversation starter juga.','n'=>'Kak Fara','r'=>'Boutique Fara Collection','i'=>'KF'],
                ['t'=>'Google Review restoran kami naik drastis. Pelanggan lebih mau review karena tidak perlu cari nama toko sendiri.','n'=>'Chef Andi','r'=>'Restoran Andi Masak','i'=>'CA'],
            ]; @endphp
            @foreach($testis as $t)
            <div class="testi-card rv">
                <div class="testi-stars">
                    @for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor
                </div>
                <p class="testi-text">"{{ $t['t'] }}"</p>
                <div class="testi-author">
                    <div class="testi-av">{{ $t['i'] }}</div>
                    <div>
                        <div class="testi-name">{{ $t['n'] }}</div>
                        <div class="testi-role">{{ $t['r'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="sec" id="faq">
    <div class="wn">
        <div class="sec-head ctr rv">
            <div class="sec-label">FAQ</div>
            <h2 class="sec-title">Pertanyaan yang sering ditanya.</h2>
        </div>
        <div class="faq-list" x-data="{open:null}">
            @php $faqs = [
                ['q'=>'HP pelanggan harus punya aplikasi khusus?','a'=>'Tidak perlu aplikasi apa pun. NFC sudah tersedia di hampir semua HP Android dan iPhone terbaru — cukup dekatkan ke card, Google Review langsung terbuka di browser.'],
                ['q'=>'Bagaimana cara aktivasinya?','a'=>'Setelah card tiba, scan QR Code di bagian bawah card. Kamu akan diarahkan ke halaman setup — cari nama usaha di Google atau paste link Google Maps, pilih, selesai. Prosesnya kurang dari 30 detik.'],
                ['q'=>'Kalau mau ganti link atau pindah lokasi, bagaimana?','a'=>'Bisa diganti kapan saja dan gratis. Scan QR Code di card, login ke dashboard, ubah link — selesai dalam hitungan detik. Card yang sama, link yang diperbarui.'],
                ['q'=>'Berapa lama pengirimannya?','a'=>'2–5 hari kerja tergantung lokasi. Kami menggunakan ekspedisi terpercaya dengan resi yang bisa kamu pantau setelah card dikirim.'],
                ['q'=>'Bisa order banyak untuk beberapa cabang?','a'=>'Bisa. Makin banyak order makin hemat — hubungi kami via WhatsApp untuk harga khusus. Setiap card bisa diaktivasi ke link berbeda.'],
                ['q'=>'Ada garansi jika card tidak berfungsi?','a'=>'Ada. Jika card tidak berfungsi dalam 30 hari pertama karena cacat produksi, kami ganti gratis. Cukup hubungi kami via WhatsApp dengan foto card yang bermasalah.'],
            ]; @endphp
            @foreach($faqs as $i => $f)
            <div class="faq-item rv" :class="{open:open==={{ $i }}}" @click="open=open==={{ $i }}?null:{{ $i }}">
                <button class="faq-q">
                    {{ $f['q'] }}
                    <span class="faq-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    </span>
                </button>
                <div class="faq-a"><p>{{ $f['a'] }}</p></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-sec">
    <div class="w">
        <div class="cta-box rv">
            <h2>Mulai dapat lebih banyak<br>Google Review hari ini.</h2>
            <p>Satu card. Sekali bayar. Review mengalir terus tanpa usaha tambahan.</p>
            <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="btn-white" target="_blank">
                Pesan via WhatsApp
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <div class="cta-note">Gratis ongkir untuk order 3+ card · Respons dalam 1 jam</div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="foot">
    <div class="w">
        <div class="foot-inner">
            <div>
                <div class="foot-brand">
                    <img src="/img/logo.png" alt="Provecho">
                    Provecho
                </div>
            </div>
            <div class="foot-links">
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#fitur">Fitur</a>
                <a href="#harga">Harga</a>
                <a href="#faq">FAQ</a>
                <a href="https://wa.me/6283842843671" target="_blank">WhatsApp</a>
            </div>
        </div>
        <div class="foot-copy">© {{ date('Y') }} Provecho. Hak cipta dilindungi.</div>
    </div>
</footer>

<!-- WA FLOAT -->
<a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20tanya%20tentang%20Provecho" class="wa" target="_blank" aria-label="WhatsApp">
    <div class="wa-btn">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
    </div>
</a>

<script>
const io = new IntersectionObserver(es => {
    es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('vis'); io.unobserve(e.target) } })
}, { threshold: 0.06, rootMargin: '0px 0px -28px 0px' });
document.querySelectorAll('.rv').forEach(el => io.observe(el));
</script>
</body>
</html>
