<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provecho — Google Review Card</title>
    <meta name="description" content="Card NFC + QR Code yang langsung membuka halaman ulasan Google usaha kamu. Pelanggan cukup tap HP atau scan QR. Datang siap pakai, garansi seumur hidup.">
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
    /* floating pill nav */
    .nav { position: fixed; top: 14px; left: 16px; right: 16px; z-index: 100; transition: top .25s }
    .nav-inner {
        max-width: 1000px; margin: 0 auto; padding: 0 8px 0 18px;
        height: 60px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: rgba(255,255,255,.72); backdrop-filter: blur(20px) saturate(1.6); -webkit-backdrop-filter: blur(20px) saturate(1.6);
        border: 1px solid rgba(228,234,242,.9); border-radius: 999px;
        box-shadow: 0 4px 20px -8px rgba(11,21,38,.08);
        transition: box-shadow .25s, background .25s;
    }
    .nav.scrolled { top: 10px }
    .nav.scrolled .nav-inner { background: rgba(255,255,255,.92); box-shadow: 0 12px 32px -12px rgba(11,21,38,.18) }
    .nav-logo {
        display: flex; align-items: center; gap: 9px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 17px; color: var(--fg); letter-spacing: -.03em;
    }
    .nav-logo img { width: 30px; height: 30px }
    .nav-links { display: flex; align-items: center; gap: 2px; position: relative }
    .nav-links a { position: relative; z-index: 1; font-size: 14px; font-weight: 500; color: var(--fg2); padding: 8px 14px; border-radius: 999px; transition: color .2s }
    .nav-links a:hover, .nav-links a.active { color: var(--fg) }
    /* pill yang meluncur ke link aktif / yang disorot (posisi diatur JS) */
    .nav-ind {
        position: absolute; top: 50%; left: 0; height: 36px; width: 0; border-radius: 999px; pointer-events: none; opacity: 0;
        transform: translate(var(--x, 0), -50%);
        background: linear-gradient(120deg, rgba(14,165,233,.14), rgba(16,185,129,.14));
        box-shadow: inset 0 0 0 1px rgba(14,165,233,.22), 0 4px 12px -6px rgba(14,165,233,.45);
        transition: transform .4s cubic-bezier(.16,1,.3,1), width .4s cubic-bezier(.16,1,.3,1), opacity .25s;
    }
    .nav-ind::after { content: ''; position: absolute; left: 50%; bottom: -7px; width: 4px; height: 4px; margin-left: -2px; border-radius: 50%; background: linear-gradient(135deg, var(--blue), var(--green)) }
    .nav-ind.on { opacity: 1 }
    .nav-cta {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(120deg, var(--blue2) 0%, var(--blue) 45%, var(--green) 100%); color: #fff; font-size: 13.5px; font-weight: 700;
        padding: 11px 18px 11px 20px; border-radius: 999px; transition: transform .15s, box-shadow .15s;
        font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -.01em;
        box-shadow: 0 8px 18px -10px rgba(14,165,233,.8), inset 0 1px 0 rgba(255,255,255,.25);
    }
    .nav-cta svg { width: 15px; height: 15px; transition: transform .15s }
    .nav-cta:hover { transform: translateY(-1px); box-shadow: 0 12px 24px -10px rgba(14,165,233,.9), inset 0 1px 0 rgba(255,255,255,.25) }
    .nav-cta:hover svg { transform: translateX(2px) }
    .cta-short { display: none }
    .nav-toggle { display: none; width: 42px; height: 42px; border-radius: 999px; border: 1px solid var(--border); background: var(--white); position: relative; flex-shrink: 0 }
    .nav-toggle span { position: absolute; left: 12px; right: 12px; height: 2px; border-radius: 2px; background: var(--fg); transition: transform .3s cubic-bezier(.16,1,.3,1), opacity .2s }
    .nav-toggle span:nth-child(1) { top: 14px }
    .nav-toggle span:nth-child(2) { top: 20px }
    .nav-toggle span:nth-child(3) { top: 26px }
    .nav.open .nav-toggle span:nth-child(1) { transform: translateY(6px) rotate(45deg) }
    .nav.open .nav-toggle span:nth-child(2) { opacity: 0 }
    .nav.open .nav-toggle span:nth-child(3) { transform: translateY(-6px) rotate(-45deg) }
    .nav-panel {
        position: absolute; top: calc(100% + 10px); left: 0; right: 0; padding: 10px;
        background: rgba(255,255,255,.96); backdrop-filter: blur(20px) saturate(1.6); -webkit-backdrop-filter: blur(20px) saturate(1.6);
        border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 30px 60px -24px rgba(11,21,38,.3);
        transform-origin: top right;
    }
    @media (min-width: 641px) { .nav-panel { display: none !important } }
    .np-enter, .np-leave { transition: opacity .2s ease, transform .25s cubic-bezier(.16,1,.3,1) }
    .np-from { opacity: 0; transform: translateY(-8px) scale(.97) }
    .np-to { opacity: 1; transform: none }
    .np-links { display: flex; flex-direction: column }
    .np-links a { display: flex; align-items: center; gap: 12px; padding: 11px 12px; border-radius: 14px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--fg); transition: background .15s }
    .np-links a:hover, .np-links a:active { background: var(--bg) }
    .np-links i { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: var(--blue-bg); color: var(--blue); flex-shrink: 0 }
    .np-links i.g { background: var(--green-bg); color: var(--green) }
    .np-links i svg { width: 17px; height: 17px }
    .np-links b { margin-left: auto; font-size: 20px; font-weight: 400; color: var(--fg3); line-height: 1 }
    .np-actions { display: grid; gap: 8px; padding: 10px 2px 2px; margin-top: 6px; border-top: 1px solid var(--border) }
    .np-buy { display: flex; align-items: center; justify-content: center; gap: 9px; padding: 14px; border-radius: 999px; background: linear-gradient(135deg,#FF6A3D,#EE4D2D); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; box-shadow: 0 12px 24px -12px rgba(238,77,45,.7) }
    .np-note { display: flex; align-items: center; justify-content: center; gap: 7px; padding: 12px 0 4px; font-size: 12.5px; font-weight: 600; color: var(--fg2) }
    .np-note svg { width: 18px; height: 18px; padding: 3px; border-radius: 50%; color: #fff; background: linear-gradient(135deg,var(--blue),var(--green)) }

    /* ─── HERO ─── */
    .hero {
        position: relative; overflow: hidden; padding: 150px 0 88px; text-align: center;
        background:
            radial-gradient(55% 60% at 8% 0%,   rgba(14,165,233,.30), transparent 65%),
            radial-gradient(50% 55% at 95% 8%,  rgba(16,185,129,.28), transparent 65%),
            radial-gradient(45% 40% at 50% 55%, rgba(56,189,248,.14), transparent 70%),
            linear-gradient(180deg, #E3F3FD 0%, #ECFAF5 55%, #FFFFFF 100%);
    }
    .hero::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(14,165,233,.22) 1px, transparent 1px); background-size: 24px 24px;
        -webkit-mask-image: radial-gradient(ellipse 75% 60% at 50% 35%, #000 25%, transparent 75%);
        mask-image: radial-gradient(ellipse 75% 60% at 50% 35%, #000 25%, transparent 75%);
    }
    /* dua cahaya biru & hijau yang bergerak pelan */
    .hero::after {
        content: ''; position: absolute; inset: -20% -10% 20%; pointer-events: none;
        background: radial-gradient(28% 38% at 30% 35%, rgba(14,165,233,.22), transparent 70%),
                    radial-gradient(26% 34% at 72% 30%, rgba(16,185,129,.20), transparent 70%);
        filter: blur(30px);
        animation: hero-drift 14s ease-in-out infinite alternate;
    }
    @keyframes hero-drift { to { transform: translate3d(4%, 6%, 0) scale(1.08) } }
    @media (prefers-reduced-motion: reduce) { .hero::after, .testi-bg::after { animation: none } }
    .hero > .w { position: relative; z-index: 1 }
    .pill {
        display: inline-flex; align-items: center; gap: 10px;
        border: 1px solid var(--border); background: rgba(255,255,255,.8);
        color: var(--fg2); font-size: 13px; font-weight: 500;
        padding: 5px 14px 5px 5px; border-radius: 999px; margin-bottom: 30px;
        box-shadow: var(--shadow-sm);
    }
    .pill b { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; color: #fff; background: linear-gradient(90deg,var(--blue),var(--green)); padding: 4px 10px; border-radius: 999px }
    .pill svg { width: 11px; height: 11px; flex-shrink: 0 }
    .hero h1 {
        font-size: clamp(40px, 6vw, 72px); font-weight: 900; color: var(--fg);
        line-height: 1.02; letter-spacing: -.045em; max-width: 820px; margin: 0 auto 22px;
    }
    .hero h1 .accent { background: linear-gradient(90deg, var(--blue) 10%, var(--green) 90%); -webkit-background-clip: text; background-clip: text; color: transparent; padding-bottom: .06em }
    .hero-sub {
        font-size: 18px; color: var(--fg2); line-height: 1.65;
        max-width: 520px; margin: 0 auto 36px;
    }
    .hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 24px }
    .btn-primary {
        display: inline-flex; align-items: center; gap: 9px;
        background: var(--blue); color: #fff; font-size: 15px; font-weight: 700;
        padding: 15px 28px; border-radius: 999px; transition: background .15s, transform .15s, box-shadow .15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 10px 28px -10px rgba(14,165,233,.7), inset 0 1px 0 rgba(255,255,255,.25);
    }
    .btn-primary:hover { background: var(--blue2); transform: translateY(-2px); box-shadow: 0 14px 32px -10px rgba(14,165,233,.8) }
    .btn-secondary {
        display: inline-flex; align-items: center; gap: 8px; background: var(--white);
        border: 1px solid var(--border); color: var(--fg); font-size: 15px; font-weight: 600;
        padding: 15px 26px; border-radius: 999px; transition: border-color .15s, transform .15s, box-shadow .15s;
        box-shadow: var(--shadow-sm);
    }
    .btn-secondary svg { transition: transform .15s }
    .btn-secondary:hover { border-color: #CBD5E1; transform: translateY(-2px) }
    .btn-secondary:hover svg { transform: translateX(3px) }
    .hero-note { font-size: 13px; color: var(--fg2); display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 8px 20px; margin-bottom: 60px }
    .hero-note span { display: flex; align-items: center; gap: 6px }
    .hero-note svg { width: 15px; height: 15px; color: var(--green); background: var(--green-bg); border-radius: 50%; padding: 2px }
    .hero-note span { font-weight: 700; color: var(--fg); background: rgba(255,255,255,.85); border: 1px solid rgba(16,185,129,.35); padding: 4px 12px 4px 6px; border-radius: 999px }
    .hero-note span svg { width: 20px; height: 20px; padding: 3px; color: #fff; background: linear-gradient(135deg,var(--blue),var(--green)) }


    /* ─── HERO: jenis usaha berjalan ─── */
    .biz { margin-top: 4px }
    .biz-label { display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 12px; font-weight: 700; color: var(--fg3); text-transform: uppercase; letter-spacing: .12em; margin-bottom: 18px }
    .biz-label::before, .biz-label::after { content: ''; width: 40px; height: 1px; background: linear-gradient(90deg, transparent, var(--blue-border)) }
    .biz-label::after { transform: scaleX(-1) }
    .biz-rows { display: grid; gap: 12px;
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent);
        mask-image: linear-gradient(90deg, transparent, #000 12%, #000 88%, transparent) }
    .biz-row { overflow: hidden; padding-block: 2px 6px }
    .biz-track { display: flex; gap: 12px; padding-right: 12px; width: max-content; animation: biz-run 60s linear infinite }
    .biz-row.rev .biz-track { animation-direction: reverse; animation-duration: 70s }
    .biz-rows:hover .biz-track { animation-play-state: paused }
    @keyframes biz-run { to { transform: translateX(-50%) } }
    .biz-pill { display: inline-flex; align-items: center; gap: 10px; white-space: nowrap; padding: 7px 18px 7px 7px; border-radius: 999px;
        background: rgba(255,255,255,.92); border: 1px solid var(--border); box-shadow: 0 6px 16px -12px rgba(11,21,38,.3);
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; color: var(--fg); letter-spacing: -.01em }
    .biz-pill i { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--blue-bg); color: var(--blue); flex-shrink: 0 }
    .biz-row.rev .biz-pill i { background: var(--green-bg); color: var(--green) }
    .biz-pill i svg { width: 16px; height: 16px }
    @media (prefers-reduced-motion: reduce) {
        .biz-track { animation: none }
        .biz-row { overflow-x: auto }
    }
/* ─── STATS ─── */
    .stats { background: var(--bg); padding: 40px 0 }
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px }
    .stat { background: var(--white); border: 1px solid var(--border); border-radius: var(--r); padding: 20px 20px 18px }
    .stat-n { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 26px; font-weight: 900; color: var(--fg); letter-spacing: -.04em; line-height: 1; margin-bottom: 6px }
    .stat-n .blue { color: var(--blue) }
    .stat-n .green { color: var(--green) }
    .stat-l { font-size: 12px; color: var(--fg3); font-weight: 500; line-height: 1.4 }

    /* ─── SECTION BASE ─── */
    .sec { padding: 88px 0 }
    .sec-label {
        display: inline-flex; align-items: center; gap: 9px; padding: 5px 16px 5px 5px; margin-bottom: 16px;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12.5px; font-weight: 800; color: var(--fg); text-transform: uppercase; letter-spacing: .1em;
        background: linear-gradient(120deg, rgba(255,255,255,.95), rgba(240,249,255,.95)); border: 1px solid var(--blue-border); border-radius: 999px;
        box-shadow: 0 8px 20px -12px rgba(14,165,233,.55);
    }
    .sec-label i { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #fff;
        background: linear-gradient(135deg, var(--blue), var(--green)); box-shadow: 0 4px 10px -4px rgba(14,165,233,.7) }
    .sec-label i svg { width: 15px; height: 15px }
    .sec-title { font-size: clamp(24px,3.2vw,38px); font-weight: 800; color: var(--fg); margin-bottom: 12px }
    .sec-sub { font-size: 15.5px; color: var(--fg2); line-height: 1.7 }
    .sec-head { margin-bottom: 52px }
    .sec-head.ctr { text-align: center }
    .sec-head.ctr .sec-sub { max-width: 480px; margin: 0 auto }

    /* ─── FEATURES ─── */
    .feat-bg { background: var(--bg) }
    .feat-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px }
    .feat {
        background: var(--white); border: 1px solid var(--border); border-radius: 18px; padding: 28px 26px;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s;
    }
    .feat:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -20px rgba(11,21,38,.18); border-color: #D3DCE8 }
    .feat-icon { width: 42px; height: 42px; border-radius: 12px; background: var(--blue-bg); display: flex; align-items: center; justify-content: center; margin-bottom: 18px; color: var(--blue); transition: transform .25s }
    .feat:hover .feat-icon { transform: scale(1.08) rotate(-4deg) }
    .feat-icon svg { width: 21px; height: 21px }
    .feat-icon.g { background: var(--green-bg); color: var(--green) }
    .feat h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 800; margin-bottom: 8px; color: var(--fg); letter-spacing: -.01em }
    .feat p { font-size: 14px; color: var(--fg2); line-height: 1.65 }

    /* tile besar: teks + visual */
    .feat.big { grid-column: span 2; display: grid; grid-template-columns: 1fr 220px; gap: 24px; align-items: center }
    .feat.tall { display: flex; flex-direction: column }
    .feat-vis { position: relative; height: 180px; border-radius: 14px; background: linear-gradient(160deg,#EFF8FF,#F1FBF7); overflow: hidden; display: flex; align-items: center; justify-content: center }
    .feat.tall .feat-vis { height: 150px; margin-bottom: 22px }
    .feat-chip { display: inline-flex; gap: 6px; flex-wrap: wrap; margin-top: 16px }
    .feat-chip span { font-size: 11.5px; font-weight: 600; color: var(--fg2); background: var(--bg); border: 1px solid var(--border); padding: 4px 10px; border-radius: 999px }

    /* visual NFC: HP mendekat ke card, gelombang sinyal */
    .nfc-card { width: 92px; height: 120px; border-radius: 12px; background: #0B1526; position: absolute; left: 28px; bottom: 26px; box-shadow: 0 10px 24px -8px rgba(11,21,38,.45); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px }
    .nfc-card b { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 22px; font-weight: 900; background: linear-gradient(135deg,var(--blue),var(--green)); -webkit-background-clip: text; background-clip: text; color: transparent }
    .nfc-card i { font-style: normal; font-size: 9px; letter-spacing: 2px; color: #FBBF24 }
    .nfc-phone { width: 62px; height: 112px; border-radius: 14px; border: 3px solid #0B1526; background: var(--white); position: absolute; right: 30px; top: 22px; animation: phone-tap 3s ease-in-out infinite; display: flex; align-items: center; justify-content: center }
    .nfc-phone::before { content: ''; position: absolute; top: 5px; width: 16px; height: 3px; border-radius: 3px; background: #0B1526 }
    .nfc-phone svg { width: 26px; height: 26px; color: var(--green); opacity: 0; animation: phone-ok 3s ease-in-out infinite }
    .nfc-wave { position: absolute; left: 74px; top: 92px; width: 30px; height: 30px; border-radius: 50%; border: 2px solid var(--blue); opacity: 0; animation: wave 3s ease-out infinite }
    .nfc-wave.w2 { animation-delay: .25s }
    @keyframes phone-tap { 0%,15% { transform: translate(0,0) rotate(8deg) } 40%,70% { transform: translate(-46px,24px) rotate(-6deg) } 90%,100% { transform: translate(0,0) rotate(8deg) } }
    @keyframes phone-ok { 0%,42% { opacity: 0; transform: scale(.6) } 50%,72% { opacity: 1; transform: scale(1) } 85%,100% { opacity: 0 } }
    @keyframes wave { 0%,38% { opacity: 0; transform: scale(.4) } 45% { opacity: .9 } 75%,100% { opacity: 0; transform: scale(3.4) } }

    /* visual QR: garis scan naik-turun */
    .qr-box { position: relative; width: 112px; height: 112px; padding: 10px; background: var(--white); border-radius: 12px; box-shadow: 0 8px 20px -10px rgba(11,21,38,.25) }
    .qr-box svg { width: 100%; height: 100%; display: block }
    .qr-scan { position: absolute; left: 6px; right: 6px; top: 8px; height: 2px; background: var(--green); box-shadow: 0 0 12px 2px rgba(16,185,129,.55); border-radius: 2px; animation: qr-scan 2.4s ease-in-out infinite alternate }
    @keyframes qr-scan { to { top: calc(100% - 10px) } }

    /* banner support */
    .feat.wide { grid-column: 1 / -1; display: flex; align-items: center; gap: 22px; background: linear-gradient(100deg,#ECFDF5,#FFFFFF 60%); border-color: rgba(16,185,129,.25) }
    .feat.wide .feat-icon { margin: 0; flex-shrink: 0; width: 52px; height: 52px; border-radius: 14px }
    .feat.wide .feat-icon svg { width: 25px; height: 25px }
    .feat.wide > div:not(.feat-icon) { flex: 1 }
    @media (prefers-reduced-motion: reduce) {
        .nfc-phone, .nfc-phone svg, .nfc-wave, .qr-scan { animation: none }
    }

    /* ─── HOW ─── */
    /* 3 langkah aktif bergantian, siklus 7.5s (2.5s per langkah) */
    .how-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px }
    .how-step {
        background: var(--white); border: 1.5px solid var(--border); border-radius: 18px; padding: 28px 26px 30px;
        text-align: left; position: relative; overflow: hidden;
        opacity: 0; transform: translateY(22px);
        transition: opacity .45s ease, transform .45s ease;
    }
    .how-grid.vis .how-step:nth-child(1) { opacity:1; transform:none }
    .how-grid.vis .how-step:nth-child(2) { opacity:1; transform:none; transition-delay:.15s }
    .how-grid.vis .how-step:nth-child(3) { opacity:1; transform:none; transition-delay:.3s }
    .how-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px }
    .how-num {
        width: 44px; height: 44px; border-radius: 12px;
        border: 1.5px solid var(--border); background: var(--bg);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 800; color: var(--fg2);
    }
    .how-tag { font-size: 11px; font-weight: 700; color: var(--fg3); letter-spacing: .1em; text-transform: uppercase }
    .how-step h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 17px; font-weight: 800; margin-bottom: 10px; color: var(--fg); letter-spacing: -.01em }
    .how-step p { font-size: 14.5px; color: var(--fg2); line-height: 1.7 }
    .how-bar { position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: var(--border) }
    .how-bar span { display: block; height: 100%; background: linear-gradient(90deg,var(--blue),var(--green)); transform: scaleX(0); transform-origin: left }

    @keyframes how-card {
        0%, 31%   { border-color: rgba(14,165,233,.45); box-shadow: 0 18px 40px -18px rgba(14,165,233,.35) }
        36%, 100% { border-color: var(--border); box-shadow: none }
    }
    @keyframes how-num {
        0%, 31%   { background: linear-gradient(135deg,var(--blue),var(--green)); border-color: transparent; color: #fff }
        36%, 100% { background: var(--bg); border-color: var(--border); color: var(--fg2) }
    }
    @keyframes how-tag {
        0%, 31%   { color: var(--blue) }
        36%, 100% { color: var(--fg3) }
    }
    @keyframes how-fill {
        0%        { transform: scaleX(0); opacity: 1 }
        31%       { transform: scaleX(1); opacity: 1 }
        36%, 100% { transform: scaleX(1); opacity: 0 }
    }
    .how-step:nth-child(1) { --d: 0s }
    .how-step:nth-child(2) { --d: 2.5s }
    .how-step:nth-child(3) { --d: 5s }
    .how-grid.vis .how-step               { animation: how-card 7.5s ease var(--d) infinite }
    .how-grid.vis .how-step .how-num      { animation: how-num  7.5s ease var(--d) infinite }
    .how-grid.vis .how-step .how-tag      { animation: how-tag  7.5s ease var(--d) infinite }
    .how-grid.vis .how-step .how-bar span { animation: how-fill 7.5s linear var(--d) infinite }
    @media (prefers-reduced-motion: reduce) {
        .how-grid.vis .how-step, .how-grid.vis .how-step * { animation: none !important }
    }

    /* ─── PRODUCT SPLIT ─── */
    .showcase {
        position: relative; overflow: hidden; border-radius: 28px; padding: 56px;
        background: radial-gradient(60% 80% at 100% 0%, rgba(16,185,129,.14), transparent 60%),
                    radial-gradient(60% 80% at 0% 100%, rgba(14,165,233,.14), transparent 60%),
                    linear-gradient(135deg, #F2F9FF, #F3FCF8);
        border: 1px solid #E1ECF5;
    }
    .split { display: grid; grid-template-columns: 1.05fr 1fr; gap: 56px; align-items: center }
    .split.rev .split-img { order: -1 }
    .split-text h2 { font-size: clamp(26px,3.2vw,40px); font-weight: 900; margin-bottom: 14px; letter-spacing: -.035em; line-height: 1.08 }
    .split-text h2 em { font-style: normal; background: linear-gradient(90deg,var(--blue),var(--green)); -webkit-background-clip: text; background-clip: text; color: transparent }
    .split-text > p { font-size: 15.5px; color: var(--fg2); line-height: 1.7; margin-bottom: 28px; max-width: 460px }
    .spec-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px }
    .spec { display: flex; gap: 12px; align-items: flex-start; background: rgba(255,255,255,.75); border: 1px solid var(--border); border-radius: 14px; padding: 14px; transition: transform .2s, box-shadow .2s }
    .spec:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -14px rgba(11,21,38,.2) }
    .spec-ic { width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--blue-bg); color: var(--blue) }
    .spec-ic.g { background: var(--green-bg); color: var(--green) }
    .spec-ic svg { width: 17px; height: 17px }
    .spec b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: var(--fg); margin-bottom: 2px }
    .spec div span { display: block; font-size: 12.5px; color: var(--fg2); line-height: 1.45 }

    /* panggung card: glow + sedikit miring + chip melayang */
    .split-img { position: relative; display: flex; justify-content: center; padding: 20px 10px }
    .split-img::before { content: ''; position: absolute; inset: 12% 8%; border-radius: 50%; background: linear-gradient(135deg,var(--blue),var(--green)); filter: blur(60px); opacity: .35 }
    .split-img img { position: relative; width: 100%; max-width: 440px; border-radius: 18px; box-shadow: 0 30px 60px -24px rgba(11,21,38,.4); transform: rotate(-3deg); transition: transform .4s ease }
    .split-img:hover img { transform: rotate(0) scale(1.02) }
    .float-chip { position: absolute; z-index: 2; display: flex; align-items: center; gap: 8px; background: var(--white); border: 1px solid var(--border); border-radius: 12px; padding: 9px 13px; font-size: 12.5px; font-weight: 600; color: var(--fg); box-shadow: 0 12px 28px -12px rgba(11,21,38,.25); animation: chip-float 5s ease-in-out infinite }
    .float-chip i { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: var(--blue-bg); color: var(--blue); font-style: normal }
    .float-chip i svg { width: 14px; height: 14px }
    .float-chip small { display: block; font-size: 11px; font-weight: 500; color: var(--fg3) }
    .float-chip.c1 { top: 6%; left: -4% }
    .float-chip.c2 { bottom: 8%; right: -4%; animation-delay: -2.5s }
    .float-chip.c2 i { background: var(--green-bg); color: var(--green) }
    @keyframes chip-float { 50% { transform: translateY(-8px) } }
    @media (prefers-reduced-motion: reduce) { .float-chip { animation: none } }

    /* ─── COMPARISON ─── */
    .comp-bg { background: linear-gradient(180deg, var(--white) 0%, #F4F9FE 100%) }
    /* tabel: kolom aspek | biasa | provecho; kolom provecho jadi satu "kartu" lewat background per sel */
    .cmp { max-width: 900px; margin: 0 auto; position: relative; isolation: isolate; --prov-w: calc((100% - 178px) * 1.1 / 2.1) }
    .cmp-head, .cmp-row, .cmp-foot { display: grid; grid-template-columns: 150px 1fr 1.1fr; column-gap: 14px }
    .cmp-h { padding: 18px 22px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; letter-spacing: -.01em; display: flex; align-items: center; gap: 9px; position: relative }
    .cmp-h.plain { color: var(--fg3); font-weight: 700 }
    .cmp-h.prov { color: var(--fg); background: var(--white); border-radius: 20px 20px 0 0; border: 1.5px solid transparent; border-bottom: 0; padding-top: 26px }
    .cmp-h.prov img { width: 24px; height: 24px }
    .cmp-badge { position: absolute; z-index: 3; top: -12px; left: 22px; font-size: 10.5px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: .08em; padding: 4px 11px; border-radius: 999px; background: linear-gradient(90deg,var(--blue),var(--green)); box-shadow: 0 6px 16px -6px rgba(14,165,233,.6) }
    .cmp-aspek { padding: 18px 0; font-size: 13px; font-weight: 700; color: var(--fg); display: flex; align-items: center; border-top: 1px solid var(--border) }
    .cmp-cell { padding: 18px 22px; font-size: 14px; line-height: 1.5; display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--border) }
    .cmp-cell.plain { color: var(--fg2) }
    .cmp-cell i.eq { background: #EEF2F7; color: var(--fg3) }
    .cmp-who { display: none }
    .cmp-same { font-style: normal; margin-left: 8px; font-size: 11px; font-weight: 600; color: var(--fg3); background: #EEF2F7; padding: 2px 8px; border-radius: 999px; white-space: nowrap }
    .cmp-cell.prov { color: var(--fg); font-weight: 500; background: var(--white) }
    .cmp-cell i { width: 24px; height: 24px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center }
    .cmp-cell i svg { width: 12px; height: 12px }
    .cmp-cell i.no  { background: #FEF2F2; color: #F87171 }
    .cmp-cell i.yes { background: linear-gradient(135deg,var(--blue),var(--green)); color: #fff }
    .cmp-cta-wrap { background: var(--white); border-radius: 0 0 20px 20px; padding: 6px 22px 24px }
    .cmp-cta { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: var(--fg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; padding: 13px; border-radius: 999px; transition: transform .15s, box-shadow .15s }
    .cmp-cta:hover { transform: translateY(-1px); box-shadow: 0 10px 24px -10px rgba(11,21,38,.5) }
    /* bingkai gradien + bayangan untuk kolom provecho, digambar sekali di atas tabel */
    .cmp::after {
        content: ''; position: absolute; top: 0; bottom: 0; right: 0; width: var(--prov-w); z-index: 1;
        border-radius: 20px; pointer-events: none;
        padding: 1.5px; background: linear-gradient(160deg,var(--blue),var(--green));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor;
        mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
    }
    .cmp::before {
        content: ''; position: absolute; top: 0; bottom: 0; right: 0; width: var(--prov-w);
        border-radius: 20px; box-shadow: 0 30px 60px -30px rgba(14,165,233,.45); z-index: -1;
    }

    /* ─── ORDER ─── */
    .order-bg { background: linear-gradient(180deg, #F4F9FE 0%, var(--white) 100%) }
    .order-layout { display: grid; grid-template-columns: 1.15fr 1fr; gap: 20px; max-width: 960px; margin: 0 auto; align-items: stretch }
    .buy-card {
        background: var(--white); border: 1px solid var(--border); border-radius: 24px; padding: 32px;
        box-shadow: 0 30px 60px -36px rgba(238,77,45,.45); display: flex; flex-direction: column;
    }
    .buy-top { display: flex; align-items: center; gap: 14px; padding-bottom: 22px; border-bottom: 1px solid var(--border); margin-bottom: 22px }
    .shp { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 6px; background: #fff; flex-shrink: 0 }
    /* file logo berisi tas + tulisan; di ukuran kecil potong ke bagian tas saja */
    .shp img { width: 15px; height: 16px; object-fit: cover; object-position: top; display: block }
    .buy-logo { flex-shrink: 0; width: 60px; height: 60px; border-radius: 16px; background: #fff; border: 1px solid #FFE0CC; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px -12px rgba(238,77,45,.6) }
    .buy-logo img { height: 46px; width: auto }
    .buy-top h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px; font-weight: 800; letter-spacing: -.02em; color: var(--fg) }
    .buy-top p { font-size: 13px; color: var(--fg3); margin-top: 2px }
    .buy-label { font-size: 11px; font-weight: 700; color: var(--fg3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 14px }
    .buy-list { list-style: none; display: grid; grid-template-columns: 1fr 1fr; gap: 16px 18px; margin-bottom: 24px }
    .buy-list li { display: flex; gap: 12px; align-items: flex-start }
    .buy-list i { width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--blue-bg); color: var(--blue) }
    .buy-list li:nth-child(even) i { background: var(--green-bg); color: var(--green) }
    .buy-list i svg { width: 17px; height: 17px }
    .buy-list b { display: block; font-size: 14px; font-weight: 700; color: var(--fg) }
    .buy-list span { display: block; font-size: 12.5px; color: var(--fg2); line-height: 1.45; margin-top: 1px }
    .warranty { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; padding: 16px 18px; border-radius: 16px; position: relative;
        background: linear-gradient(120deg, #ECFDF5, #EFF8FF); border: 1px solid rgba(16,185,129,.3) }
    .warranty-ic { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg,var(--blue),var(--green)); color: #fff; box-shadow: 0 8px 18px -8px rgba(16,185,129,.7) }
    .warranty-ic svg { width: 22px; height: 22px }
    .warranty b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15.5px; font-weight: 800; color: var(--fg); letter-spacing: -.01em }
    .warranty span:not(.warranty-ic) { display: block; font-size: 13px; color: var(--fg2); line-height: 1.5; margin-top: 2px }
    .buy-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 24px }
    .buy-chips span { font-size: 12px; font-weight: 600; color: #C2410C; background: #FFF4ED; border: 1px solid #FFE0CC; padding: 5px 11px; border-radius: 999px }
    .buy-btn {
        margin-top: auto; display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%;
        background: linear-gradient(135deg,#FF6A3D,#EE4D2D); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15.5px; font-weight: 800; padding: 16px; border-radius: 999px;
        box-shadow: 0 14px 28px -12px rgba(238,77,45,.7), inset 0 1px 0 rgba(255,255,255,.25);
        transition: transform .15s, box-shadow .15s;
    }
    .buy-btn:hover { transform: translateY(-2px); box-shadow: 0 18px 32px -12px rgba(238,77,45,.8) }

    .order-side { display: flex; flex-direction: column; gap: 20px }
    .after-card { background: var(--white); border: 1px solid var(--border); border-radius: 24px; padding: 28px; flex: 1 }
    .after-steps { list-style: none; display: flex; flex-direction: column; position: relative }
    .after-steps::before { content: ''; position: absolute; left: 15px; top: 16px; bottom: 16px; width: 2px; background: linear-gradient(var(--blue),var(--green)); opacity: .35 }
    .after-steps li { display: flex; gap: 14px; align-items: flex-start; padding: 8px 0; position: relative }
    .after-steps li > span { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--white); border: 2px solid var(--blue); color: var(--blue); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 800 }
    .after-steps li:last-child > span { border-color: var(--green); background: var(--green); color: #fff }
    .after-steps b { display: block; font-size: 14px; font-weight: 700; color: var(--fg); margin-top: 5px }
    .after-steps small { display: block; font-size: 12.5px; color: var(--fg2) }
    .cs-card { border-radius: 24px; padding: 24px; background: linear-gradient(135deg,#ECFDF5,#F5FFFB); border: 1px solid rgba(16,185,129,.25) }
    .cs-top { display: flex; gap: 14px; margin-bottom: 18px }
    .cs-ic { width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: #25D366; color: #fff }
    .cs-ic svg { width: 21px; height: 21px }
    .cs-top b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; color: var(--fg); margin-bottom: 4px }
    .cs-top p { font-size: 13px; color: var(--fg2); line-height: 1.55 }
    .cs-btn { display: flex; align-items: center; justify-content: center; gap: 8px; background: var(--white); border: 1.5px solid #25D366; color: #15803D; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; padding: 12px; border-radius: 999px; transition: background .15s, color .15s }
    .cs-btn:hover { background: #25D366; color: #fff }

    /* ─── TESTIMONIALS ─── */
    .testi-bg { position: relative; overflow: hidden; background: linear-gradient(180deg, var(--white) 0%, #F2F9FF 45%, #F1FBF7 70%, var(--white) 100%) }
    .testi-bg::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(14,165,233,.18) 1px, transparent 1px); background-size: 24px 24px;
        -webkit-mask-image: radial-gradient(ellipse 60% 55% at 50% 55%, #000 20%, transparent 75%);
        mask-image: radial-gradient(ellipse 60% 55% at 50% 55%, #000 20%, transparent 75%);
    }
    .testi-bg::after {
        content: ''; position: absolute; inset: 10% -10% 0; pointer-events: none; filter: blur(30px);
        background: radial-gradient(30% 40% at 20% 55%, rgba(14,165,233,.16), transparent 70%),
                    radial-gradient(30% 40% at 80% 50%, rgba(16,185,129,.15), transparent 70%);
        animation: hero-drift 16s ease-in-out infinite alternate;
    }
    .testi-bg > * { position: relative; z-index: 1 }
    .sec-title .grad { font-style: normal; background: linear-gradient(90deg, var(--blue), var(--green)); -webkit-background-clip: text; background-clip: text; color: transparent }
    /* marquee: baris bisa di-scroll (swipe/drag), JS menggeser pelan; track berisi 2 set identik untuk loop */
    .marquee { display: flex; flex-direction: column; gap: 6px;
        -webkit-mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent);
        mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent) }
    .marquee-row { display: flex; overflow-x: auto; overflow-y: hidden; padding-block: 8px 16px; scrollbar-width: none; cursor: grab; touch-action: pan-x pan-y; overscroll-behavior-x: contain }
    .marquee-row::-webkit-scrollbar { display: none }
    .marquee-row.dragging { cursor: grabbing; user-select: none }
    .marquee-row.dragging .testi-card { pointer-events: none }
    .marquee-track { display: flex; gap: 18px; padding-right: 18px; width: max-content }
    .testi-card {
        width: 340px; flex-shrink: 0; margin: 0; display: flex; flex-direction: column;
        background: var(--white); border: 1px solid var(--border); border-radius: 18px; padding: 22px 22px 20px;
        box-shadow: 0 1px 2px rgba(11,21,38,.04), 0 12px 28px -18px rgba(11,21,38,.18);
        transition: transform .2s, border-color .2s, box-shadow .2s;
    }
    .testi-card { position: relative; overflow: hidden }
    /* border gradien muncul saat disorot */
    .testi-card::before {
        content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1.5px; pointer-events: none; opacity: 0; transition: opacity .25s;
        background: linear-gradient(135deg, var(--blue), var(--green));
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor;
        mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
    }
    .testi-card:hover { transform: translateY(-4px); border-color: transparent; box-shadow: 0 22px 40px -20px rgba(14,165,233,.45) }
    .testi-card:hover::before { opacity: 1 }
    .testi-tag { font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 999px; color: var(--blue2); background: var(--blue-bg) }
    .testi-text mark { color: inherit; font-weight: 700; padding: 0 2px; border-radius: 3px;
        background: linear-gradient(transparent 58%, rgba(14,165,233,.22) 58%, rgba(16,185,129,.22) 100%) }
    /* variasi warna avatar & label per kartu */
    .tone-1 .testi-av { background: linear-gradient(135deg, #10B981, #34D399) }
    .tone-1 .testi-tag { color: #047857; background: var(--green-bg) }
    .tone-2 .testi-av { background: linear-gradient(135deg, #F59E0B, #F97316) }
    .tone-2 .testi-tag { color: #B45309; background: #FEF6E4 }
    .tone-3 .testi-av { background: linear-gradient(135deg, #6366F1, #0EA5E9) }
    .tone-3 .testi-tag { color: #4338CA; background: #EEF2FF }
    .testi-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px }
    .testi-stars { display: flex; gap: 2px }
    .testi-stars svg { width: 15px; height: 15px; fill: #F59E0B }
    .testi-q { position: absolute; right: 14px; bottom: 58px; width: 64px; height: 64px; color: var(--blue-bg); opacity: .7; pointer-events: none }
    .testi-card:hover .testi-q { color: var(--blue-border) }
    .testi-top, .testi-text, .testi-author { position: relative }
    .testi-text { margin: 0 0 18px; font-size: 14.5px; color: var(--fg); line-height: 1.65; flex: 1 }
    .testi-author { display: flex; align-items: center; gap: 11px; padding-top: 16px; border-top: 1px solid var(--border) }
    .testi-av { width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg,var(--blue),var(--green)); display: flex; align-items: center; justify-content: center; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; font-weight: 800; color: #fff; flex-shrink: 0 }
    .testi-name { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; font-weight: 700; color: var(--fg) }
    .testi-role { font-size: 12.5px; color: var(--fg3) }

    /* ─── FAQ ─── */
    .faq-bg { background: linear-gradient(180deg, var(--white) 0%, #F4F9FE 100%) }
    .faq-layout { display: grid; grid-template-columns: 340px 1fr; gap: 56px; align-items: start }
    .faq-side { position: sticky; top: 110px }
    .faq-side .sec-title { text-align: left; margin-bottom: 14px }
    .faq-lead { font-size: 15px; color: var(--fg2); line-height: 1.7; margin-bottom: 24px }
    .faq-cs { background: linear-gradient(135deg,#ECFDF5,#F5FFFB); border: 1px solid rgba(16,185,129,.25); border-radius: 20px; padding: 20px }
    .faq-cs-top { display: flex; align-items: center; gap: 12px; margin-bottom: 16px }
    .faq-cs-top b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 800; color: var(--fg) }
    .faq-cs-top span:not(.cs-ic) { font-size: 13px; color: var(--fg2) }
    .faq-list { display: flex; flex-direction: column; gap: 12px }
    .faq-item {
        background: var(--white); border: 1px solid var(--border); border-radius: 18px; overflow: hidden;
        box-shadow: 0 1px 2px rgba(11,21,38,.04); transition: border-color .25s, box-shadow .25s, background .25s;
    }
    .faq-item:hover { border-color: #D3DCE8 }
    .faq-item.open { border-color: var(--blue-border); background: linear-gradient(180deg, #F5FBFF, var(--white) 60%); box-shadow: 0 18px 36px -22px rgba(14,165,233,.45) }
    .faq-q { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 22px; font-size: 15.5px; font-weight: 700; text-align: left; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -.01em; color: var(--fg) }
    .faq-q:focus-visible { outline: 2px solid var(--blue); outline-offset: -2px; border-radius: 18px }
    .faq-ic { width: 30px; height: 30px; border-radius: 10px; background: var(--bg); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: transform .3s, background .3s, color .3s, border-color .3s; color: var(--fg2) }
    .faq-ic svg { width: 14px; height: 14px }
    .faq-item.open .faq-ic { background: linear-gradient(135deg,var(--blue),var(--green)); border-color: transparent; color: #fff; transform: rotate(45deg) }
    .faq-a { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .35s cubic-bezier(.16,1,.3,1) }
    .faq-a > p { overflow: hidden; padding: 0 22px; font-size: 14.5px; color: var(--fg2); line-height: 1.75; opacity: 0; transition: opacity .25s, padding .35s }
    .faq-item.open .faq-a { grid-template-rows: 1fr }
    .faq-item.open .faq-a > p { opacity: 1; padding-bottom: 20px }

    /* ─── CTA ─── */
    .cta-sec { padding: 40px 0 96px }
    .cta-box {
        position: relative; overflow: hidden; border-radius: 28px; padding: 64px 56px;
        display: grid; grid-template-columns: 1.25fr 1fr; gap: 40px; align-items: center;
        background: linear-gradient(125deg, #0284C7 0%, #0EA5E9 45%, #10B981 100%);
        box-shadow: 0 40px 80px -40px rgba(14,165,233,.6);
    }
    .cta-box::before {
        content: ''; position: absolute; inset: 0; pointer-events: none;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px); background-size: 22px 22px;
        -webkit-mask-image: linear-gradient(90deg, #000, transparent 70%); mask-image: linear-gradient(90deg, #000, transparent 70%);
    }
    .cta-box::after {
        content: ''; position: absolute; width: 520px; height: 520px; right: -140px; top: -180px; border-radius: 50%; pointer-events: none;
        background: radial-gradient(circle, rgba(255,255,255,.28), transparent 65%);
        animation: hero-drift 12s ease-in-out infinite alternate;
    }
    .cta-text, .cta-visual { position: relative; z-index: 1 }
    .cta-kicker i { display: inline-flex; width: 18px; height: 18px; margin-right: 7px; vertical-align: -4px } .cta-kicker i svg { width: 16px; height: 16px; fill: #FDE68A; stroke: #FDE68A }
    .cta-kicker { display: inline-block; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #fff; background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); padding: 5px 12px; border-radius: 999px; margin-bottom: 18px }
    .cta-box h2 { font-size: clamp(28px,3.8vw,44px); font-weight: 900; color: #fff; letter-spacing: -.04em; line-height: 1.06; margin-bottom: 14px }
    .cta-box p { font-size: 16.5px; color: rgba(255,255,255,.88); line-height: 1.65; max-width: 460px; margin-bottom: 28px }
    .cta-btns { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px }
    .btn-white { display: inline-flex; align-items: center; gap: 10px; background: #fff; color: var(--fg); font-size: 15px; font-weight: 800; padding: 15px 26px; border-radius: 999px; transition: transform .15s, box-shadow .15s; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 12px 28px -12px rgba(11,21,38,.45) }
    .btn-white .shp { background: #FFF4ED }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 16px 32px -12px rgba(11,21,38,.55) }
    .btn-ghost { display: inline-flex; align-items: center; color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; padding: 15px 24px; border-radius: 999px; border: 1.5px solid rgba(255,255,255,.55); transition: background .15s, border-color .15s }
    .btn-ghost:hover { background: rgba(255,255,255,.14); border-color: #fff }
    .cta-notes { list-style: none; display: flex; flex-wrap: wrap; gap: 8px 18px }
    .cta-notes li { display: flex; align-items: center; gap: 7px; font-size: 13.5px; font-weight: 600; color: #fff }
    .cta-notes svg { width: 18px; height: 18px; padding: 3px; border-radius: 50%; background: rgba(255,255,255,.2) }
    .cta-visual { display: flex; justify-content: center }
    .cta-visual img { width: 100%; max-width: 340px; border-radius: 18px; transform: rotate(4deg); box-shadow: 0 30px 60px -20px rgba(11,21,38,.5); border: 4px solid rgba(255,255,255,.5); transition: transform .4s }
    .cta-box:hover .cta-visual img { transform: rotate(0) scale(1.02) }
    @media (prefers-reduced-motion: reduce) { .cta-box::after { animation: none } }

    /* ─── FOOTER ─── */
    .foot { position: relative; background: linear-gradient(180deg, #F4F9FE, var(--white)); padding: 64px 0 28px; border-top: 1px solid var(--border) }
    .foot::before { content: ''; position: absolute; top: -1px; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, var(--blue), var(--green), transparent); opacity: .6 }
    .foot-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 40px }
    .foot-brand { display: inline-flex; align-items: center; gap: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 19px; color: var(--fg); letter-spacing: -.03em }
    .foot-brand img { width: 30px; height: 30px }
    .foot-about p { font-size: 14px; color: var(--fg2); line-height: 1.7; max-width: 320px; margin: 14px 0 20px }
    .foot-col { display: flex; flex-direction: column; gap: 11px }
    .foot-col h4 { font-size: 12px; font-weight: 700; color: var(--fg); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 4px; font-family: 'Inter', sans-serif }
    .foot-col a, .foot-col span { font-size: 14px; color: var(--fg2); width: fit-content }
    .foot-col a { transition: color .15s }
    .foot-col a:hover { color: var(--blue) }
    .foot-bottom { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-top: 48px; padding-top: 22px; border-top: 1px solid var(--border); font-size: 13px; color: var(--fg3) }
    .foot-badge { display: inline-flex; align-items: center; gap: 7px; color: var(--fg2); font-weight: 600 }
    .foot-badge svg { width: 20px; height: 20px; padding: 3px; border-radius: 50%; color: #fff; background: linear-gradient(135deg,var(--blue),var(--green)) }

    /* ─── WA FLOAT ─── */

    /* ─── REVEAL ─── */
    @keyframes up { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:none } }
    .hi   { animation: up .65s ease both }
    .hi-1 { animation-delay: .05s } .hi-2 { animation-delay: .13s } .hi-3 { animation-delay: .21s } .hi-4 { animation-delay: .29s }
    .rv { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease }
    .rv.vis { opacity: 1; transform: none }
    .rv-1 { transition-delay: .08s } .rv-2 { transition-delay: .16s } .rv-3 { transition-delay: .24s }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 900px) {
        .nav-links { display: none }
        .nav-toggle { display: block }
        .feat-grid { grid-template-columns: repeat(2,1fr) }
        .order-layout { grid-template-columns: 1fr }
        .faq-layout { grid-template-columns: 1fr; gap: 32px }
        .faq-side { position: static }
        .foot-grid { grid-template-columns: repeat(3,1fr) }
        .foot-about { grid-column: 1 / -1 }
        .split { grid-template-columns: 1fr; gap: 36px }
        .showcase { padding: 28px 18px 20px; border-radius: 22px }
        .split.rev .split-img { order: unset }
        /* Cara Kerja HP/tablet: timeline vertikal, nomor kiri + garis penyambung */
        .how-grid { grid-template-columns: 1fr; gap: 12px; max-width: 560px; margin: 0 auto }
        .how-step { display: grid; grid-template-columns: 44px 1fr; column-gap: 14px; padding: 18px 18px 24px; border-radius: 20px; overflow: visible }
        .how-top { display: contents }
        .how-num { grid-row: 1 / span 3; grid-column: 1; position: relative; z-index: 1 }
        .how-tag { grid-column: 2; font-size: 10.5px; margin: 2px 0 4px }
        .how-step h3 { grid-column: 2; font-size: 16px; margin-bottom: 6px }
        .how-step p { grid-column: 2; font-size: 14px; line-height: 1.65 }
        .how-step:not(:last-child)::after {
            content: ''; position: absolute; left: 39px; top: 66px; bottom: -32px; width: 2px; border-radius: 2px;
            background: linear-gradient(var(--blue), var(--green)); opacity: .3;
        }
        .how-bar { left: 76px; right: 18px; bottom: 12px; border-radius: 3px; overflow: hidden }
        /* HP: satu card per aspek, marketplace vs Provecho ditumpuk */
        .cmp::before, .cmp::after, .cmp-head { display: none }
        .cmp-row { display: flex; flex-direction: column; gap: 8px; background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 16px; margin-bottom: 10px; box-shadow: 0 1px 2px rgba(11,21,38,.04) }
        .cmp-aspek { border: 0; padding: 0 2px 2px; font-size: 11.5px; text-transform: uppercase; letter-spacing: .1em; color: var(--fg3) }
        .cmp-cell { border: 0; padding: 10px 12px; font-size: 13.5px; align-items: flex-start; border-radius: 14px; gap: 11px }
        .cmp-cell i { margin-top: 1px }
        .cmp-cell.plain { background: var(--bg) }
        .cmp-cell.prov { background: linear-gradient(120deg,#EFF8FF,#F0FDF7); border: 1px solid var(--blue-border) }
        .cmp-who { display: block; font-size: 11px; font-weight: 700; letter-spacing: .02em; color: var(--fg3); margin-bottom: 2px }
        .cmp-cell.prov .cmp-who { color: var(--blue2) }
        .cmp-foot { display: block; margin-top: 6px }
        .cmp-cta-wrap { background: none; padding: 0 }
        .cmp-cta { padding: 15px }
        .stats-grid { grid-template-columns: repeat(2,1fr) }
        .stat:nth-child(2)::after { display: none }
    }
    @media (max-width: 640px) {
        .cta-long { display: none }
        .cta-short { display: inline }
        .nav { left: 10px; right: 10px }
        .nav-inner { height: 56px; padding: 0 6px 0 14px }
        .nav-cta { padding: 10px 14px 10px 16px; font-size: 13.5px; gap: 6px }
        .hero { padding: 116px 0 56px }
        .hero-sub { font-size: 16px }
        .pill { font-size: 11.5px; gap: 8px }
        .pill b { white-space: nowrap; font-size: 10.5px; padding: 4px 8px }
        .sec { padding: 60px 0 }
        .cta-box { padding: 40px 22px; grid-template-columns: 1fr; border-radius: 22px }
        /* CTA HP: foto kecil di atas, teks rata tengah, tombol selebar penuh */
        .cta-sec { padding: 24px 0 64px }
        .cta-box { gap: 20px; text-align: center }
        .cta-visual { order: -1 }
        .cta-visual img { max-width: 170px; border-width: 3px; transform: rotate(-4deg) }
        .cta-kicker { font-size: 11px; margin-bottom: 14px }
        .cta-box h2 { font-size: 27px }
        .cta-box p { font-size: 15px; margin: 0 auto 22px }
        .cta-btns { display: grid; gap: 10px; margin-bottom: 20px }
        .btn-white, .btn-ghost { justify-content: center; width: 100%; padding: 15px }
        .cta-notes { justify-content: center; gap: 8px 14px }
        .cta-notes li { font-size: 12.5px }
        .order-layout, .order-side { gap: 12px }
        .order-layout > * { min-width: 0 }
        .buy-card, .after-card, .cs-card { padding: 20px; border-radius: 20px }
        .buy-top { gap: 12px; padding-bottom: 16px; margin-bottom: 16px }
        .buy-logo { width: 48px; height: 48px; border-radius: 14px }
        .buy-logo img { height: 36px }
        .buy-top h3 { font-size: 16px }
        .buy-top p { font-size: 12.5px }
        /* isi paket: 2x2 tile kecil */
        .buy-list { grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px }
        .buy-list li { flex-direction: column; gap: 8px; background: var(--bg); border-radius: 14px; padding: 12px }
        .buy-list i { width: 30px; height: 30px; border-radius: 9px }
        .buy-list i svg { width: 15px; height: 15px }
        .buy-list b { font-size: 13px }
        .buy-list span { font-size: 12px }
        .warranty { padding: 14px; gap: 12px; margin-bottom: 14px }
        .warranty-ic { width: 38px; height: 38px }
        .warranty b { font-size: 14.5px }
        .warranty span:not(.warranty-ic) { font-size: 12.5px }
        /* label Shopee: satu baris, bisa digeser */
        .buy-chips { flex-wrap: nowrap; overflow-x: auto; margin: 0 -20px 16px; padding: 0 20px; scrollbar-width: none }
        .buy-chips::-webkit-scrollbar { display: none }
        .buy-chips span { white-space: nowrap }
        .buy-btn { padding: 15px }
        .after-steps li { padding: 6px 0 }
        .cs-top { gap: 12px; margin-bottom: 14px }
        .cs-ic { width: 36px; height: 36px; border-radius: 11px }
        .cs-top b { font-size: 14.5px }
        .cs-top p { font-size: 12.5px }
        .feat-grid { grid-template-columns: 1fr }
        .feat-grid { gap: 12px }
        .feat { padding: 20px; border-radius: 20px }
        .feat:hover { transform: none }
        .feat h3 { font-size: 16.5px }
        /* tile utama: visual di atas, teks di bawah */
        .feat.big { grid-column: auto; grid-template-columns: 1fr; gap: 18px }
        .feat.big .feat-vis { order: -1; height: 170px }
        .feat.big .feat-icon, .feat.tall .feat-icon { display: none }
        .feat.tall .feat-vis { height: 150px; margin-bottom: 18px }
        /* tile kecil: baris ringkas, ikon kiri */
        .feat:not(.big):not(.tall):not(.wide) { display: grid; grid-template-columns: 44px 1fr; column-gap: 14px; align-items: start; padding: 18px }
        .feat:not(.big):not(.tall):not(.wide) .feat-icon { margin: 0; grid-row: span 2 }
        .feat:not(.big):not(.tall):not(.wide) h3 { margin-bottom: 4px; font-size: 15.5px }
        .feat:not(.big):not(.tall):not(.wide) p { font-size: 13.5px; line-height: 1.6 }
        /* banner CS: ikon + judul sejajar, tombol selebar card */
        .feat.wide { display: grid; grid-template-columns: 48px 1fr; gap: 14px; align-items: center }
        .feat.wide .feat-icon { width: 48px; height: 48px }
        .feat.wide h3 { margin-bottom: 4px }
        /* Hero HP: tombol selebar penuh */
        .biz-label { font-size: 11px; margin-bottom: 14px }
        .biz-label::before, .biz-label::after { width: 24px }
        .biz-rows { gap: 8px; -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent); mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent) }
        .biz-track { gap: 8px; padding-right: 8px; animation-duration: 50s }
        .biz-row.rev .biz-track { animation-duration: 58s }
        .biz-pill { font-size: 14px; padding: 6px 14px 6px 6px; gap: 8px }
        .biz-pill i { width: 28px; height: 28px }
        .biz-pill i svg { width: 14px; height: 14px }
        .hero h1 { font-size: 38px; margin-bottom: 16px }
        .hero-sub { font-size: 15.5px; margin-bottom: 26px }
        .hero-btns { display: grid; justify-content: stretch; gap: 10px; max-width: 340px; margin: 0 auto 20px }
        .btn-primary, .btn-secondary { justify-content: center; width: 100%; padding: 15px }
        .hero-note { gap: 8px 14px; margin-bottom: 36px; font-size: 12.5px }

        /* Desain Card HP: foto + label di atas, teks rata tengah, spesifikasi 2x2 */
        .showcase .split { gap: 24px }
        .split-img { order: -1; flex-wrap: wrap; justify-content: center; gap: 8px; padding: 6px 0 0 }
        .split-img::before { inset: 4% 12% 30% }
        .split-img img { order: -1; max-width: 250px; transform: rotate(-2deg); margin: 0 calc(50% - 125px) 10px }
        .split-img:hover img { transform: rotate(-2deg) }
        .float-chip { position: static; animation: none; padding: 6px 12px 6px 6px; font-size: 12.5px; border-radius: 999px; box-shadow: 0 6px 16px -10px rgba(11,21,38,.3) }
        .float-chip i { width: 24px; height: 24px; border-radius: 50% }
        .float-chip small { display: none }
        .split-text { text-align: center }
        .split-text h2 { font-size: 26px }
        .split-text > p { font-size: 14.5px; margin: 0 auto 20px }
        .spec-grid { grid-template-columns: 1fr 1fr; gap: 8px; text-align: left }
        .spec { flex-direction: column; gap: 8px; padding: 12px; border-radius: 14px }
        .spec:hover { transform: none; box-shadow: none }
        .spec-ic { width: 30px; height: 30px; border-radius: 9px }
        .spec-ic svg { width: 15px; height: 15px }
        .spec b { font-size: 13px }
        .spec div span { font-size: 12px }

        /* FAQ HP: judul → pertanyaan → kotak CS (CS pindah ke bawah) */
        .faq-layout { display: flex; flex-direction: column; gap: 0 }
        .faq-side { display: contents }
        .faq-side .sec-label { align-self: center }
        .faq-side .sec-title { text-align: center }
        .faq-lead { text-align: center; font-size: 14.5px; margin-bottom: 28px }
        .faq-list { order: 1; gap: 10px }
        .faq-cs { order: 2; margin-top: 16px; border-radius: 20px; width: 100%; align-self: stretch }
        .faq-cs .cs-btn { padding: 13px }
        .faq-item { border-radius: 16px }
        .faq-q { padding: 16px 16px 16px 18px; font-size: 15px; gap: 12px }
        .faq-q:focus-visible { border-radius: 16px }
        .faq-ic { width: 28px; height: 28px; border-radius: 9px }
        .faq-a > p { padding: 0 18px; font-size: 14px; line-height: 1.7 }
        .faq-item.open .faq-a > p { padding-bottom: 16px }

        /* HP: satu baris saja, card lebih ringkas, tahan jari untuk berhenti */
        .marquee { -webkit-mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent); mask-image: linear-gradient(90deg, transparent, #000 6%, #000 94%, transparent) }
        .marquee-row.rev { display: none }
        .marquee-track { gap: 12px; padding-right: 12px }
        .testi-card { width: 286px; padding: 18px 18px 16px; border-radius: 20px }
        .testi-card:hover { transform: none }
        .testi-top { margin-bottom: 10px }
        .testi-text { font-size: 14px; margin-bottom: 14px }
        .testi-author { padding-top: 12px }
        .testi-av { width: 34px; height: 34px; border-radius: 10px; font-size: 12px }
        .testi-name { font-size: 13.5px }
        .testi-role { font-size: 12px }
        /* footer HP: brand rata tengah, Navigasi + Bantuan berdampingan, Produk disembunyikan (sudah dijelaskan di atas) */
        .foot { padding: 48px 0 32px }
        .foot-grid { grid-template-columns: 1fr 1fr; gap: 28px 16px }
        .foot-about { grid-column: 1 / -1; text-align: center; padding-bottom: 28px; border-bottom: 1px solid var(--border) }
        .foot-about p { margin: 12px auto 18px; font-size: 13.5px }
        .foot-prod { display: none }
        .foot-col { gap: 12px }
        .foot-col h4 { font-size: 11.5px }
        .foot-col a { font-size: 14px }
        .foot-bottom { flex-direction: column-reverse; align-items: center; text-align: center; gap: 10px; margin-top: 32px; font-size: 12.5px }
    }
    </style>
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav" x-data="{open:false}" :class="{open:open}" @click.outside="open=false" @keydown.escape.window="open=false">
    <div class="nav-inner">
        <a href="#" class="nav-logo">
            <img src="/img/logo.png" alt="Provecho">
            Provecho
        </a>
        <div class="nav-links">
            <span class="nav-ind" aria-hidden="true"></span>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#fitur">Fitur</a>
            <a href="#kenapa">Perbandingan</a>
            <a href="#harga">Pemesanan</a>
            <a href="#faq">FAQ</a>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <a href="{{ route('produk') }}" class="nav-cta">
                <span class="cta-long">Pesan Sekarang</span><span class="cta-short">Pesan</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <button type="button" class="nav-toggle" @click="open=!open" :aria-expanded="open" aria-controls="nav-panel" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </div>
    <div class="nav-panel" id="nav-panel" x-show="open" x-cloak
         x-transition:enter="np-enter" x-transition:enter-start="np-from" x-transition:enter-end="np-to"
         x-transition:leave="np-leave" x-transition:leave-start="np-to" x-transition:leave-end="np-from">
        <div class="np-links">
            <a href="#cara-kerja" @click="open=false"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h10M4 18h6"/></svg></i>Cara Kerja<b>›</b></a>
            <a href="#fitur" @click="open=false"><i class="g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg></i>Fitur<b>›</b></a>
            <a href="#kenapa" @click="open=false"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 3v18M5 8l-3 6h6zM19 8l-3 6h6zM5 8h14"/></svg></i>Perbandingan<b>›</b></a>
            <a href="#harga" @click="open=false"><i class="g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 7h12l-1 13H7z"/><path d="M9 7a3 3 0 016 0"/></svg></i>Pemesanan<b>›</b></a>
            <a href="#faq" @click="open=false"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 015 .5c0 1.5-2.5 2-2.5 3.5M12 17h.01"/></svg></i>FAQ<b>›</b></a>
        </div>
        <div class="np-actions">
            <a href="{{ route('produk') }}" class="np-buy">Pesan sekarang</a>
        </div>
        <div class="np-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            Garansi seumur hidup · Datang siap pakai
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="w">
        <div class="pill hi">
            <b><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>NFC + QR</b>
            Card ulasan Google untuk usaha kamu
        </div>
        <h1 class="hi hi-1">
            Pelanggan tap HP.<br>
            <span class="accent">Tinggal kasih bintang.</span>
        </h1>
        <p class="hero-sub hi hi-2">
            Card akrilik premium dengan NFC dan QR Code yang langsung membuka halaman ulasan Google usaha kamu, tanpa perlu dicari dulu di Maps.
        </p>
        <div class="hero-btns hi hi-3">
            <a href="{{ route('produk') }}" class="btn-primary">
                Pesan sekarang
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <a href="#cara-kerja" class="btn-secondary">
                Lihat cara kerjanya
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="hero-note hi hi-4">
            <span class="note-hl">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                Garansi seumur hidup
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                Tanpa biaya bulanan
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                Datang siap pakai
            </span>
        </div>
        @php
            $ic = [
                'kopi'   => 'M17 8h1a4 4 0 010 8h-1M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4zM6 2v3M10 2v3M14 2v3',
                'makan'  => 'M3 2v7a3 3 0 006 0V2M6 2v20M18 2a3 3 0 00-3 3v7h3v10',
                'gunting'=> 'M9 6a3 3 0 11-6 0 3 3 0 016 0zM9 18a3 3 0 11-6 0 3 3 0 016 0zM20 4L8.1 15.9M14.5 14.5L20 20M8.1 8.1L12 12',
                'medis'  => 'M9 3h6v6h6v6h-6v6H9v-6H3V9h6z',
                'kunci'  => 'M14.7 6.3a4 4 0 00-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.4-.6-.6-2.4z',
                'baju'   => 'M8 3L3 6l2 4 3-1v12h8V9l3 1 2-4-5-3a4 4 0 01-8 0z',
                'kasur'  => 'M3 18V6M3 14h18v4M21 14v-3a3 3 0 00-3-3h-8v6',
                'gym'    => 'M6 7v10M18 7v10M3 10v4M21 10v4M6 12h12',
                'tas'    => 'M6 7h12l-1 13H7zM9 7a3 3 0 016 0',
                'kamera' => 'M4 8h3l2-3h6l2 3h3v11H4zM12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z',
                'hewan'  => 'M12 13c-3 0-5 3-5 5s2 2 5 2 5 0 5-2-2-5-5-5zM5 10a1.5 1.5 0 103 0 1.5 1.5 0 10-3 0zM16 10a1.5 1.5 0 103 0 1.5 1.5 0 10-3 0zM8.5 6a1.5 1.5 0 103 0 1.5 1.5 0 10-3 0zM12.5 6a1.5 1.5 0 103 0 1.5 1.5 0 10-3 0z',
                'kilau'  => 'M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z',
                'mobil'  => 'M4 13l2-5h12l2 5v4H4zM6 17v2M18 17v2M7.5 14h.01M16.5 14h.01',
                'kerja'  => 'M3 7h18v13H3zM9 7V4h6v3',
                'lokasi' => 'M12 21s-7-6-7-11a7 7 0 0114 0c0 5-7 11-7 11zM12 12a2 2 0 100-4 2 2 0 000 4z',
                'kue'    => 'M4 21h16v-8H4zM4 16c2 1 4 1 6 0s4-1 6 0 3 1 4 0M12 13V9',
                'toko'   => 'M3 9l2-5h14l2 5M4 9v11h16V9M3 9h18M9 20v-6h6v6',
                'buku'   => 'M4 4h6a2 2 0 012 2v14a2 2 0 00-2-2H4zM20 4h-6a2 2 0 00-2 2v14a2 2 0 012-2h6z',
            ];
            $bizRows = [
                [['Cafe','kopi'],['Restoran','makan'],['Barbershop','gunting'],['Klinik','medis'],['Bengkel','kunci'],['Laundry','baju'],['Hotel','kasur'],['Gym','gym'],['Toko Kue','kue'],['Salon','kilau'],['Pet Shop','hewan'],['Studio Foto','kamera'],['Bimbel','buku']],
                [['Kedai Kopi','kopi'],['Warung Makan','makan'],['Apotek','medis'],['Butik','tas'],['Minimarket','toko'],['Homestay','kasur'],['Spa','kilau'],['Cuci Mobil','mobil'],['Tempat Wisata','lokasi'],['Coworking','kerja'],['Klinik Hewan','hewan'],['Toko Retail','toko'],['Nail Art','kilau']],
            ];
        @endphp
        <div class="biz hi hi-4">
            <div class="biz-label">Cocok untuk semua jenis usaha</div>
            <div class="biz-rows">
                @foreach($bizRows as $ri => $row)
                <div class="biz-row {{ $ri ? 'rev' : '' }}">
                    <div class="biz-track">
                        @foreach([false, true] as $dup)
                            @foreach($row as [$name, $key])
                            <span class="biz-pill" @if($dup) aria-hidden="true" @endif><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $ic[$key] }}"/></svg></i>{{ $name }}</span>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
</section>


<!-- CARA KERJA -->
<section class="sec" id="cara-kerja">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h5v-5h5v-5h5V5"/></svg></i>Cara Kerja</div>
            <h2 class="sec-title">Dari pesan sampai ulasan masuk, cuma <em class="grad">3 langkah.</em></h2>
            <p class="sec-sub">Kamu pesan di Shopee, kami hubungkan card ke halaman ulasan Google usaha kamu, lalu tinggal pajang di meja kasir. Pelanggan cukup tap HP atau scan QR.</p>
        </div>
        <div class="how-grid rv">
            <div class="how-step">
                <div class="how-top"><div class="how-num">1</div><span class="how-tag">Langkah 01</span></div>
                <h3>Pesan & Terima Card</h3>
                <p>Checkout di Shopee, pilih jumlah card. Card akrilik dikirim ke alamat kamu dalam 2–5 hari kerja.</p>
                <div class="how-bar"><span></span></div>
            </div>
            <div class="how-step">
                <div class="how-top"><div class="how-num">2</div><span class="how-tag">Langkah 02</span></div>
                <h3>Kami Aktifkan, Datang Siap Pakai</h3>
                <p>Tulis nama usaha kamu di catatan pesanan. Card kami hubungkan ke halaman ulasan Google usaha kamu sebelum dikirim.</p>
                <div class="how-bar"><span></span></div>
            </div>
            <div class="how-step">
                <div class="how-top"><div class="how-num">3</div><span class="how-tag">Langkah 03</span></div>
                <h3>Taruh & Biarkan Bekerja</h3>
                <p>Letakkan di meja kasir. Pelanggan tap NFC atau scan QR, halaman ulasan Google langsung terbuka. Ulasan masuk tiap hari.</p>
                <div class="how-bar"><span></span></div>
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="sec feat-bg" id="fitur">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/></svg></i>Fitur</div>
            <h2 class="sec-title">Semua yang dibutuhkan untuk <em class="grad">menambah ulasan Google.</em></h2>
            <p class="sec-sub">NFC untuk HP yang mendukung, QR Code untuk semua HP, stand akrilik untuk dipajang, dan CS kalau ada kendala. Semua termasuk dalam sekali beli, tanpa biaya bulanan.</p>
        </div>
        <div class="feat-grid rv">
            <div class="feat big">
                <div>
                    <div class="feat-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/><path d="M8.5 7C9.5 5.5 11 5 12 5s2.5.5 3.5 2"/></svg>
                    </div>
                    <h3>Tap sekali, halaman ulasan langsung terbuka</h3>
                    <p>Pelanggan cukup menempelkan HP ke card. Halaman ulasan Google toko kamu terbuka otomatis, tanpa buka aplikasi atau ketik nama toko.</p>
                    <div class="feat-chip"><span>Android</span><span>iPhone</span><span>Tanpa aplikasi</span></div>
                </div>
                <div class="feat-vis" aria-hidden="true">
                    <div class="nfc-card"><b>P</b><i>★★★★★</i></div>
                    <span class="nfc-wave"></span><span class="nfc-wave w2"></span>
                    <div class="nfc-phone"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></div>
                </div>
            </div>
            <div class="feat tall">
                <div class="feat-vis" aria-hidden="true">
                    <div class="qr-box">
                        <svg viewBox="0 0 21 21" fill="#0B1526" shape-rendering="crispEdges">
                            <path d="M0 0h7v7H0zM1 1v5h5V1zM2 2h3v3H2zM14 0h7v7h-7zM15 1v5h5V1zM16 2h3v3h-3zM0 14h7v7H0zM1 15v5h5v-5zM2 16h3v3H2z" fill-rule="evenodd"/>
                            <path d="M8 0h1v1H8zM10 1h2v1h-2zM8 3h2v2H8zM11 4h1v2h-1zM9 6h2v1H9zM12 8h2v1h-2zM8 8h2v2H8zM0 8h2v1H0zM3 9h3v1H3zM15 8h1v2h-1zM17 9h3v1h-3zM19 11h2v2h-2zM14 11h3v1h-3zM10 11h2v2h-2zM8 13h2v1H8zM12 14h1v3h-1zM9 16h2v1H9zM8 18h3v1H8zM14 14h2v2h-2zM17 15h2v1h-2zM15 17h1v2h-1zM18 18h3v1h-3zM12 19h2v2h-2zM16 20h2v1h-2zM1 11h2v2H1zM4 12h2v1H4z"/>
                        </svg>
                        <span class="qr-scan"></span>
                    </div>
                </div>
                <h3>QR Code untuk semua HP</h3>
                <p>HP tanpa NFC tetap bisa. Cukup scan QR di card yang sama dengan kamera.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <h3>Ganti Lokasi Tanpa Beli Baru</h3>
                <p>Pindah lokasi atau ganti profil Google? Kirim link Google Maps yang baru lewat chat Shopee atau WhatsApp, kami ubah tujuannya. Card yang sama tetap dipakai.</p>
            </div>
            <div class="feat">
                <div class="feat-icon g">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Akrilik Premium + Free Stand</h3>
                <p>Card akrilik premium dengan stand akrilik gratis, jadi bisa langsung berdiri di meja kasir. Rapi dipajang dan mudah dilap kalau kotor.</p>
            </div>
            <div class="feat">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <h3>Datang Siap Pakai</h3>
                <p>Card kami aktifkan sebelum dikirim, jadi begitu sampai tinggal dipajang di meja kasir. Tidak perlu setup apa pun.</p>
            </div>
            <div class="feat wide">
                <div class="feat-icon g">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <div>
                    <h3>Support WhatsApp langsung dari tim kami</h3>
                    <p>Ada pertanyaan atau kendala? Chat langsung ke WhatsApp. Dibalas oleh orang, bukan bot, dan gratis untuk semua pembeli.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCT SHOWCASE -->
<section class="sec">
    <div class="w">
        <div class="showcase rv">
        <div class="split">
            <div class="split-text">
                <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18v14H3zM7 15h4"/></svg></i>Desain Card</div>
                <h2>Card yang langsung <em>dimengerti pelanggan.</em></h2>
                <p>Petunjuk "Tap" dan "Scan" serta logo Google tercetak di card, jadi pelanggan tahu harus apa tanpa dijelaskan kasir. HP dengan NFC cukup ditempel, HP lain tinggal scan QR.</p>
                <div class="spec-grid">
                    <div class="spec">
                        <span class="spec-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg></span>
                        <div><b>Instruksi jelas</b><span>"Tap Your Phone" dan "Scan QR" tercetak di card</span></div>
                    </div>
                    <div class="spec">
                        <span class="spec-ic g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></span>
                        <div><b>Branding Google</b><span>Pelanggan langsung kenal dan percaya</span></div>
                    </div>
                    <div class="spec">
                        <span class="spec-ic g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3M21 14v7h-7"/></svg></span>
                        <div><b>QR tajam</b><span>Resolusi tinggi, tetap terbaca dari jauh</span></div>
                    </div>
                    <div class="spec">
                        <span class="spec-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.3 7L12 12l8.7-5M12 22V12"/></svg></span>
                        <div><b>Free stand akrilik</b><span>Card langsung berdiri, siap dipajang</span></div>
                    </div>
                </div>
            </div>
            <div class="split-img">
                <div class="float-chip c1">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 8.5a8 8 0 010 7M9.5 6a12 12 0 010 12M13 10a3 3 0 010 4"/></svg></i>
                    <div>NFC aktif<small>tap langsung terbuka</small></div>
                </div>
                <img src="/img/landing.jpg" alt="Desain Provecho Card dengan NFC dan QR Code">
                <div class="float-chip c2">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg></i>
                    <div>Semua HP<small>NFC atau scan QR</small></div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- COMPARISON -->
<section class="sec comp-bg" id="kenapa">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M5 8l-3 6h6zM19 8l-3 6h6zM5 8h14"/></svg></i>Perbandingan</div>
            <h2 class="sec-title">Sama-sama card NFC, <em class="grad">bedanya di layanan.</em></h2>
            <p class="sec-sub">Bahan dan fungsi dasarnya mirip dengan card di marketplace. Provecho menambahkan card yang datang siap pakai, ganti lokasi tanpa tulis ulang chip, dan garansi seumur hidup.</p>
        </div>
        @php
            $dot = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M6 12h12"/></svg>';
            $v   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
            // [aspek, marketplace, provecho, provecho unggul?]
            $rows = [
                ['NFC & QR',   'Umumnya sudah ada keduanya',                        'NFC dan QR Code dalam satu card',                     false],
                ['Bahan',      'Akrilik atau PVC, tergantung penjual',              'Akrilik premium + free stand akrilik',       true],
                ['Setup awal', 'Link diisi penjual, atau kamu tulis sendiri pakai aplikasi NFC', 'Diaktifkan sebelum dikirim, datang siap pakai', true],
                ['Ganti link', 'Tulis ulang chip pakai aplikasi NFC',               'Kirim link baru lewat chat Shopee atau WhatsApp, kami yang ubah', true],
                ['Desain',     'Bervariasi, sering tanpa petunjuk cara pakai',      'Petunjuk "Tap" dan "Scan" tercetak di card',          true],
                ['Bantuan',    'Tergantung penjual',                                'Dibantu lewat WhatsApp setelah beli',                 true],
                ['Garansi',    'Tergantung penjual',                                'Seumur hidup, card cacat produksi diganti baru',      true],
            ];
        @endphp
        <div class="cmp rv">
            <div class="cmp-head">
                <div></div>
                <div class="cmp-h plain">Card NFC marketplace</div>
                <div class="cmp-h prov">
                    <span class="cmp-badge">Rekomendasi</span>
                    <img src="/img/logo.png" alt="">Provecho Card
                </div>
            </div>
            @foreach($rows as [$aspek, $biasa, $prov, $unggul])
                <div class="cmp-row">
                    <div class="cmp-aspek">{{ $aspek }}</div>
                    <div class="cmp-cell plain"><i class="eq">{!! $dot !!}</i><span><small class="cmp-who">Card marketplace</small>{{ $biasa }}</span></div>
                    <div class="cmp-cell prov"><i class="{{ $unggul ? 'yes' : 'eq' }}">{!! $unggul ? $v : $dot !!}</i><span><small class="cmp-who">Provecho</small>{{ $prov }}@unless($unggul)<em class="cmp-same">sama</em>@endunless</span></div>
                </div>
            @endforeach
            <div class="cmp-foot">
                <div></div>
                <div></div>
                <div class="cmp-cta-wrap">
                    <a href="#harga" class="cmp-cta">Lihat cara pesan
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ORDER -->
<section class="sec order-bg" id="harga">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 7h12l-1 13H7zM9 7a3 3 0 016 0"/></svg></i>Pemesanan</div>
            <h2 class="sec-title">Pesan lewat Shopee, <em class="grad">datang siap pakai.</em></h2>
            <p class="sec-sub">Bayar dengan metode apa pun yang ada di Shopee dan dapat perlindungan pembeli. Tulis nama usaha kamu di catatan pesanan, kami aktifkan card sebelum dikirim.</p>
        </div>
        <div class="order-layout rv">
            <div class="buy-card">
                <div class="buy-top">
                    <span class="buy-logo"><img src="/img/shopee-seeklogo.png" alt="Shopee"></span>
                    <div>
                        <h3>Provecho Google Review Card</h3>
                        <p>Tersedia di toko resmi Provecho di Shopee</p>
                    </div>
                </div>
                <div class="buy-label">Isi paket</div>
                <ul class="buy-list">
                    <li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 15h4"/></svg></i><div><b>Card akrilik premium</b><span>NFC dan QR Code dalam satu card</span></div></li>
                    <li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 20h16M7 20l2-9h6l2 9"/></svg></i><div><b>Free stand akrilik</b><span>Card langsung berdiri di meja kasir</span></div></li>
                    <li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></i><div><b>Siap pakai</b><span>Kami aktifkan sebelum dikirim</span></div></li>
                    <li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></i><div><b>Bantuan CS</b><span>Lewat WhatsApp setelah beli</span></div></li>
                </ul>
                <div class="warranty">
                    <span class="warranty-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></span>
                    <div>
                        <b>Garansi seumur hidup</b>
                        <span>Card cacat produksi? Kami ganti dengan card baru, tanpa batas waktu.</span>
                    </div>
                </div>
                <div class="buy-chips">
                    <span>Perlindungan pembeli</span><span>Banyak metode bayar</span><span>Bisa pakai voucher</span>
                </div>
                <a href="{{ route('produk') }}" class="buy-btn">
                    Lihat produk &amp; pesan
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="order-side">
                <div class="after-card">
                    <div class="buy-label">Setelah checkout</div>
                    <ol class="after-steps">
                        <li><span>1</span><div><b>Pesanan diproses</b><small>Card diaktifkan sesuai nama usaha di catatan pesanan</small></div></li>
                        <li><span>2</span><div><b>Card dikirim</b><small>Sampai dalam 2–5 hari kerja</small></div></li>
                        <li><span>3</span><div><b>Pajang &amp; pakai</b><small>Card sudah aktif saat sampai, tinggal dipajang</small></div></li>
                    </ol>
                </div>
                <div class="cs-card">
                    <div class="cs-top">
                        <span class="cs-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></span>
                        <div>
                            <b>Masih ragu atau mau tanya dulu?</b>
                            <p>Soal order banyak, cara pakai, atau kendala setelah beli, tanya langsung ke CS kami.</p>
                        </div>
                    </div>
                    <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20tanya%20tentang%20Provecho%20Card" target="_blank" rel="noopener" class="cs-btn">Chat CS WhatsApp
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="sec testi-bg">
    <div class="w">
        <div class="sec-head ctr rv">
            <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></i>Testimoni</div>
            <h2 class="sec-title">Yang pemilik usaha <em class="grad">bilang.</em></h2>
            <p class="sec-sub">Cerita dari pemilik cafe, barbershop, restoran, dan toko yang memajang Provecho di meja kasirnya.</p>
        </div>
    </div>
            @php $testis = [
                ['t'=>'Sebelum punya card ini Google Review kami cuma 12. Sekarang sudah 40+ dalam sebulan. Pelanggan senang karena gampang banget.','n'=>'Kak Rina','r'=>'Pemilik Cafe Mungil','i'=>'KR','tag'=>'Cafe','hl'=>'Sekarang sudah 40+ dalam sebulan'],
                ['t'=>'Simple banget. Taruh di kasir, pelanggan tap sendiri. Tidak perlu minta-minta lagi. Review masuk terus tiap hari.','n'=>'Pak Budi','r'=>'Barbershop Budi & Bros','i'=>'PB','tag'=>'Barbershop','hl'=>'Tidak perlu minta-minta lagi'],
                ['t'=>'Awalnya ragu, tapi ternyata gampang. Card datang sudah aktif, tinggal dipajang. Worth it banget untuk harganya.','n'=>'Mbak Sari','r'=>'Toko Oleh-Oleh Sari','i'=>'MS','tag'=>'Toko Oleh-oleh','hl'=>'Card datang sudah aktif'],
                ['t'=>'Udah order 3 card untuk 3 cabang. Semua jalan lancar. Pelanggan tidak perlu cari nama toko dulu di Google.','n'=>'Mas Doni','r'=>'Warung Makan Doni Jaya','i'=>'MD','tag'=>'Warung Makan','hl'=>'3 card untuk 3 cabang'],
                ['t'=>'Card-nya kelihatan premium. Pelanggan yang lihat pasti nanya ini apaan — jadi conversation starter juga.','n'=>'Kak Fara','r'=>'Boutique Fara Collection','i'=>'KF','tag'=>'Butik','hl'=>'kelihatan premium'],
                ['t'=>'Google Review restoran kami naik drastis. Pelanggan lebih mau review karena tidak perlu cari nama toko sendiri.','n'=>'Chef Andi','r'=>'Restoran Andi Masak','i'=>'CA','tag'=>'Restoran','hl'=>'naik drastis'],
            ]; @endphp
            @php
                $rowsT = [$testis, array_reverse($testis)];
                $star = "<svg viewBox=\"0 0 24 24\"><path d=\"M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z\"/></svg>";
            @endphp
        <div class="marquee rv">
            @foreach($rowsT as $ri => $list)
            <div class="marquee-row {{ $ri ? "rev" : "" }}">
                <div class="marquee-track">
                    {{-- set kedua = duplikat untuk loop mulus --}}
                    @foreach([false, true] as $dup)
                        @foreach($list as $ti => $t)
                        <figure class="testi-card tone-{{ ($ri * 2 + $ti) % 4 }}" @if($dup) aria-hidden="true" @endif>
                            <svg class="testi-q" viewBox="0 0 24 24" fill="currentColor"><path d="M9.5 6C6.5 6 4 8.5 4 11.5V18h6v-6H7c0-1.7 1.3-3 3-3V6h-.5zm10 0c-3 0-5.5 2.5-5.5 5.5V18h6v-6h-3c0-1.7 1.3-3 3-3V6h-.5z"/></svg>
                            <div class="testi-top">
                                <div class="testi-stars">{!! str_repeat($star, 5) !!}</div>
                                <span class="testi-tag">{{ $t["tag"] }}</span>
                            </div>
                            <blockquote class="testi-text">{!! str_replace(e($t["hl"]), '<mark>'.e($t["hl"]).'</mark>', e($t["t"])) !!}</blockquote>
                            <figcaption class="testi-author">
                                <div class="testi-av">{{ $t["i"] }}</div>
                                <div>
                                    <div class="testi-name">{{ $t["n"] }}</div>
                                    <div class="testi-role">{{ $t["r"] }}</div>
                                </div>
                            </figcaption>
                        </figure>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
</section>

<!-- FAQ -->
<section class="sec faq-bg" id="faq">
    <div class="w">
        <div class="faq-layout">
            <div class="faq-side rv">
                <div class="sec-label"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21a9 9 0 100-18 9 9 0 000 18zM9.5 9.5a2.5 2.5 0 015 .5c0 1.5-2.5 2-2.5 3.5M12 17h.01"/></svg></i>FAQ</div>
                <h2 class="sec-title">Pertanyaan yang <em class="grad">sering ditanya.</em></h2>
                <p class="faq-lead">Soal cara kerja card, pengiriman, ganti lokasi, sampai garansi. Belum terjawab? Tanya langsung ke CS kami lewat WhatsApp.</p>
                <div class="faq-cs">
                    <div class="faq-cs-top">
                        <span class="cs-ic"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></span>
                        <div>
                            <b>Masih ada pertanyaan?</b>
                            <span>CS kami balas lewat WhatsApp.</span>
                        </div>
                    </div>
                    <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20tanya%20tentang%20Provecho%20Card" target="_blank" rel="noopener" class="cs-btn">Chat CS WhatsApp
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
            <div class="faq-list" x-data="{open:0}">
            @php $faqs = [
                ['q'=>'HP pelanggan harus punya aplikasi khusus?','a'=>'Tidak perlu aplikasi apa pun. NFC sudah tersedia di hampir semua HP Android dan iPhone terbaru — cukup dekatkan ke card, Google Review langsung terbuka di browser.'],
                ['q'=>'Bagaimana cara aktivasinya?','a'=>'Tidak perlu aktivasi sendiri. Tulis nama usaha kamu di catatan pesanan Shopee, kami hubungkan card ke halaman ulasan Google usaha kamu sebelum dikirim. Card datang siap pakai.'],
                ['q'=>'Kalau mau ganti link atau pindah lokasi, bagaimana?','a'=>'Kirim link Google Maps lokasi baru lewat chat Shopee atau WhatsApp CS kami. Kami ubah tujuannya, jadi card yang sama tetap bisa dipakai tanpa beli baru.'],
                ['q'=>'Berapa lama pengirimannya?','a'=>'2–5 hari kerja tergantung lokasi. Kami menggunakan ekspedisi terpercaya dengan resi yang bisa kamu pantau setelah card dikirim.'],
                ['q'=>'Bisa order banyak untuk beberapa cabang?','a'=>'Bisa. Checkout beberapa card sekaligus di Shopee, atau tanya CS kami lewat WhatsApp kalau butuh jumlah besar. Setiap card bisa diaktivasi ke usaha yang berbeda.'],
                ['q'=>'Ada garansi jika card tidak berfungsi?','a'=>'Ada, garansi seumur hidup tanpa batas waktu. Kalau card bermasalah karena cacat produksi, kami ganti dengan card baru. Cukup kirim foto card yang bermasalah lewat chat Shopee atau WhatsApp CS kami.'],
            ]; @endphp
            @foreach($faqs as $i => $f)
            <div class="faq-item rv" :class="{open:open==={{ $i }}}">
                <button type="button" class="faq-q" @click="open=open==={{ $i }}?null:{{ $i }}" :aria-expanded="open==={{ $i }}">
                    {{ $f['q'] }}
                    <span class="faq-ic">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </span>
                </button>
                <div class="faq-a"><p>{{ $f['a'] }}</p></div>
            </div>
            @endforeach
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-sec">
    <div class="w">
        <div class="cta-box rv">
            <div class="cta-text">
                <span class="cta-kicker"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg></i>Siap dapat lebih banyak ulasan?</span>
                <h2>Mulai kumpulkan Google Review hari ini.</h2>
                <p>Satu card, sekali bayar, bergaransi seumur hidup. Taruh di meja kasir dan biarkan pelanggan memberi ulasan sendiri.</p>
                <div class="cta-btns">
                    <a href="{{ route('produk') }}" class="btn-white">
                        Pesan sekarang
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <ul class="cta-notes">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>Garansi seumur hidup</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Datang siap pakai</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>Bantuan CS via WhatsApp</li>
                </ul>
            </div>
            <div class="cta-visual" aria-hidden="true">
                <img src="/img/landing.jpg" alt="">
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="foot">
    <div class="w">
        <div class="foot-grid">
            <div class="foot-about">
                <a href="#" class="foot-brand">
                    <img src="/img/logo.png" alt="">
                    Provecho
                </a>
                <p>Card NFC + QR Code yang membuka halaman ulasan Google usaha kamu. Datang siap pakai, bergaransi seumur hidup.</p>
            </div>
            <nav class="foot-col" aria-label="Navigasi">
                <h4>Navigasi</h4>
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#fitur">Fitur</a>
                <a href="#kenapa">Perbandingan</a>
                <a href="#harga">Pemesanan</a>
                <a href="#faq">FAQ</a>
            </nav>
            <div class="foot-col foot-prod">
                <h4>Produk</h4>
                <span>Card akrilik premium</span>
                <span>Free stand akrilik</span>
                <span>NFC + QR Code</span>
                <span>Datang siap pakai</span>
            </div>
            <div class="foot-col">
                <h4>Bantuan</h4>
                <a href="https://wa.me/6283842843671" target="_blank" rel="noopener">Chat CS WhatsApp</a>
                <a href="#faq">Ganti lokasi Google Maps</a>
                <a href="#faq">Klaim garansi</a>
                <a href="{{ route('produk') }}">Lihat produk</a>
            </div>
        </div>
        <div class="foot-bottom">
            <span>© {{ date('Y') }} Provecho. Hak cipta dilindungi.</span>
            <span class="foot-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                Garansi seumur hidup untuk cacat produksi
            </span>
        </div>
    </div>
</footer>

<script>
const io = new IntersectionObserver(es => {
    es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('vis'); io.unobserve(e.target) } })
}, { threshold: 0.06, rootMargin: '0px 0px -28px 0px' });
document.querySelectorAll('.rv').forEach(el => io.observe(el));
const nav = document.getElementById('nav');
// navbar: penanda section aktif + pill yang meluncur ke link aktif/disorot
const navLinks = [...document.querySelectorAll('.nav-links a')];
const navInd = document.querySelector('.nav-ind');
const navSecs = navLinks.map(a => document.querySelector(a.getAttribute('href')));
let activeLink = null;
const moveInd = a => {
    navInd.classList.toggle('on', !!a);
    if (!a) return;
    navInd.style.setProperty('--x', a.offsetLeft + 'px');
    navInd.style.width = a.offsetWidth + 'px';
};
navLinks.forEach(a => a.addEventListener('mouseenter', () => moveInd(a)));
document.querySelector('.nav-links').addEventListener('mouseleave', () => moveInd(activeLink));

const onScroll = () => {
    nav.classList.toggle('scrolled', scrollY > 12);
    const line = innerHeight * .35;
    let cur = null;
    navSecs.forEach((s, i) => { if (s && s.getBoundingClientRect().top <= line) cur = navLinks[i]; });
    if (cur && document.getElementById('faq').getBoundingClientRect().bottom < line) cur = null; // sudah lewat FAQ (CTA/footer)
    if (cur !== activeLink) {
        navLinks.forEach(a => a.classList.toggle('active', a === cur));
        activeLink = cur;
        if (!document.querySelector('.nav-links:hover')) moveInd(cur);
    }
};
addEventListener('scroll', onScroll, { passive: true }); onScroll();

// Testimoni: geser otomatis, tapi bisa di-swipe (HP), di-drag mouse, atau digeser trackpad (PC).
// Track berisi 2 set identik; posisi dibungkus di [0, setengah lebar] agar loop tanpa ujung.
const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
document.querySelectorAll('.marquee-row').forEach(row => {
    const track = row.querySelector('.marquee-track');
    const dir = row.classList.contains('rev') ? -1 : 1;
    const speed = 28; // px per detik
    let half = 0, pos = 0, last = 0, holdUntil = 0, hover = false, drag = null;

    const measure = () => { half = track.scrollWidth / 2; };
    const wrap = () => { if (half) { pos = ((pos % half) + half) % half; row.scrollLeft = pos; } };
    const hold = (ms = 1500) => { holdUntil = performance.now() + ms; };
    measure(); pos = dir < 0 ? half / 2 : 0; wrap();
    addEventListener('resize', () => { measure(); wrap(); });

    row.addEventListener('mouseenter', () => hover = true);
    row.addEventListener('mouseleave', () => hover = false);
    // swipe / trackpad / scroll bawaan: ikuti posisi user, jeda sebentar sebelum lanjut
    row.addEventListener('touchstart', () => hold(1e9), { passive: true });
    row.addEventListener('touchend', () => hold(), { passive: true });
    row.addEventListener('wheel', () => hold(), { passive: true });
    row.addEventListener('scroll', () => {
        if (Math.abs(row.scrollLeft - pos) > 1) { pos = row.scrollLeft; wrap(); }
    }, { passive: true });

    // drag pakai mouse
    row.addEventListener('pointerdown', e => {
        if (e.pointerType !== 'mouse' || e.button !== 0) return;
        drag = { x: e.clientX, start: pos, moved: false };
        row.setPointerCapture(e.pointerId);
        hold(1e9);
    });
    row.addEventListener('pointermove', e => {
        if (!drag) return;
        const dx = e.clientX - drag.x;
        if (Math.abs(dx) > 3) { drag.moved = true; row.classList.add('dragging'); }
        pos = drag.start - dx; wrap();
    });
    const endDrag = () => { if (!drag) return; drag = null; row.classList.remove('dragging'); hold(); };
    row.addEventListener('pointerup', endDrag);
    row.addEventListener('pointercancel', endDrag);

    const tick = t => {
        const dt = last ? Math.min((t - last) / 1000, .05) : 0; last = t;
        if (!reduceMotion && !hover && !drag && t > holdUntil) { pos += dir * speed * dt; wrap(); }
        requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
});
</script>
</body>
</html>
