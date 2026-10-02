<!DOCTYPE html>
<html lang="id" style="scroll-behavior:smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provecho — Google Review Card NFC untuk UMKM</title>
    <meta name="description" content="Kartu NFC + QR Code yang langsung membuka halaman Google Review. Pelanggan tap, bintang 5 mengalir. Mulai dari Rp 75.000.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
    :root {
        --g50:#F0FDF4;--g100:#DCFCE7;--g200:#BBF7D0;--g300:#86EFAC;
        --g400:#4ADE80;--g500:#22C55E;--g600:#1B8C3D;--g700:#15702F;
        --g800:#166534;--g900:#14532D;--g950:#091A10;
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

    .container{width:100%;max-width:1140px;margin:0 auto;padding:0 24px}

    /* ── Nav ── */
    .nav{position:fixed;top:14px;left:50%;transform:translateX(-50%);width:calc(100% - 28px);max-width:920px;
         background:rgba(9,26,16,.92);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
         border-radius:999px;padding:8px 8px 8px 22px;z-index:1000;display:flex;align-items:center;
         justify-content:space-between;transition:box-shadow .3s}
    .nav.scrolled{box-shadow:0 8px 40px rgba(0,0,0,.18)}
    .nav-logo{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;font-size:15px;letter-spacing:-.02em}
    .nav-logo img{width:28px;height:28px;border-radius:6px}
    .nav-links{display:flex;align-items:center;gap:6px}
    .nav-links a{color:rgba(255,255,255,.7);font-size:13.5px;font-weight:500;padding:7px 14px;border-radius:999px;
                 transition:color .2s,background .2s}
    .nav-links a:hover{color:#fff;background:rgba(255,255,255,.08)}
    .nav-cta{display:inline-flex;align-items:center;gap:6px;background:var(--g600);color:#fff;font-size:13.5px;
             font-weight:600;padding:9px 20px;border-radius:999px;transition:background .2s;white-space:nowrap}
    .nav-cta:hover{background:var(--g700)}
    .nav-toggle{display:none;color:#fff;padding:8px}

    /* ── Hero ── */
    .hero{padding:140px 0 80px;position:relative;overflow:hidden}
    .hero::before{content:'';position:absolute;top:-120px;right:-100px;width:500px;height:500px;
                  background:radial-gradient(circle,rgba(27,140,61,.12) 0%,transparent 70%);pointer-events:none}
    .hero::after{content:'';position:absolute;bottom:-80px;left:-60px;width:400px;height:400px;
                 background:radial-gradient(circle,rgba(34,197,94,.08) 0%,transparent 70%);pointer-events:none}
    .hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
    .hero-badge{display:inline-flex;align-items:center;gap:8px;background:var(--g50);border:1px solid var(--g200);
                color:var(--g700);font-size:13px;font-weight:600;padding:6px 16px;border-radius:999px;margin-bottom:24px}
    .hero h1{font-size:clamp(36px,5vw,56px);font-weight:800;line-height:1.08;letter-spacing:-.03em;margin-bottom:20px}
    .hero h1 em{font-style:normal;color:var(--g600)}
    .hero-sub{font-size:17px;color:var(--n500);line-height:1.65;max-width:480px;margin-bottom:32px}
    .hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:36px}
    .btn-primary{display:inline-flex;align-items:center;gap:8px;background:var(--g600);color:#fff;font-size:15px;
                 font-weight:600;padding:14px 28px;border-radius:12px;transition:background .2s,transform .1s}
    .btn-primary:hover{background:var(--g700)}
    .btn-primary:active{transform:scale(.98)}
    .btn-secondary{display:inline-flex;align-items:center;gap:8px;background:var(--n50);border:1px solid var(--n200);
                   color:var(--n700);font-size:15px;font-weight:600;padding:14px 28px;border-radius:12px;
                   transition:background .2s,border-color .2s}
    .btn-secondary:hover{background:var(--n100);border-color:var(--n300)}
    .hero-badges{display:flex;gap:10px;flex-wrap:wrap}
    .hero-badges span{display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:500;
                      color:var(--n500);background:var(--n50);border:1px solid var(--n200);padding:5px 12px;border-radius:999px}
    .hero-badges span svg{width:14px;height:14px;color:var(--g600)}

    .hero-visual{display:flex;align-items:center;justify-content:center;position:relative}
    .card-showcase{position:relative;width:320px;height:320px}
    .card-showcase img{width:280px;height:280px;object-fit:contain;border-radius:20px;
                       filter:drop-shadow(0 20px 40px rgba(0,0,0,.12));
                       transform:perspective(800px) rotateY(-6deg) rotateX(4deg);
                       transition:transform .4s ease}
    .card-showcase:hover img{transform:perspective(800px) rotateY(-2deg) rotateX(2deg)}
    .card-showcase .nfc-ring{position:absolute;top:50%;left:50%;width:100px;height:100px;
                             transform:translate(-50%,-50%);pointer-events:none}
    .nfc-ring circle{fill:none;stroke:var(--g400);stroke-width:1.5;opacity:0;
                     transform-origin:center;animation:nfc-pulse 2.4s ease-out infinite}
    .nfc-ring circle:nth-child(2){animation-delay:.4s}
    .nfc-ring circle:nth-child(3){animation-delay:.8s}
    @keyframes nfc-pulse{0%{r:8;opacity:.7}100%{r:48;opacity:0}}

    /* ── Stats ── */
    .stats{background:var(--g50);border-top:1px solid var(--g100);border-bottom:1px solid var(--g100);padding:48px 0}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:32px;text-align:center}
    .stat-num{font-size:36px;font-weight:800;letter-spacing:-.03em;color:var(--g600);line-height:1}
    .stat-label{font-size:13px;color:var(--n500);font-weight:500;margin-top:6px}

    /* ── Section headers ── */
    .sec{padding:100px 0}
    .sec-label{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--g600);
               text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px}
    .sec-title{font-size:clamp(28px,4vw,40px);font-weight:800;letter-spacing:-.03em;line-height:1.15;margin-bottom:16px}
    .sec-sub{font-size:17px;color:var(--n500);max-width:560px;line-height:1.6}
    .sec-header{margin-bottom:56px}
    .sec-header.center{text-align:center}
    .sec-header.center .sec-sub{margin:0 auto}

    /* ── Cara Kerja ── */
    .steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
    .step{background:#fff;border:1px solid var(--n200);border-radius:16px;padding:28px 24px;position:relative;
          transition:border-color .2s,box-shadow .2s}
    .step:hover{border-color:var(--g200);box-shadow:0 8px 24px rgba(27,140,61,.06)}
    .step-num{width:36px;height:36px;background:var(--g50);border:1.5px solid var(--g200);border-radius:10px;
              display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--g700);
              margin-bottom:18px}
    .step h3{font-size:16px;font-weight:700;margin-bottom:8px;letter-spacing:-.01em}
    .step p{font-size:14px;color:var(--n500);line-height:1.55}
    .step-connector{display:none}

    /* ── Features ── */
    .feature{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;padding:64px 0;
             border-top:1px solid var(--n200)}
    .feature:first-child{border-top:none}
    .feature.reverse{direction:rtl}
    .feature.reverse>*{direction:ltr}
    .feature-tag{font-size:12px;font-weight:700;color:var(--g600);text-transform:uppercase;letter-spacing:.08em;
                 margin-bottom:12px}
    .feature h3{font-size:clamp(22px,3vw,28px);font-weight:800;letter-spacing:-.02em;line-height:1.2;margin-bottom:14px}
    .feature-desc{font-size:15px;color:var(--n500);line-height:1.65;margin-bottom:24px}
    .feature-list{list-style:none;display:flex;flex-direction:column;gap:10px}
    .feature-list li{display:flex;align-items:flex-start;gap:10px;font-size:14px;font-weight:500;color:var(--n700)}
    .feature-list li svg{width:18px;height:18px;color:var(--g500);flex-shrink:0;margin-top:2px}

    .mockup{background:var(--g950);border-radius:14px;overflow:hidden;box-shadow:0 24px 48px -12px rgba(0,0,0,.2)}
    .mockup-bar{display:flex;gap:6px;padding:11px 16px;background:rgba(255,255,255,.05)}
    .mockup-dot{width:9px;height:9px;border-radius:50%}
    .mockup-dot:nth-child(1){background:#ff5f57}
    .mockup-dot:nth-child(2){background:#febc2e}
    .mockup-dot:nth-child(3){background:#28c840}
    .mockup-body{padding:20px}

    .mock-phone{width:220px;margin:0 auto;background:#111;border-radius:28px;padding:6px;
                box-shadow:0 20px 40px rgba(0,0,0,.2)}
    .mock-phone-notch{width:80px;height:20px;background:#111;border-radius:0 0 12px 12px;margin:0 auto;position:relative;z-index:2}
    .mock-phone-screen{background:#fff;border-radius:22px;padding:20px 16px;min-height:280px}

    /* ── Comparison ── */
    .compare{background:var(--n50)}
    .compare-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:800px;margin:0 auto}
    .compare-card{border-radius:16px;padding:32px 28px}
    .compare-card.manual{background:#fff;border:1px solid var(--n200)}
    .compare-card.smart{background:var(--g600);color:#fff}
    .compare-card h3{font-size:20px;font-weight:700;margin-bottom:6px}
    .compare-card .compare-sub{font-size:13px;opacity:.7;margin-bottom:24px}
    .compare-list{list-style:none;display:flex;flex-direction:column;gap:14px}
    .compare-list li{display:flex;align-items:flex-start;gap:10px;font-size:14.5px;line-height:1.45}
    .compare-list li svg{width:18px;height:18px;flex-shrink:0;margin-top:2px}
    .compare-card.manual .compare-list li svg{color:#ef4444}
    .compare-card.smart .compare-list li svg{color:var(--g200)}
    .compare-result{margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.15);
                    font-size:28px;font-weight:800;letter-spacing:-.02em}
    .compare-card.manual .compare-result{border-top-color:var(--n200);color:var(--n400)}
    .compare-card.smart .compare-result{color:#fff}

    /* ── Pricing ── */
    .pricing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:960px;margin:0 auto}
    .price-card{background:#fff;border:1px solid var(--n200);border-radius:16px;padding:32px 28px;position:relative;
                transition:border-color .2s,box-shadow .2s}
    .price-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.06)}
    .price-card.popular{border-color:var(--g300);box-shadow:0 0 0 1px var(--g300)}
    .price-badge{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--g600);color:#fff;
                 font-size:11.5px;font-weight:700;padding:4px 16px;border-radius:999px;white-space:nowrap}
    .price-card h3{font-size:18px;font-weight:700;margin-bottom:4px}
    .price-card .price-type{font-size:13px;color:var(--n400);margin-bottom:20px}
    .price-amount{font-size:36px;font-weight:800;letter-spacing:-.03em;color:var(--n900)}
    .price-amount small{font-size:15px;font-weight:500;color:var(--n400)}
    .price-note{font-size:12.5px;color:var(--n400);margin-top:4px;margin-bottom:24px}
    .price-cta{display:block;text-align:center;padding:13px;border-radius:10px;font-size:14px;font-weight:600;
               transition:background .2s,color .2s}
    .price-cta.primary{background:var(--g600);color:#fff}
    .price-cta.primary:hover{background:var(--g700)}
    .price-cta.outline{background:var(--n50);border:1px solid var(--n200);color:var(--n700)}
    .price-cta.outline:hover{background:var(--n100)}
    .price-divider{height:1px;background:var(--n200);margin:24px 0}
    .price-features{list-style:none;display:flex;flex-direction:column;gap:11px}
    .price-features li{display:flex;align-items:center;gap:9px;font-size:13.5px;color:var(--n600)}
    .price-features li svg{width:16px;height:16px;flex-shrink:0}
    .price-features li svg.check{color:var(--g500)}
    .price-features li svg.x{color:var(--n300)}
    .price-features li.disabled{color:var(--n400)}

    /* ── FAQ ── */
    .faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:4px}
    .faq-item{background:#fff;border:1px solid var(--n200);border-radius:12px;overflow:hidden;
              transition:border-color .2s}
    .faq-item.open{border-color:var(--g200)}
    .faq-q{width:100%;display:flex;align-items:center;justify-content:space-between;gap:16px;
           padding:18px 22px;font-size:15px;font-weight:600;text-align:left;color:var(--n900);cursor:pointer}
    .faq-icon{width:22px;height:22px;border-radius:6px;background:var(--n50);display:flex;align-items:center;
              justify-content:center;flex-shrink:0;transition:transform .25s,background .2s;font-size:14px;color:var(--n500)}
    .faq-item.open .faq-icon{transform:rotate(45deg);background:var(--g50);color:var(--g600)}
    .faq-a{overflow:hidden;max-height:0;opacity:0;transition:max-height .35s cubic-bezier(.16,1,.3,1),opacity .25s,padding .35s}
    .faq-item.open .faq-a{max-height:300px;opacity:1;padding:0 22px 20px}
    .faq-a p{font-size:14.5px;color:var(--n500);line-height:1.65}

    /* ── Testimonials ── */
    .testi{background:var(--g950);padding:100px 0;overflow:hidden}
    .testi .sec-label{color:var(--g400)}
    .testi .sec-title{color:#fff}
    .testi .sec-sub{color:rgba(255,255,255,.5)}
    .testi-track{display:flex;gap:20px;width:max-content;padding:10px 0}
    .testi-track.scroll-left{animation:tscroll-l 50s linear infinite}
    .testi-track.scroll-right{animation:tscroll-r 50s linear infinite}
    .testi-track:hover{animation-play-state:paused}
    @keyframes tscroll-l{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    @keyframes tscroll-r{from{transform:translateX(-50%)}to{transform:translateX(0)}}
    .testi-card{width:340px;flex-shrink:0;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);
                border-radius:14px;padding:24px}
    .testi-stars{display:flex;gap:2px;margin-bottom:14px}
    .testi-stars svg{width:16px;height:16px;color:#FBBF24}
    .testi-text{font-size:14px;color:rgba(255,255,255,.75);line-height:1.6;margin-bottom:18px}
    .testi-author{display:flex;align-items:center;gap:12px}
    .testi-avatar{width:36px;height:36px;border-radius:999px;background:var(--g700);display:flex;align-items:center;
                  justify-content:center;font-size:14px;font-weight:700;color:var(--g200)}
    .testi-name{font-size:13.5px;font-weight:600;color:#fff}
    .testi-role{font-size:12px;color:rgba(255,255,255,.4)}
    .testi-rows{display:flex;flex-direction:column;gap:16px;margin-top:48px}

    /* ── CTA ── */
    .cta-sec{padding:100px 0}
    .cta-box{background:var(--g600);border-radius:24px;padding:64px 48px;text-align:center;position:relative;overflow:hidden}
    .cta-box::before{content:'';position:absolute;top:-60px;right:-60px;width:300px;height:300px;
                     background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%);pointer-events:none}
    .cta-box h2{font-size:clamp(28px,4vw,40px);font-weight:800;color:#fff;letter-spacing:-.03em;line-height:1.15;
                margin-bottom:14px}
    .cta-box p{font-size:17px;color:rgba(255,255,255,.7);max-width:480px;margin:0 auto 32px}
    .btn-white{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--g700);font-size:15px;
               font-weight:700;padding:15px 32px;border-radius:12px;transition:transform .1s,box-shadow .2s}
    .btn-white:hover{box-shadow:0 4px 20px rgba(0,0,0,.12)}
    .btn-white:active{transform:scale(.98)}
    .cta-badges{display:flex;gap:16px;justify-content:center;margin-top:28px;flex-wrap:wrap}
    .cta-badges span{display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.6);font-size:13px;font-weight:500}
    .cta-badges span svg{width:16px;height:16px;color:var(--g300)}

    /* ── Footer ── */
    .foot{background:var(--g950);padding:64px 0 0;color:rgba(255,255,255,.6)}
    .foot-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:40px;padding-bottom:48px;
               border-bottom:1px solid rgba(255,255,255,.08)}
    .foot-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:700;font-size:16px;margin-bottom:12px}
    .foot-brand img{width:28px;height:28px;border-radius:6px}
    .foot-desc{font-size:13.5px;line-height:1.6;max-width:280px}
    .foot h4{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.35);
             margin-bottom:14px}
    .foot ul{list-style:none;display:flex;flex-direction:column;gap:9px}
    .foot ul a{font-size:13.5px;transition:color .2s}
    .foot ul a:hover{color:#fff}
    .marquee{overflow:hidden;white-space:nowrap;padding:40px 0 20px;border-top:1px solid rgba(255,255,255,.08);margin-top:48px}
    .marquee-track{display:inline-flex;gap:48px;animation:mq 18s linear infinite;
                   font-size:clamp(36px,6vw,56px);font-weight:900;color:rgba(255,255,255,.04);letter-spacing:-.02em}
    @keyframes mq{from{transform:translateX(0)}to{transform:translateX(-50%)}}
    .foot-copy{text-align:center;padding:20px 0;font-size:12.5px;color:rgba(255,255,255,.3)}

    /* ── WA Float ── */
    .wa-float{position:fixed;bottom:24px;right:24px;width:56px;height:56px;background:#25D366;border-radius:999px;
              display:flex;align-items:center;justify-content:center;z-index:999;
              box-shadow:0 4px 16px rgba(37,211,102,.35);transition:transform .2s}
    .wa-float:hover{transform:scale(1.08)}
    .wa-float svg{width:28px;height:28px;color:#fff}

    /* ── Hero entrance ── */
    @keyframes hero-in{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .hero-enter{animation:hero-in .7s ease both}
    .hero-enter-d1{animation:hero-in .7s ease .1s both}
    .hero-enter-d2{animation:hero-in .7s ease .2s both}
    .hero-enter-d3{animation:hero-in .7s ease .3s both}
    .hero-enter-d4{animation:hero-in .7s ease .4s both}

    /* ── Scroll reveal ── */
    .reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease}
    .reveal.visible{opacity:1;transform:translateY(0)}
    .reveal-d1{transition-delay:.1s}.reveal-d2{transition-delay:.2s}.reveal-d3{transition-delay:.3s}.reveal-d4{transition-delay:.35s}

    /* ── Responsive ── */
    @media(max-width:900px){
        .hero-grid{grid-template-columns:1fr;gap:40px;text-align:center}
        .hero-sub{margin:0 auto 32px}
        .hero-actions{justify-content:center}
        .hero-badges{justify-content:center}
        .hero-visual{order:-1}
        .card-showcase{width:240px;height:240px}
        .card-showcase img{width:220px;height:220px}
        .steps-grid{grid-template-columns:1fr 1fr;gap:16px}
        .feature{grid-template-columns:1fr;gap:40px;text-align:center}
        .feature.reverse{direction:ltr}
        .feature-list{align-items:center}
        .compare-grid{grid-template-columns:1fr}
        .pricing-grid{grid-template-columns:1fr;max-width:400px}
        .foot-grid{grid-template-columns:1fr 1fr;gap:32px}
    }
    @media(max-width:640px){
        .nav-links{display:none}
        .nav-toggle{display:block}
        .nav.open .nav-links{display:flex;flex-direction:column;position:absolute;top:56px;left:12px;right:12px;
                             background:rgba(9,26,16,.97);border-radius:16px;padding:12px;
                             box-shadow:0 12px 32px rgba(0,0,0,.2)}
        .nav.open .nav-links a{padding:12px 16px;border-radius:10px}
        .stats-grid{grid-template-columns:1fr 1fr;gap:20px}
        .steps-grid{grid-template-columns:1fr}
        .cta-box{padding:48px 24px}
        .foot-grid{grid-template-columns:1fr}
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
        <a href="#fitur" @click="open=false">Fitur</a>
        <a href="#cara-kerja" @click="open=false">Cara Kerja</a>
        <a href="#harga" @click="open=false">Harga</a>
        <a href="#faq" @click="open=false">FAQ</a>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
        <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="nav-cta" target="_blank">Pesan Sekarang</a>
        <button class="nav-toggle" @click="open=!open">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h14M4 12h14M4 17h14"/></svg>
        </button>
    </div>
</nav>

<!-- ════ Hero ════ -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-badge hero-enter">
                    <svg width="12" height="12" viewBox="0 0 12 12"><path d="M6 0l1.5 4.5L12 6l-4.5 1.5L6 12 4.5 7.5 0 6l4.5-1.5z" fill="currentColor"/></svg>
                    #1 Google Review Card untuk UMKM Indonesia
                </div>
                <h1 class="hero-enter-d1">Review bintang 5,<br>satu tap dari <em>pelanggan.</em></h1>
                <p class="hero-sub hero-enter-d2">Card NFC + QR Code di meja kasir — pelanggan tap atau scan, halaman Google Review langsung terbuka. Tanpa install aplikasi, tanpa ribet.</p>
                <div class="hero-actions hero-enter-d3">
                    <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="btn-primary" target="_blank">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Pesan via WhatsApp
                    </a>
                    <a href="#cara-kerja" class="btn-secondary">Lihat Cara Kerja</a>
                </div>
                <div class="hero-badges hero-enter-d4">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 18a6 6 0 100-12 6 6 0 000 12z"/><path d="M2 12C2 6.5 6.5 2 12 2m10 10c0 5.5-4.5 10-10 10"/></svg>
                        NFC + QR
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="3"/><circle cx="12" cy="18" r="1"/></svg>
                        Tanpa Aplikasi
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                        Link Fleksibel
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </span>
                </div>
            </div>
            <div class="hero-visual hero-enter-d2">
                <div class="card-showcase">
                    <img src="/img/desain-card.png" alt="Provecho Google Review Card">
                    <svg class="nfc-ring" viewBox="0 0 100 100"><circle cx="50" cy="50"/><circle cx="50" cy="50"/><circle cx="50" cy="50"/></svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Stats ════ -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="reveal">
                <div class="stat-num" data-count="3">3</div>
                <div class="stat-label">detik — Tap to Review</div>
            </div>
            <div class="reveal reveal-d1">
                <div class="stat-num">2</div>
                <div class="stat-label">Akses — NFC + QR Code</div>
            </div>
            <div class="reveal reveal-d2">
                <div class="stat-num">Rp 0</div>
                <div class="stat-label">Biaya Bulanan</div>
            </div>
            <div class="reveal reveal-d3">
                <div class="stat-num">&infin;</div>
                <div class="stat-label">Masa Pakai Card</div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Cara Kerja ════ -->
<section class="sec" id="cara-kerja">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">Cara Kerja</div>
            <h2 class="sec-title">Dari kartu ke bintang 5<br>dalam 4 langkah.</h2>
            <p class="sec-sub">Tidak perlu install apapun. Tidak perlu pengetahuan teknis.</p>
        </div>
        <div class="steps-grid">
            <div class="step reveal">
                <div class="step-num">1</div>
                <h3>Pesan Card</h3>
                <p>Pilih paket Standard atau Premium. Card akrilik premium dikirim langsung ke alamatmu.</p>
            </div>
            <div class="step reveal reveal-d1">
                <div class="step-num">2</div>
                <h3>Tempel di Kasir</h3>
                <p>Taruh card di meja kasir, konter, atau dinding. Desain premium langsung terlihat profesional.</p>
            </div>
            <div class="step reveal reveal-d2">
                <div class="step-num">3</div>
                <h3>Pelanggan Tap / Scan</h3>
                <p>Pelanggan mendekatkan HP ke NFC atau scan QR Code. Tidak perlu install aplikasi apapun.</p>
            </div>
            <div class="step reveal reveal-d3">
                <div class="step-num">4</div>
                <h3>Review Masuk</h3>
                <p>Google Review form langsung terbuka di HP pelanggan. Rating dan ulasan mengalir otomatis.</p>
            </div>
        </div>
    </div>
</section>

<!-- ════ Features ════ -->
<section class="sec" id="fitur" style="padding-top:20px">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">Fitur</div>
            <h2 class="sec-title">Bukan kartu NFC biasa.</h2>
            <p class="sec-sub">Kompetitor jual kartu yang URL-nya mati kalau link berubah. Provecho beda.</p>
        </div>

        <!-- Feature 1 -->
        <div class="feature reveal">
            <div>
                <div class="feature-tag">Fitur 1 / 4</div>
                <h3>Dual Akses — NFC + QR Code</h3>
                <p class="feature-desc">Dua cara review dalam satu card. NFC untuk smartphone modern, QR Code sebagai fallback universal. Tidak ada pelanggan yang tertinggal.</p>
                <ul class="feature-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>NFC tap — tanpa buka kamera</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>QR Code — semua HP bisa</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Chip NTAG213 — tahan bertahun-tahun</li>
                </ul>
            </div>
            <div>
                <div class="mock-phone">
                    <div class="mock-phone-notch"></div>
                    <div class="mock-phone-screen" style="text-align:center;padding-top:24px">
                        <svg width="32" height="32" viewBox="0 0 24 24" style="margin:0 auto"><circle cx="12" cy="12" r="10" fill="#4285F4"/><path d="M12 7v10M7 12h10" stroke="#fff" stroke-width="2"/></svg>
                        <div style="font-size:11px;color:#5f6368;margin-top:8px">Tulis ulasan untuk</div>
                        <div style="font-size:14px;font-weight:700;margin-top:4px;color:#202124">Warung Makan Sederhana</div>
                        <div style="display:flex;gap:6px;justify-content:center;margin-top:16px">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FBBF24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FBBF24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FBBF24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FBBF24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="#FBBF24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                        </div>
                        <div style="margin:16px 12px 0;height:48px;border:1px solid #dadce0;border-radius:8px"></div>
                        <div style="margin-top:12px;display:inline-block;background:#1a73e8;color:#fff;font-size:12px;font-weight:600;padding:8px 28px;border-radius:20px">Posting</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 2 -->
        <div class="feature reverse reveal">
            <div>
                <div class="feature-tag">Fitur 2 / 4</div>
                <h3>Aktivasi Mandiri oleh Pemilik</h3>
                <p class="feature-desc">Pemilik usaha scan card, cari nama usahanya di Google, pilih, selesai. Tidak perlu bantuan teknis, tidak perlu hubungi admin.</p>
                <ul class="feature-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Cari bisnis via Google Places</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Atau paste link Google Maps</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Card langsung aktif, siap dipakai</li>
                </ul>
            </div>
            <div>
                <div class="mockup">
                    <div class="mockup-bar"><span class="mockup-dot"></span><span class="mockup-dot"></span><span class="mockup-dot"></span></div>
                    <div class="mockup-body">
                        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-bottom:6px">provecho.id/c/PV2529C6</div>
                        <div style="background:#fff;border-radius:8px;padding:16px">
                            <div style="font-size:13px;font-weight:700;color:#111;margin-bottom:12px">Aktifkan Kartu Anda</div>
                            <div style="background:#f3f4f6;border-radius:6px;padding:10px 12px;font-size:11px;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:6px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                                Cari nama usaha...
                            </div>
                            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:10px 12px;margin-bottom:8px">
                                <div style="font-size:12px;font-weight:600;color:#166534">Warung Makan Sederhana</div>
                                <div style="font-size:10px;color:#6b7280;margin-top:2px">Jl. Sudirman No. 45, Bandung</div>
                            </div>
                            <div style="background:#1B8C3D;color:#fff;text-align:center;padding:8px;border-radius:6px;font-size:11px;font-weight:600">Aktifkan Card</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 3 -->
        <div class="feature reveal">
            <div>
                <div class="feature-tag">Fitur 3 / 4</div>
                <h3>Link Bisa Diubah Kapan Saja</h3>
                <p class="feature-desc">Pindah lokasi? Ganti profil Google? Ubah link review tanpa ganti card fisik. Ini yang membedakan Provecho dari card NFC murahan di Shopee.</p>
                <ul class="feature-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Update link review kapan saja</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Card fisik tidak perlu diganti</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Bisa nonaktifkan & aktifkan ulang</li>
                </ul>
            </div>
            <div>
                <div class="mockup">
                    <div class="mockup-bar"><span class="mockup-dot"></span><span class="mockup-dot"></span><span class="mockup-dot"></span></div>
                    <div class="mockup-body">
                        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-bottom:6px">Dashboard / Cards / PV2529C6</div>
                        <div style="background:#fff;border-radius:8px;padding:16px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
                                <div style="font-size:13px;font-weight:700;color:#111">Edit Google Review Link</div>
                                <div style="background:#f0fdf4;color:#166534;font-size:10px;font-weight:600;padding:3px 10px;border-radius:999px">Aktif</div>
                            </div>
                            <div style="font-size:10px;color:#6b7280;margin-bottom:4px">URL Tujuan</div>
                            <div style="background:#f3f4f6;border-radius:6px;padding:8px 10px;font-size:10px;color:#374151;word-break:break-all;margin-bottom:10px">https://search.google.com/local/writereview?placeid=ChIJ...</div>
                            <div style="font-size:10px;color:#6b7280;margin-bottom:4px">URL Baru</div>
                            <div style="border:1.5px solid #1B8C3D;border-radius:6px;padding:8px 10px;font-size:10px;color:#111;margin-bottom:10px">https://search.google.com/local/writereview?placeid=ChIJnew...</div>
                            <div style="display:flex;gap:6px">
                                <div style="flex:1;background:#1B8C3D;color:#fff;text-align:center;padding:7px;border-radius:6px;font-size:10px;font-weight:600">Simpan Perubahan</div>
                                <div style="padding:7px 14px;border:1px solid #e5e7eb;border-radius:6px;font-size:10px;color:#6b7280">Batal</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature 4 -->
        <div class="feature reverse reveal">
            <div>
                <div class="feature-tag">Fitur 4 / 4</div>
                <h3>Dashboard Admin Lengkap</h3>
                <p class="feature-desc">Pantau berapa kali card di-tap, lihat card mana yang paling aktif, kelola semua card dari satu tempat. Ekspor data ke CSV kapan saja.</p>
                <ul class="feature-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Statistik scan real-time</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Activity log lengkap</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Ekspor CSV untuk reporting</li>
                </ul>
            </div>
            <div>
                <div class="mockup">
                    <div class="mockup-bar"><span class="mockup-dot"></span><span class="mockup-dot"></span><span class="mockup-dot"></span></div>
                    <div class="mockup-body">
                        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-bottom:10px">Dashboard</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
                            <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:12px">
                                <div style="font-size:10px;color:rgba(255,255,255,.4)">Total Card</div>
                                <div style="font-size:22px;font-weight:800;color:#fff">247</div>
                            </div>
                            <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:12px">
                                <div style="font-size:10px;color:rgba(255,255,255,.4)">Aktif</div>
                                <div style="font-size:22px;font-weight:800;color:#4ade80">183</div>
                            </div>
                        </div>
                        <div style="background:rgba(255,255,255,.06);border-radius:8px;padding:12px">
                            <div style="font-size:10px;color:rgba(255,255,255,.4);margin-bottom:8px">Scan 7 hari terakhir</div>
                            <div style="display:flex;align-items:flex-end;gap:4px;height:48px">
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:60%"></div>
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:45%"></div>
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:80%"></div>
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:55%"></div>
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:90%"></div>
                                <div style="flex:1;background:#1B8C3D;border-radius:3px 3px 0 0;height:70%"></div>
                                <div style="flex:1;background:#22c55e;border-radius:3px 3px 0 0;height:100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ════ Comparison ════ -->
<section class="sec compare" id="perbandingan">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">Perbandingan</div>
            <h2 class="sec-title">Cara manual vs cara cerdas.</h2>
            <p class="sec-sub">Tanpa Provecho, satu review bisa butuh 2-5 menit. Dengan Provecho, 3 detik.</p>
        </div>
        <div class="compare-grid">
            <div class="compare-card manual reveal">
                <h3>Tanpa Provecho</h3>
                <div class="compare-sub">Cara manual yang bikin pelanggan malas</div>
                <ul class="compare-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Minta pelanggan buka Google Maps</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Cari nama usaha secara manual</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Klik menu review, tunggu loading</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Butuh 2-5 menit per review</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Jarang ada yang mau</li>
                </ul>
                <div class="compare-result">2-5 menit</div>
            </div>
            <div class="compare-card smart reveal reveal-d1">
                <h3>Dengan Provecho</h3>
                <div class="compare-sub">Tap sekali, langsung review</div>
                <ul class="compare-list">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Pelanggan tap NFC atau scan QR</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Google Review form langsung terbuka</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Tanpa install aplikasi apapun</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Semua pelanggan bisa, semua HP bisa</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Conversion rate jauh lebih tinggi</li>
                </ul>
                <div class="compare-result">3 detik</div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Pricing ════ -->
<section class="sec" id="harga" style="background:var(--n50)">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">Harga</div>
            <h2 class="sec-title">Investasi kecil, dampak besar.</h2>
            <p class="sec-sub">Beli sekali, pakai selamanya. Tanpa biaya bulanan, tanpa biaya tersembunyi.</p>
        </div>
        <div class="pricing-grid">
            <!-- Standard -->
            <div class="price-card reveal">
                <h3>Standard</h3>
                <div class="price-type">Untuk satu lokasi usaha</div>
                <div class="price-amount">Rp 75K <small>/card</small></div>
                <div class="price-note">Pembelian satu kali. Tanpa langganan.</div>
                <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20paket%20Standard" class="price-cta outline" target="_blank">Pilih Standard</a>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Card akrilik NFC + QR Code</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Aktivasi mandiri</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Link bisa diubah kapan saja</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Akses dashboard admin</li>
                    <li class="disabled"><svg class="x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Bantuan setup Google Business</li>
                    <li class="disabled"><svg class="x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>Prioritas support</li>
                </ul>
            </div>
            <!-- Premium -->
            <div class="price-card popular reveal reveal-d1">
                <div class="price-badge">Paling Populer</div>
                <h3>Premium</h3>
                <div class="price-type">Solusi lengkap untuk yang serius</div>
                <div class="price-amount">Rp 100K <small>/card</small></div>
                <div class="price-note">Termasuk bantuan setup profil Google.</div>
                <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20mau%20pesan%20Provecho%20paket%20Premium" class="price-cta primary" target="_blank">Pilih Premium</a>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Card akrilik NFC + QR Code</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Aktivasi mandiri</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Link bisa diubah kapan saja</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Akses dashboard admin</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Bantuan setup Google Business Profile</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Prioritas support via WhatsApp</li>
                </ul>
            </div>
            <!-- Grosir -->
            <div class="price-card reveal reveal-d2">
                <h3>Grosir</h3>
                <div class="price-type">Untuk reseller & multi-cabang</div>
                <div class="price-amount">Rp 60K <small>/card</small></div>
                <div class="price-note">Minimum order 10 card.</div>
                <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20tertarik%20paket%20Grosir%20Provecho%20(10%2B%20card)" class="price-cta outline" target="_blank">Hubungi Kami</a>
                <div class="price-divider"></div>
                <ul class="price-features">
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Semua fitur Premium</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Harga spesial per unit</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Custom branding (opsional)</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Free ongkir</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Bantuan setup semua card</li>
                    <li><svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Dedicated support</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ════ FAQ ════ -->
<section class="sec" id="faq">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">FAQ</div>
            <h2 class="sec-title">Ada pertanyaan?</h2>
            <p class="sec-sub">Jawaban untuk pertanyaan yang paling sering ditanyakan.</p>
        </div>
        <div class="faq-list" x-data="{active:null}">
            <div class="faq-item reveal" :class="{'open':active===1}">
                <button class="faq-q" @click="active=active===1?null:1"><span>Apa itu Provecho?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Provecho adalah kartu fisik berteknologi NFC + QR Code yang memudahkan pelanggan memberikan Google Review untuk usaha Anda. Cukup taruh di meja kasir — pelanggan tap atau scan, langsung ke halaman review Google.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===2}">
                <button class="faq-q" @click="active=active===2?null:2"><span>Bagaimana cara kerjanya?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Card Provecho berisi chip NFC dan QR Code yang terhubung ke link Google Review usaha Anda. Saat pelanggan mendekatkan HP atau scan QR, browser langsung terbuka di halaman Google Review — tinggal kasih bintang dan tulis ulasan.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===3}">
                <button class="faq-q" @click="active=active===3?null:3"><span>Apakah semua HP bisa tap NFC?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Hampir semua smartphone keluaran 2018 ke atas mendukung NFC. Untuk HP yang belum ada NFC, pelanggan bisa scan QR Code — jadi semua pelanggan tetap bisa memberikan review.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===4}">
                <button class="faq-q" @click="active=active===4?null:4"><span>Bagaimana kalau saya pindah lokasi usaha?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Link review bisa diubah kapan saja melalui dashboard Provecho. Tidak perlu ganti card fisik — cukup update link di sistem. Ini yang membedakan Provecho dari card NFC biasa yang URL-nya hardcoded.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===5}">
                <button class="faq-q" @click="active=active===5?null:5"><span>Apakah ada biaya bulanan?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Tidak. Provecho adalah pembelian satu kali. Tidak ada biaya berlangganan, tidak ada biaya tersembunyi. Bayar sekali, pakai selamanya.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===6}">
                <button class="faq-q" @click="active=active===6?null:6"><span>Berapa lama card bertahan?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Card akrilik Provecho dirancang untuk penggunaan jangka panjang. Material akrilik tahan air dan tahan gores. Chip NFC tidak memerlukan baterai dan bisa berfungsi bertahun-tahun tanpa perawatan.</p></div>
            </div>
            <div class="faq-item reveal" :class="{'open':active===7}">
                <button class="faq-q" @click="active=active===7?null:7"><span>Bagaimana cara pesannya?</span><span class="faq-icon">+</span></button>
                <div class="faq-a"><p>Klik tombol "Pesan via WhatsApp" di halaman ini. Anda akan langsung terhubung dengan tim kami untuk proses pemesanan, pembayaran, dan pengiriman. Pembayaran via QRIS atau transfer bank.</p></div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Testimonials ════ -->
<section class="testi">
    <div class="container">
        <div class="sec-header center reveal">
            <div class="sec-label">Testimoni</div>
            <h2 class="sec-title">Kata mereka yang sudah pakai.</h2>
            <p class="sec-sub">Pemilik usaha dari berbagai kota sudah merasakan dampaknya.</p>
        </div>
    </div>
    <div class="testi-rows">
        <div class="testi-track scroll-left">
            @foreach([
                ['text'=>'Sejak pasang Provecho di kasir, review Google kami naik drastis. Dari 12 review jadi 60+ dalam 2 bulan. Pelanggan tinggal tap, selesai!','name'=>'Sari Dewi','role'=>'Pemilik Warung Makan, Bandung','i'=>'S'],
                ['text'=>'Dulu sering minta pelanggan review tapi jarang ada yang mau. Pakai Provecho, tanpa diminta pun mereka langsung review. Game changer buat bisnis kecil.','name'=>'Agus Pratama','role'=>'Owner Barbershop, Jakarta','i'=>'A'],
                ['text'=>'Worth it banget Rp 75 ribu. Investasi kecil, dampaknya besar ke bisnis. Sekarang rating kami 4.8 di Google Maps.','name'=>'Rina Wahyuni','role'=>'Pemilik Cafe, Surabaya','i'=>'R'],
                ['text'=>'Setup-nya gampang banget. Scan card, cari nama usaha, langsung aktif. Ga perlu panggil teknisi atau apa.','name'=>'Budi Santoso','role'=>'Pemilik Bengkel, Yogyakarta','i'=>'B'],
                ['text'=>'Card-nya bagus, akrilik tebal, ga murahan kayak yang di Shopee. Pelanggan sering nanya ini apa, jadi conversation starter juga.','name'=>'Maya Putri','role'=>'Owner Salon, Semarang','i'=>'M'],
                ['text'=>'Tiap bulan sekarang dapat 20-30 review baru tanpa effort. Ranking Google Maps naik, pelanggan baru datang sendiri.','name'=>'Hendra Wijaya','role'=>'Pemilik Restoran, Malang','i'=>'H'],
            ] as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">"{{ $t['text'] }}"</div>
                <div class="testi-author"><div class="testi-avatar">{{ $t['i'] }}</div><div><div class="testi-name">{{ $t['name'] }}</div><div class="testi-role">{{ $t['role'] }}</div></div></div>
            </div>
            @endforeach
            @foreach([
                ['text'=>'Sejak pasang Provecho di kasir, review Google kami naik drastis. Dari 12 review jadi 60+ dalam 2 bulan. Pelanggan tinggal tap, selesai!','name'=>'Sari Dewi','role'=>'Pemilik Warung Makan, Bandung','i'=>'S'],
                ['text'=>'Dulu sering minta pelanggan review tapi jarang ada yang mau. Pakai Provecho, tanpa diminta pun mereka langsung review. Game changer buat bisnis kecil.','name'=>'Agus Pratama','role'=>'Owner Barbershop, Jakarta','i'=>'A'],
                ['text'=>'Worth it banget Rp 75 ribu. Investasi kecil, dampaknya besar ke bisnis. Sekarang rating kami 4.8 di Google Maps.','name'=>'Rina Wahyuni','role'=>'Pemilik Cafe, Surabaya','i'=>'R'],
                ['text'=>'Setup-nya gampang banget. Scan card, cari nama usaha, langsung aktif. Ga perlu panggil teknisi atau apa.','name'=>'Budi Santoso','role'=>'Pemilik Bengkel, Yogyakarta','i'=>'B'],
                ['text'=>'Card-nya bagus, akrilik tebal, ga murahan kayak yang di Shopee. Pelanggan sering nanya ini apa, jadi conversation starter juga.','name'=>'Maya Putri','role'=>'Owner Salon, Semarang','i'=>'M'],
                ['text'=>'Tiap bulan sekarang dapat 20-30 review baru tanpa effort. Ranking Google Maps naik, pelanggan baru datang sendiri.','name'=>'Hendra Wijaya','role'=>'Pemilik Restoran, Malang','i'=>'H'],
            ] as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">"{{ $t['text'] }}"</div>
                <div class="testi-author"><div class="testi-avatar">{{ $t['i'] }}</div><div><div class="testi-name">{{ $t['name'] }}</div><div class="testi-role">{{ $t['role'] }}</div></div></div>
            </div>
            @endforeach
        </div>
        <div class="testi-track scroll-right">
            @foreach([
                ['text'=>'Punya 3 cabang, semua pakai Provecho. Dashboard-nya enak, bisa pantau semua card dari satu tempat.','name'=>'Dedi Kurniawan','role'=>'Owner Mie Ayam, Solo','i'=>'D'],
                ['text'=>'Awalnya ragu, tapi setelah 2 minggu review nambah 25+. Sekarang malah pesan lagi buat kasir kedua.','name'=>'Linda Susanti','role'=>'Pemilik Toko Roti, Bekasi','i'=>'L'],
                ['text'=>'Yang paling bagus itu link-nya bisa diubah. Dulu pakai card NFC Shopee, begitu profil Google berubah jadi mati.','name'=>'Rizki Aditya','role'=>'Owner Coffee Shop, Depok','i'=>'R'],
                ['text'=>'Pelanggan langsung review tanpa perlu diminta. Cukup taruh di meja, mereka penasaran sendiri terus tap.','name'=>'Fitri Handayani','role'=>'Pemilik Klinik Kecantikan, Tangerang','i'=>'F'],
                ['text'=>'ROI-nya gila. Rp 75 ribu tapi review naik drastis, customer baru datang terus. Best investment buat UMKM.','name'=>'Wahyu Setiawan','role'=>'Owner Laundry, Bogor','i'=>'W'],
                ['text'=>'Material akrilik-nya premium banget. Beda jauh sama stiker NFC yang tipis. Ini keliatan profesional di konter.','name'=>'Dewi Anggraini','role'=>'Pemilik Apotek, Cirebon','i'=>'D'],
            ] as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">"{{ $t['text'] }}"</div>
                <div class="testi-author"><div class="testi-avatar">{{ $t['i'] }}</div><div><div class="testi-name">{{ $t['name'] }}</div><div class="testi-role">{{ $t['role'] }}</div></div></div>
            </div>
            @endforeach
            @foreach([
                ['text'=>'Punya 3 cabang, semua pakai Provecho. Dashboard-nya enak, bisa pantau semua card dari satu tempat.','name'=>'Dedi Kurniawan','role'=>'Owner Mie Ayam, Solo','i'=>'D'],
                ['text'=>'Awalnya ragu, tapi setelah 2 minggu review nambah 25+. Sekarang malah pesan lagi buat kasir kedua.','name'=>'Linda Susanti','role'=>'Pemilik Toko Roti, Bekasi','i'=>'L'],
                ['text'=>'Yang paling bagus itu link-nya bisa diubah. Dulu pakai card NFC Shopee, begitu profil Google berubah jadi mati.','name'=>'Rizki Aditya','role'=>'Owner Coffee Shop, Depok','i'=>'R'],
                ['text'=>'Pelanggan langsung review tanpa perlu diminta. Cukup taruh di meja, mereka penasaran sendiri terus tap.','name'=>'Fitri Handayani','role'=>'Pemilik Klinik Kecantikan, Tangerang','i'=>'F'],
                ['text'=>'ROI-nya gila. Rp 75 ribu tapi review naik drastis, customer baru datang terus. Best investment buat UMKM.','name'=>'Wahyu Setiawan','role'=>'Owner Laundry, Bogor','i'=>'W'],
                ['text'=>'Material akrilik-nya premium banget. Beda jauh sama stiker NFC yang tipis. Ini keliatan profesional di konter.','name'=>'Dewi Anggraini','role'=>'Pemilik Apotek, Cirebon','i'=>'D'],
            ] as $t)
            <div class="testi-card">
                <div class="testi-stars">@for($s=0;$s<5;$s++)<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>@endfor</div>
                <div class="testi-text">"{{ $t['text'] }}"</div>
                <div class="testi-author"><div class="testi-avatar">{{ $t['i'] }}</div><div><div class="testi-name">{{ $t['name'] }}</div><div class="testi-role">{{ $t['role'] }}</div></div></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ════ CTA ════ -->
<section class="cta-sec">
    <div class="container">
        <div class="cta-box reveal">
            <h2>Siap punya lebih banyak<br>Google Review?</h2>
            <p>Investasi Rp 75.000, dampaknya ke bisnis tak terhingga. Gratis konsultasi.</p>
            <a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="btn-white" target="_blank">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pesan Sekarang via WhatsApp
            </a>
            <div class="cta-badges">
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>NFC + QR Code</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Tanpa Biaya Bulanan</span>
                <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>Link Bisa Diubah</span>
            </div>
        </div>
    </div>
</section>

<!-- ════ Footer ════ -->
<footer class="foot">
    <div class="container">
        <div class="foot-grid">
            <div>
                <div class="foot-brand"><img src="/img/logo.png" alt="Provecho"><span>Provecho</span></div>
                <p class="foot-desc">Google Review Card NFC + QR Code untuk UMKM Indonesia. Satu tap, review masuk.</p>
            </div>
            <div>
                <h4>Produk</h4>
                <ul>
                    <li><a href="#fitur">Fitur</a></li>
                    <li><a href="#cara-kerja">Cara Kerja</a></li>
                    <li><a href="#harga">Harga</a></li>
                </ul>
            </div>
            <div>
                <h4>Support</h4>
                <ul>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="https://wa.me/628XXXXXXXXXX" target="_blank">WhatsApp</a></li>
                </ul>
            </div>
            <div>
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="marquee">
        <div class="marquee-track">
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
            <span>PROVECHO</span><span style="color:rgba(255,255,255,.06)">&#10022;</span>
        </div>
    </div>
    <div class="foot-copy">&copy; {{ date('Y') }} Provecho. All rights reserved.</div>
</footer>

<!-- ════ WA Float ════ -->
<a href="https://wa.me/628XXXXXXXXXX?text=Halo%2C%20saya%20tertarik%20dengan%20Provecho%20Google%20Review%20Card" class="wa-float" target="_blank" aria-label="Chat via WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12.05 21.785h-.01a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884zm8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<script>
document.addEventListener('DOMContentLoaded',function(){
    var nav=document.querySelector('.nav');
    window.addEventListener('scroll',function(){nav.classList.toggle('scrolled',window.scrollY>50)},{passive:true});

    var obs=new IntersectionObserver(function(entries){
        entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target)}})
    },{threshold:.12});
    document.querySelectorAll('.reveal').forEach(function(el){obs.observe(el)});
});
</script>
</body>
</html>
