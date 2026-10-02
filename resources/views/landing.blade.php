<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provecho — Google Review Card untuk UMKM</title>
    <meta name="description" content="Card NFC + QR Code akrilik yang langsung membuka halaman Google Review. Taruh di kasir, pelanggan tap, review masuk. Rp 50.000/card.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
    :root {
        --g50:#F0F9FF;--g100:#E0F2FE;--g200:#BAE6FD;--g300:#7DD3FC;
        --g400:#38BDF8;--g500:#0EA5E9;--g600:#0284C7;--g700:#0369A1;
        --g800:#075985;--g900:#0C4A6E;--g950:#082F49;
        --n50:#F9FAFB;--n100:#F3F4F6;--n200:#E5E7EB;--n300:#D1D5DB;
        --n400:#9CA3AF;--n500:#6B7280;--n600:#4B5563;--n700:#374151;
        --n800:#1F2937;--n900:#111827;
        color-scheme:light;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    [x-cloak]{display:none!important}
    html{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:var(--n900);
         -webkit-font-smoothing:antialiased;font-size:16px;line-height:1.6}
    body{background:#fff}
    img{max-width:100%;display:block}
    a{text-decoration:none;color:inherit}
    button{font:inherit;border:none;background:none;cursor:pointer}

    .ctnr{width:100%;max-width:1100px;margin:0 auto;padding:0 24px}

    /* ── Nav ── */
    .nav{position:fixed;top:14px;left:50%;transform:translateX(-50%);width:calc(100% - 28px);max-width:880px;
         background:rgba(8,47,73,.92);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
         border-radius:999px;padding:8px 8px 8px 22px;z-index:1000;display:flex;align-items:center;
         justify-content:space-between;transition:box-shadow .3s}
    .nav.scrolled{box-shadow:0 8px 40px rgba(0,0,0,.18)}
    .nav-logo{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;font-size:15px;letter-spacing:-.02em}
    .nav-logo img{width:28px;height:28px;border-radius:6px}
    .nav-links{display:flex;align-items:center;gap:4px}
    .nav-links a{color:rgba(255,255,255,.7);font-size:13.5px;font-weight:500;padding:7px 14px;border-radius:999px;
                 transition:color .2s,background .2s}
    .nav-links a:hover{color:#fff;background:rgba(255,255,255,.08)}
    .nav-cta{display:inline-flex;align-items:center;gap:6px;background:var(--g600);color:#fff;font-size:13.5px;
             font-weight:600;padding:9px 20px;border-radius:999px;transition:background .2s;white-space:nowrap}
    .nav-cta:hover{background:var(--g700)}
    .nav-toggle{display:none;color:#fff;padding:8px}

    /* ── Hero ── */
    .hero{padding:130px 0 60px;position:relative;overflow:hidden}
    .hero::before{content:'';position:absolute;top:-80px;right:-60px;width:480px;height:480px;
                  background:radial-gradient(circle,rgba(14,165,233,.10) 0%,transparent 70%);pointer-events:none}
    .hero-grid{display:grid;grid-template-columns:1fr 420px;gap:48px;align-items:center}
    .hero-badge{display:inline-flex;align-items:center;gap:7px;background:var(--g50);border:1px solid var(--g200);
                color:var(--g700);font-size:12.5px;font-weight:600;padding:5px 14px;border-radius:999px;margin-bottom:22px}
    .hero h1{font-size:clamp(34px,4.8vw,52px);font-weight:800;line-height:1.08;letter-spacing:-.035em;margin-bottom:18px}
    .hero h1 em{font-style:normal;color:var(--g600)}
    .hero-sub{font-size:16.5px;color:var(--n500);line-height:1.65;max-width:460px;margin-bottom:28px}
    .hero-price{display:inline-flex;align-items:baseline;gap:6px;background:var(--g950);color:#fff;
                padding:10px 22px;border-radius:12px;margin-bottom:28px;font-size:15px;font-weight:600}
    .hero-price b{font-size:28px;font-weight:800;letter-spacing:-.02em}
    .hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:28px}
    .btn-wa{display:inline-flex;align-items:center;gap:8px;background:var(--g600);color:#fff;font-size:15px;
            font-weight:600;padding:14px 28px;border-radius:12px;transition:background .2s,transform .1s}
    .btn-wa:hover{background:var(--g700)}
    .btn-wa:active{transform:scale(.98)}
    .btn-ghost{display:inline-flex;align-items:center;gap:8px;background:transparent;border:1.5px solid var(--n200);
               color:var(--n700);font-size:15px;font-weight:600;padding:13px 26px;border-radius:12px;
               transition:border-color .2s,background .2s}
    .btn-ghost:hover{border-color:var(--n300);background:var(--n50)}
    .hero-trust{display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:var(--n400);font-weight:500}
    .hero-trust span{display:flex;align-items:center;gap:5px}
    .hero-trust svg{width:15px;height:15px;color:var(--g500)}

    .hero-visual{position:relative;display:flex;align-items:center;justify-content:center}
    .card-float{position:relative}
    .card-float img{width:340px;height:340px;object-fit:contain;
                    filter:drop-shadow(0 24px 48px rgba(0,0,0,.12));
                    border-radius:18px;
                    transform:perspective(900px) rotateY(-5deg) rotateX(3deg);
                    transition:transform .5s ease}
    .card-float:hover img{transform:perspective(900px) rotateY(-1deg) rotateX(1deg)}
    .nfc-ring{position:absolute;top:50%;left:50%;width:100px;height:100px;transform:translate(-50%,-50%);pointer-events:none}
    .nfc-ring circle{fill:none;stroke:var(--g400);stroke-width:1.5;opacity:0;transform-origin:center;animation:nfc-p 2.4s ease-out infinite}
    .nfc-ring circle:nth-child(2){animation-delay:.4s}
    .nfc-ring circle:nth-child(3){animation-delay:.8s}
    @keyframes nfc-p{0%{r:8;opacity:.6}100%{r:48;opacity:0}}

    /* ── Strip ── */
    .strip{background:var(--g950);padding:20px 0}
    .strip-row{display:flex;justify-content:center;gap:40px;flex-wrap:wrap}
    .strip-item{display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.65);font-size:13.5px;font-weight:500}
    .strip-item svg{width:16px;height:16px;color:var(--g400)}

    /* ── Section base ── */
    .sec{padding:90px 0}
    .sec-label{display:inline-flex;align-items:center;gap:7px;font-size:12.5px;font-weight:700;color:var(--g600);
               text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px}
    .sec-title{font-size:clamp(26px,3.6vw,38px);font-weight:800;letter-spacing:-.03em;line-height:1.15;margin-bottom:14px}
    .sec-sub{font-size:16px;color:var(--n500);max-width:520px;line-height:1.6}
    .sec-head{margin-bottom:48px}
    .sec-head.ctr{text-align:center}
    .sec-head.ctr .sec-sub{margin:0 auto}

    /* ── Cara Kerja ── */
    .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .step{background:#fff;border:1.5px solid var(--n200);border-radius:16px;padding:28px 24px;position:relative;
          transition:border-color .25s,box-shadow .25s}
    .step:hover{border-color:var(--g300);box-shadow:0 8px 28px rgba(2,132,199,.07)}
    .step-num{width:40px;height:40px;background:var(--g50);border:1.5px solid var(--g200);border-radius:10px;
              display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:var(--g700);
              margin-bottom:16px}
    .step h3{font-size:16px;font-weight:700;margin-bottom:6px;letter-spacing:-.01em}
    .step p{font-size:14px;color:var(--n500);line-height:1.55}

    /* ── Kenapa Beda ── */
    .diff{background:var(--n50)}
    .diff-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:840px;margin:0 auto}
    .diff-card{border-radius:16px;padding:32px 28px}
    .diff-card.old{background:#fff;border:1px solid var(--n200)}
    .diff-card.prov{background:var(--g600);color:#fff}
    .diff-card h3{font-size:19px;font-weight:700;margin-bottom:4px}
    .diff-card .diff-sub{font-size:13px;opacity:.65;margin-bottom:22px}
    .diff-list{list-style:none;display:flex;flex-direction:column;gap:12px}
    .diff-list li{display:flex;align-items:flex-start;gap:9px;font-size:14px;line-height:1.45}
    .diff-list li svg{width:17px;height:17px;flex-shrink:0;margin-top:2px}
    .diff-card.old .diff-list li svg{color:#ef4444}
    .diff-card.prov .diff-list li svg{color:var(--g200)}
    .diff-price{margin-top:24px;padding-top:20px;font-size:13px;opacity:.7}
    .diff-card.old .diff-price{border-top:1px solid var(--n200);color:var(--n500)}
    .diff-card.prov .diff-price{border-top:1px solid rgba(255,255,255,.15)}
    .diff-card.prov .diff-price b{font-size:26px;font-weight:800;opacity:1;color:#fff;letter-spacing:-.02em}

    /* ── Features grid ── */
    .feat-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:48px}
    .feat{background:#fff;border:1.5px solid var(--n200);border-radius:16px;padding:28px 24px;
          transition:border-color .25s,box-shadow .25s}
    .feat:hover{border-color:var(--g300);box-shadow:0 6px 20px rgba(2,132,199,.06)}
    .feat-icon{width:42px;height:42px;background:var(--g50);border:1.5px solid var(--g200);border-radius:11px;
               display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:var(--g600)}
    .feat h3{font-size:16px;font-weight:700;margin-bottom:6px;letter-spacing:-.01em}
    .feat p{font-size:14px;color:var(--n500);line-height:1.55}

    /* ── Pricing ── */
    .price-wrap{max-width:440px;margin:0 auto}
    .price-card{background:#fff;border:2px solid var(--g300);border-radius:20px;padding:40px 32px;text-align:center;
                box-shadow:0 12px 40px rgba(2,132,199,.08)}
    .price-card h3{font-size:14px;font-weight:700;color:var(--g600);text-transform:uppercase;letter-spacing:.06em;
                   margin-bottom:16px}
    .price-amount{font-size:52px;font-weight:900;letter-spacing:-.04em;line-height:1;color:var(--n900)}
    .price-amount small{font-size:18px;font-weight:500;color:var(--n400)}
    .price-note{font-size:14px;color:var(--n500);margin-top:8px;margin-bottom:28px}
    .price-list{list-style:none;text-align:left;display:flex;flex-direction:column;gap:11px;margin-bottom:32px}
    .price-list li{display:flex;align-items:center;gap:10px;font-size:14.5px;color:var(--n700)}
    .price-list li svg{width:18px;height:18px;color:var(--g500);flex-shrink:0}
    .price-cta{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;
               background:var(--g600);color:#fff;font-size:16px;font-weight:700;padding:16px;
               border-radius:12px;transition:background .2s}
    .price-cta:hover{background:var(--g700)}
    .price-alt{margin-top:20px;display:flex;flex-direction:column;gap:10px;align-items:center}
    .price-alt span{font-size:13px;color:var(--n400)}
    .price-alt a{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:600;
                 color:var(--g600);transition:color .2s}
    .price-alt a:hover{color:var(--g700)}

    /* ── FAQ ── */
    .faq-list{max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:4px}
    .faq-item{background:#fff;border:1px solid var(--n200);border-radius:12px;overflow:hidden;transition:border-color .2s}
    .faq-item.open{border-color:var(--g200)}
    .faq-q{width:100%;display:flex;align-items:center;justify-content:space-between;gap:16px;
           padding:17px 20px;font-size:15px;font-weight:600;text-align:left;color:var(--n900);cursor:pointer}
    .faq-icon{width:22px;height:22px;border-radius:6px;background:var(--n50);display:flex;align-items:center;
              justify-content:center;flex-shrink:0;transition:transform .25s,background .2s;font-size:14px;color:var(--n500)}
    .faq-item.open .faq-icon{transform:rotate(45deg);background:var(--g50);color:var(--g600)}
    .faq-a{overflow:hidden;max-height:0;opacity:0;transition:max-height .35s cubic-bezier(.16,1,.3,1),opacity .25s,padding .35s}
    .faq-item.open .faq-a{max-height:300px;opacity:1;padding:0 20px 18px}
    .faq-a p{font-size:14px;color:var(--n500);line-height:1.65}

    /* ── Testimonials ── */
    .testi{background:var(--g950);padding:90px 0;overflow:hidden}
    .testi .sec-label{color:var(--g400)}
    .testi .sec-title{color:#fff}
    .testi .sec-sub{color:rgba(255,255,255,.45)}
    .testi-track{display:flex;gap:16px;width:max-content;padding:8px 0}
    .testi-track.scroll-l{animation:tsl 45s linear infinite}
    .testi-track.scroll-r{animation:tsr 45s linear infinite}
    .testi-track:hover{animation-play-state:paused}
    @keyframes tsl{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    @keyframes tsr{from{transform:translateX(-50%)}to{transform:translateX(0)}}
    .testi-card{width:320px;flex-shrink:0;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);
                border-radius:14px;padding:22px}
    .testi-stars{display:flex;gap:2px;margin-bottom:12px}
    .testi-stars svg{width:15px;height:15px;color:#FBBF24}
    .testi-text{font-size:13.5px;color:rgba(255,255,255,.7);line-height:1.6;margin-bottom:16px}
    .testi-author{display:flex;align-items:center;gap:10px}
    .testi-avatar{width:34px;height:34px;border-radius:999px;background:var(--g700);display:flex;align-items:center;
                  justify-content:center;font-size:13px;font-weight:700;color:var(--g200)}
    .testi-name{font-size:13px;font-weight:600;color:#fff}
    .testi-role{font-size:11.5px;color:rgba(255,255,255,.35)}
    .testi-rows{display:flex;flex-direction:column;gap:14px;margin-top:44px}

    /* ── CTA ── */
    .cta-sec{padding:90px 0}
    .cta-box{background:var(--g600);border-radius:22px;padding:56px 40px;text-align:center;position:relative;overflow:hidden}
    .cta-box::before{content:'';position:absolute;top:-50px;right:-50px;width:260px;height:260px;
                     background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);pointer-events:none}
    .cta-box h2{font-size:clamp(26px,3.8vw,36px);font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.15;
                margin-bottom:12px}
    .cta-box p{font-size:16px;color:rgba(255,255,255,.65);max-width:440px;margin:0 auto 28px}
    .btn-white{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--g700);font-size:15px;
               font-weight:700;padding:15px 32px;border-radius:12px;transition:transform .1s,box-shadow .2s}
    .btn-white:hover{box-shadow:0 4px 20px rgba(0,0,0,.12)}
    .btn-white:active{transform:scale(.98)}

    /* ── Footer ── */
    .foot{background:var(--g950);padding:48px 0 0;color:rgba(255,255,255,.55)}
    .foot-top{display:flex;justify-content:space-between;align-items:flex-start;gap:32px;padding-bottom:40px;
              border-bottom:1px solid rgba(255,255,255,.08);flex-wrap:wrap}
    .foot-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;font-size:15px;margin-bottom:10px}
    .foot-brand img{width:26px;height:26px;border-radius:6px}
    .foot-desc{font-size:13px;line-height:1.6;max-width:320px}
    .foot-links{display:flex;gap:32px}
    .foot-links a{font-size:13px;transition:color .2s}
    .foot-links a:hover{color:#fff}
    .foot-copy{text-align:center;padding:20px 0;font-size:12px;color:rgba(255,255,255,.25)}

    /* ── WA Float ── */
    .wa-float{position:fixed;bottom:24px;right:24px;width:56px;height:56px;background:#25D366;border-radius:999px;
              display:flex;align-items:center;justify-content:center;z-index:999;
              box-shadow:0 4px 16px rgba(37,211,102,.35);transition:transform .2s}
    .wa-float:hover{transform:scale(1.08)}
    .wa-float svg{width:28px;height:28px;color:#fff}

    /* ── Animations ── */
    @keyframes hero-in{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
    .hi{animation:hero-in .65s ease both}
    .hi-1{animation:hero-in .65s ease .08s both}
    .hi-2{animation:hero-in .65s ease .16s both}
    .hi-3{animation:hero-in .65s ease .24s both}
    .hi-4{animation:hero-in .65s ease .32s both}
    .rv{opacity:0;transform:translateY(20px);transition:opacity .55s ease,transform .55s ease}
    .rv.vis{opacity:1;transform:translateY(0)}
    .rv-1{transition-delay:.08s}.rv-2{transition-delay:.16s}.rv-3{transition-delay:.24s}

    /* ── Responsive ── */
    @media(max-width:900px){
        .hero-grid{grid-template-columns:1fr;gap:36px;text-align:center}
        .hero-sub{margin:0 auto 24px}
        .hero-price{margin:0 auto 24px}
        .hero-actions{justify-content:center}
        .hero-trust{justify-content:center}
        .hero-visual{order:-1}
        .card-float img{width:260px;height:260px}
        .steps{grid-template-columns:1fr 1fr;gap:14px}
        .diff-grid{grid-template-columns:1fr}
        .feat-grid{grid-template-columns:1fr}
        .foot-top{flex-direction:column;gap:24px}
    }
    @media(max-width:640px){
        .nav-links{display:none}
        .nav-toggle{display:block}
        .nav.open .nav-links{display:flex;flex-direction:column;position:absolute;top:56px;left:12px;right:12px;
                             background:rgba(9,26,16,.97);border-radius:14px;padding:10px;
                             box-shadow:0 12px 32px rgba(0,0,0,.2)}
        .nav.open .nav-links a{padding:12px 16px;border-radius:10px}
        .steps{grid-template-columns:1fr}
        .cta-box{padding:40px 20px}
        .strip-row{gap:20px}
        .price-card{padding:32px 24px}
    }
    </style>
</head>
<body>

<!-- ════ Nav ════ -->
<nav class="nav" x-data="{open:false}" :class="{'open':open}">
    <a href="#" class="nav-logo">
        <img src="/img/logo.png" alt="Provecho">
        <span>Provecho</span>
    </a>
    <div class="nav-links">
        <a href="#cara-kerja" @click="open=false">Cara Kerja</a>
        <a href="#kenapa" @click="open=false">Kenapa Kami</a>
        <a href="#harga" @click="open=false">Harga</a>
        <a href="#faq" @click="open=false">FAQ</a>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
        <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="nav-cta" target="_blank">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Pesan
        </a>
        <button class="nav-toggle" @click="open=!open">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h14M4 12h14M4 17h14"/></svg>
        </button>
    </div>
</nav>

<!-- ════ Hero ════ -->
<section class="hero">
    <div class="ctnr">
        <div class="hero-grid">
            <div>
                <div class="hero-badge hi">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    Google Review Card NFC + QR
                </div>
                <h1 class="hi-1">Taruh di kasir.<br>Pelanggan tap.<br><em>Review masuk.</em></h1>
                <p class="hero-sub hi-2">Card akrilik dengan NFC + QR Code yang langsung membuka halaman Google Review usaha Anda. Pelanggan tinggal tap atau scan — tanpa install aplikasi, tanpa ribet.</p>
                <div class="hero-price hi-3">
                    <b>Rp 50.000</b> /card
                </div>
                <div class="hero-actions hi-3">
                    <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="btn-wa" target="_blank">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Pesan via WhatsApp
                    </a>
                    <a href="#cara-kerja" class="btn-ghost">Lihat Cara Kerja</a>
                </div>
                <div class="hero-trust hi-4">
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>Bayar sekali, pakai selamanya</span>
                    <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>Tanpa biaya bulanan</span>
                </div>
            </div>
            <div class="hero-visual hi-2">
                <div class="card-float">
                    <img src="/img/desain-card.png" alt="Provecho Google Review Card — Card akrilik NFC + QR Code">
                    <svg class="nfc-ring" viewBox="0 0 100 100"><circle cx="50" cy="50"/><circle cx="50" cy="50"/><circle cx="50" cy="50"/></svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Strip ════ -->
<div class="strip">
    <div class="ctnr">
        <div class="strip-row">
            <div class="strip-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                3 detik — tap to review
            </div>
            <div class="strip-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Akrilik premium, tahan lama
            </div>
            <div class="strip-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 18a6 6 0 100-12 6 6 0 000 12z"/><path d="M2 12C2 6.5 6.5 2 12 2m10 10c0 5.5-4.5 10-10 10"/></svg>
                NFC + QR Code
            </div>
            <div class="strip-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Tanpa biaya bulanan
            </div>
        </div>
    </div>
</div>

<!-- ════ Cara Kerja ════ -->
<section class="sec" id="cara-kerja">
    <div class="ctnr">
        <div class="sec-head ctr rv">
            <div class="sec-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                Cara Kerja
            </div>
            <h2 class="sec-title">Tiga langkah. Selesai.</h2>
            <p class="sec-sub">Tidak perlu install apapun. Tidak perlu pengetahuan teknis.</p>
        </div>
        <div class="steps">
            <div class="step rv">
                <div class="step-num">1</div>
                <h3>Pesan & Terima</h3>
                <p>Hubungi kami via WhatsApp. Card akrilik premium dikirim langsung ke alamat Anda.</p>
            </div>
            <div class="step rv rv-1">
                <div class="step-num">2</div>
                <h3>Aktivasi Sendiri</h3>
                <p>Scan card baru Anda, cari nama usaha di Google, pilih. Card langsung aktif dalam 30 detik.</p>
            </div>
            <div class="step rv rv-2">
                <div class="step-num">3</div>
                <h3>Taruh & Biarkan Bekerja</h3>
                <p>Taruh di meja kasir atau konter. Pelanggan tap NFC atau scan QR — halaman Google Review langsung terbuka di HP mereka.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════ Kenapa Beda ════ -->
<section class="sec diff" id="kenapa">
    <div class="ctnr">
        <div class="sec-head ctr rv">
            <div class="sec-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                Perbandingan
            </div>
            <h2 class="sec-title">Bukan card NFC biasa.</h2>
            <p class="sec-sub">Cuma Rp 6.000 lebih mahal dari card NFC murahan — tapi bedanya jauh.</p>
        </div>
        <div class="diff-grid">
            <div class="diff-card old rv">
                <h3>Card NFC Biasa</h3>
                <div class="diff-sub">Yang banyak dijual di marketplace</div>
                <ul class="diff-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Link hardcoded — tidak bisa diubah</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Link mati? Beli card baru</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Material stiker atau PVC tipis</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Setup manual, harus paham teknis</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Tidak ada support setelah beli</li>
                </ul>
                <div class="diff-price">Mulai dari Rp 44.000</div>
            </div>
            <div class="diff-card prov rv rv-1">
                <h3>Provecho</h3>
                <div class="diff-sub">Smart card yang bisa dikelola</div>
                <ul class="diff-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Smart link — bisa diupdate kapan saja</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Link bermasalah? Hubungi kami, kami perbaiki</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Akrilik premium, tahan air & gores</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Aktivasi mandiri, tanpa keahlian teknis</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Support via WhatsApp</li>
                </ul>
                <div class="diff-price"><b>Rp 50.000</b> /card</div>
            </div>
        </div>

        <!-- Feature cards -->
        <div class="feat-grid">
            <div class="feat rv">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                </div>
                <h3>Smart Link</h3>
                <p>Card NFC biasa pakai link hardcoded — kalau profil Google Anda berubah, card jadi sampah. Provecho pakai server redirect: link bermasalah? Hubungi kami via WhatsApp, kami update. Card tetap berfungsi.</p>
            </div>
            <div class="feat rv rv-1">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 18a6 6 0 100-12 6 6 0 000 12z"/><path d="M2 12C2 6.5 6.5 2 12 2m10 10c0 5.5-4.5 10-10 10"/></svg>
                </div>
                <h3>Dual Akses: NFC + QR</h3>
                <p>NFC untuk smartphone modern — tap tanpa buka kamera. QR Code untuk HP tanpa NFC. Dua jalur, semua pelanggan bisa review. Tanpa install aplikasi apapun.</p>
            </div>
            <div class="feat rv">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                </div>
                <h3>Aktivasi Mandiri</h3>
                <p>Card sampai, scan, cari nama usaha Anda di Google, pilih. Selesai. Bisa juga paste link Google Maps langsung. Tidak perlu hubungi siapa-siapa untuk setup.</p>
            </div>
            <div class="feat rv rv-1">
                <div class="feat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Akrilik Premium</h3>
                <p>Bukan stiker tipis yang ngelupas. Card Provecho dari akrilik tebal, tahan air, tahan gores. Chip NFC NTAG213 tanpa baterai — bisa berfungsi bertahun-tahun tanpa perawatan.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════ Harga ════ -->
<section class="sec" id="harga">
    <div class="ctnr">
        <div class="sec-head ctr rv">
            <div class="sec-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Harga
            </div>
            <h2 class="sec-title">Satu harga. Tanpa kejutan.</h2>
            <p class="sec-sub">Bayar sekali, pakai selamanya. Tidak ada biaya berlangganan.</p>
        </div>
        <div class="price-wrap rv">
            <div class="price-card">
                <h3>Provecho Google Review Card</h3>
                <div class="price-amount">Rp 50.000 <small>/card</small></div>
                <div class="price-note">Pembelian satu kali. Tanpa biaya bulanan.</div>
                <ul class="price-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Card akrilik NFC + QR Code</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Chip NTAG213, tahan bertahun-tahun</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Aktivasi mandiri — tanpa bantuan teknis</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Smart link — bisa diupdate jika bermasalah</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Support via WhatsApp</li>
                </ul>
                <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="price-cta" target="_blank">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Pesan via WhatsApp
                </a>
                <div class="price-alt">
                    <span>Juga tersedia di Shopee</span>
                    <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20tanya%20program%20reseller%20Provecho" target="_blank">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4-4"/><path d="M16 3.13a4 4 0 010 7.75"/><path d="M22 21v-2a4 4 0 00-3-3.87"/></svg>
                        Program reseller tersedia — hubungi kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════ FAQ ════ -->
<section class="sec" id="faq" style="background:var(--n50)">
    <div class="ctnr">
        <div class="sec-head ctr rv">
            <div class="sec-label">FAQ</div>
            <h2 class="sec-title">Pertanyaan yang sering muncul.</h2>
        </div>
        <div class="faq-list" x-data="{active:null}">
            <div class="faq-item rv" :class="{'open':active===1}">
                <button class="faq-q" @click="active=active===1?null:1"><span>Apa itu Provecho?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Provecho adalah card akrilik berteknologi NFC + QR Code yang memudahkan pelanggan memberikan Google Review untuk usaha Anda. Taruh di meja kasir — pelanggan tap HP atau scan QR, halaman Google Review langsung terbuka.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===2}">
                <button class="faq-q" @click="active=active===2?null:2"><span>Bagaimana cara kerjanya?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Card berisi chip NFC dan QR Code yang terhubung ke server Provecho. Server meneruskan ke halaman Google Review usaha Anda. Jadi ketika pelanggan tap atau scan, browser langsung terbuka di halaman review — tinggal kasih bintang dan tulis ulasan.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===3}">
                <button class="faq-q" @click="active=active===3?null:3"><span>Apakah semua HP bisa pakai NFC?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Hampir semua smartphone keluaran 2018 ke atas sudah mendukung NFC. Untuk HP yang belum punya NFC, pelanggan bisa scan QR Code — jadi semua pelanggan tetap bisa memberikan review.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===4}">
                <button class="faq-q" @click="active=active===4?null:4"><span>Bagaimana kalau link Google Review saya berubah?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Hubungi kami via WhatsApp, kami akan update link-nya dari sisi server. Card fisik tidak perlu diganti. Ini bedanya dengan card NFC biasa yang URL-nya hardcoded dan tidak bisa diubah.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===5}">
                <button class="faq-q" @click="active=active===5?null:5"><span>Apakah ada biaya bulanan?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Tidak. Provecho adalah pembelian satu kali seharga Rp 50.000. Tidak ada biaya berlangganan, tidak ada biaya maintenance, tidak ada biaya tersembunyi. Bayar sekali, pakai selamanya.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===6}">
                <button class="faq-q" @click="active=active===6?null:6"><span>Berapa lama card bertahan?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Material akrilik tahan air dan tahan gores, dirancang untuk pemakaian jangka panjang. Chip NFC NTAG213 tidak memerlukan baterai dan bisa berfungsi bertahun-tahun tanpa perawatan apapun.</p></div>
            </div>
            <div class="faq-item rv" :class="{'open':active===7}">
                <button class="faq-q" @click="active=active===7?null:7"><span>Bagaimana cara pesannya?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Klik tombol "Pesan via WhatsApp" di halaman ini. Anda langsung terhubung dengan kami untuk proses pemesanan dan pengiriman. Pembayaran bisa via transfer bank atau QRIS.</p></div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Testimonials ════ -->
<section class="testi">
    <div class="ctnr">
        <div class="sec-head ctr rv">
            <div class="sec-label">Testimoni</div>
            <h2 class="sec-title">Kata mereka yang sudah pakai.</h2>
            <p class="sec-sub">UMKM dari berbagai bidang sudah merasakan dampak Provecho.</p>
        </div>
    </div>
    <div class="testi-rows">
        <div class="testi-track scroll-l">
            @php
            $t1 = [
                ['i'=>'R','n'=>'Rina Wulandari','r'=>'Kafe Kopi Senja, Bandung','t'=>'Sebelum pakai Provecho, review Google kami cuma 23. Sekarang sudah 87 dalam 3 bulan. Card-nya bagus, akrilik tebal, cocok ditaruh di meja kasir.'],
                ['i'=>'A','n'=>'Andi Pratama','r'=>'Barbershop Gentlemen, Surabaya','t'=>'Pelanggan tinggal tap HP aja, langsung muncul halaman review. Ga perlu minta-minta lagi. Simpel banget.'],
                ['i'=>'D','n'=>'Dewi Sartika','r'=>'Klinik Gigi Sehat Ceria, Jakarta','t'=>'Kami taruh di meja resepsionis. Pasien selesai perawatan, tap card, kasih review. Rating kami naik dari 4.1 ke 4.6 dalam 2 bulan.'],
                ['i'=>'H','n'=>'Hendra Wijaya','r'=>'Bengkel Motor Jaya, Semarang','t'=>'Ga nyangka bakal ngaruh segini. Pelanggan yang puas tinggal tap, review masuk. Sekarang bengkel kami selalu muncul pertama di pencarian Google.'],
                ['i'=>'S','n'=>'Sri Mulyani','r'=>'Toko Kue Mama Sri, Yogyakarta','t'=>'Card-nya tahan lama, sudah 5 bulan masih bagus padahal kena tangan berminyak terus. QR Code juga berfungsi buat pelanggan yang HP-nya ga ada NFC.'],
                ['i'=>'F','n'=>'Fajar Nugroho','r'=>'Warung Makan Sederhana, Malang','t'=>'Aktivasinya gampang banget, tinggal scan terus cari nama warung di Google. Ga perlu hubungi siapa-siapa. Langsung aktif.'],
            ];
            @endphp
            @foreach(array_merge($t1,$t1) as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">{{ $t['t'] }}</div>
                <div class="testi-author">
                    <div class="testi-avatar">{{ $t['i'] }}</div>
                    <div><div class="testi-name">{{ $t['n'] }}</div><div class="testi-role">{{ $t['r'] }}</div></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="testi-track scroll-r">
            @php
            $t2 = [
                ['i'=>'B','n'=>'Budi Santoso','r'=>'RM Padang Minang, Bekasi','t'=>'Harga 50 ribu, efeknya jutaan. Review naik, pelanggan baru datang karena lihat rating tinggi di Google. Worth it banget.'],
                ['i'=>'L','n'=>'Linda Permata','r'=>'Salon Cantik Alami, Depok','t'=>'Klien kami suka karena ga ribet. Tap HP, kasih bintang, selesai. Kami juga ga perlu canggung minta review lagi.'],
                ['i'=>'T','n'=>'Tommy Gunawan','r'=>'Toko Elektronik Jaya, Tangerang','t'=>'Card-nya premium, desainnya bagus. Pelanggan sering tanya ini apa, jadi sekalian promosi usaha. Dual fungsi.'],
                ['i'=>'N','n'=>'Nisa Rahmawati','r'=>'Pet Shop Paw Friends, Bogor','t'=>'Yang paling aku suka: link-nya bisa diupdate. Waktu aku pindah alamat, tinggal chat WhatsApp mereka, link langsung diganti. Ga perlu beli card baru.'],
                ['i'=>'M','n'=>'Made Agus','r'=>'Restoran Bali Kitchen, Denpasar','t'=>'Tamu dari luar negeri juga bisa langsung tap. NFC itu universal, ga perlu bahasa sama. Tap, bintang 5, done.'],
                ['i'=>'Y','n'=>'Yuni Astuti','r'=>'Laundry Express Clean, Medan','t'=>'Baru 2 minggu pasang, review sudah nambah 12. Sebelumnya sebulan paling dapat 1-2. Berasa banget bedanya.'],
            ];
            @endphp
            @foreach(array_merge($t2,$t2) as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($i=0;$i<5;$i++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">{{ $t['t'] }}</div>
                <div class="testi-author">
                    <div class="testi-avatar">{{ $t['i'] }}</div>
                    <div><div class="testi-name">{{ $t['n'] }}</div><div class="testi-role">{{ $t['r'] }}</div></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ════ CTA ════ -->
<section class="cta-sec">
    <div class="ctnr">
        <div class="cta-box rv">
            <h2>Siap punya lebih banyak Google Review?</h2>
            <p>Satu card, Rp 50.000, tanpa biaya bulanan. Review mengalir tanpa perlu minta-minta.</p>
            <a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20Google%20Review%20Card" class="btn-white" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color:var(--g600)"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pesan via WhatsApp
            </a>
        </div>
    </div>
</section>

<!-- ════ Footer ════ -->
<footer class="foot">
    <div class="ctnr">
        <div class="foot-top">
            <div>
                <div class="foot-brand"><img src="/img/logo.png" alt="Provecho"> Provecho</div>
                <p class="foot-desc">Google Review Card NFC + QR Code untuk UMKM Indonesia. Bantu usaha Anda mendapatkan lebih banyak review dengan cara yang paling mudah.</p>
            </div>
            <div class="foot-links">
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#kenapa">Kenapa Kami</a>
                <a href="#harga">Harga</a>
                <a href="#faq">FAQ</a>
                <a href="https://wa.me/6283842843671" target="_blank">WhatsApp</a>
            </div>
        </div>
        <div class="foot-copy">&copy; {{ date('Y') }} Provecho. All rights reserved.</div>
    </div>
</footer>

<!-- ════ WA Float ════ -->
<a href="https://wa.me/6283842843671?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="wa-float" target="_blank" aria-label="Chat via WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<script>
addEventListener('scroll',()=>{document.querySelector('.nav').classList.toggle('scrolled',scrollY>20)},{passive:true});
const io=new IntersectionObserver(es=>{es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('vis');io.unobserve(e.target)}})},{threshold:.15});
document.querySelectorAll('.rv').forEach(el=>io.observe(el));
</script>
</body>
</html>
