<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Provecho Google Review Card — NFC + QR, Akrilik 10×10 cm</title>
    <meta name="description" content="Card akrilik 10×10 cm dengan chip NFC dan QR Code yang membuka halaman ulasan Google usaha kamu. Gratis dudukan akrilik, datang siap pakai, garansi seumur hidup.">
    <meta property="og:title" content="Provecho Google Review Card">
    <meta property="og:description" content="Tap atau scan, pelanggan langsung ke halaman ulasan Google usaha kamu. Gratis dudukan akrilik, garansi seumur hidup.">
    <meta property="og:image" content="{{ url('/img/produk/1.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <style>
    :root {
        --white: #FFFFFF; --bg: #F8FAFD; --fg: #0B1526; --fg2: #4B5971; --fg3: #8A96A8;
        --blue: #0EA5E9; --blue2: #0284C7; --blue-bg: #EFF8FF; --blue-border: #BAE6FD;
        --green: #10B981; --green-bg: #ECFDF5; --border: #E4EAF2;
        --grad: linear-gradient(120deg, #0EA5E9, #10B981);
        color-scheme: light;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }
    html { font-family: 'Inter', system-ui, sans-serif; color: var(--fg); -webkit-font-smoothing: antialiased; line-height: 1.6 }
    body { background: var(--white) }
    a { color: inherit; text-decoration: none }
    img { max-width: 100%; display: block }
    button { font: inherit; border: 0; background: none; cursor: pointer; color: inherit }
    h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -.025em; line-height: 1.15; text-wrap: balance }
    :focus-visible { outline: 3px solid var(--blue-border); outline-offset: 2px; border-radius: 6px }
    .w { width: 100%; max-width: 1120px; margin: 0 auto; padding-inline: 20px }

    /* top bar */
    .top { position: sticky; top: env(safe-area-inset-top, 0px); z-index: 20; background: rgba(255,255,255,.88); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border) }
    .top .w { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 64px }
    .logo { display: flex; align-items: center; gap: 9px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 18px }
    .logo img { width: 30px; height: 30px }
    .back { font-size: 14px; font-weight: 500; color: var(--fg2); display: inline-flex; align-items: center; gap: 6px }
    .back:hover { color: var(--blue2) }

    .shp { width: 22px; height: 22px; border-radius: 6px; background: #fff; overflow: hidden; flex-shrink: 0; display: inline-block }
    .shp img { width: 100%; height: 100%; object-fit: cover; object-position: top }
    .buy {
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        background: var(--grad); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
        border-radius: 999px; box-shadow: 0 12px 26px -12px rgba(14,165,233,.75); transition: transform .15s, box-shadow .15s;
    }
    .buy:hover { transform: translateY(-1px); box-shadow: 0 16px 30px -12px rgba(14,165,233,.85) }
    .buy-sm { font-size: 14px; padding: 9px 16px 9px 10px }
    .buy-lg { font-size: 16.5px; padding: 15px 26px 15px 16px; width: 100% }

    /* product */
    .product { padding-block: 32px 56px; background: linear-gradient(180deg, var(--blue-bg), var(--white) 70%) }
    .product .w { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); gap: 48px; align-items: start }

    .gallery { position: sticky; top: 88px }
    .slides {
        display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth;
        border-radius: 22px; background: #fff; border: 1px solid var(--border); box-shadow: 0 20px 50px -28px rgba(11,21,38,.35);
        scrollbar-width: none;
    }
    .slides::-webkit-scrollbar { display: none }
    .slides img { flex: 0 0 100%; width: 100%; height: auto; aspect-ratio: 1; object-fit: cover; scroll-snap-align: start }
    .stage { position: relative }
    .nav-btn {
        position: absolute; top: 50%; translate: 0 -50%; width: 42px; height: 42px; border-radius: 50%;
        background: rgba(255,255,255,.92); box-shadow: 0 6px 18px -6px rgba(11,21,38,.35); display: grid; place-items: center; color: var(--fg);
    }
    .nav-btn.prev { left: 12px } .nav-btn.next { right: 12px }
    .nav-btn:disabled { opacity: 0; pointer-events: none }
    .counter { position: absolute; right: 14px; top: 14px; font-size: 12px; font-weight: 600; color: #fff; background: rgba(11,21,38,.6); padding: 3px 10px; border-radius: 999px; font-variant-numeric: tabular-nums }
    .thumbs { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 8px; margin-top: 12px }
    .thumbs button { border-radius: 10px; overflow: hidden; border: 2px solid transparent; opacity: .65; transition: opacity .15s, border-color .15s }
    .thumbs button:hover { opacity: 1 }
    .thumbs button[aria-current="true"] { border-color: var(--blue); opacity: 1 }
    .thumbs img { width: 100%; height: auto; aspect-ratio: 1; object-fit: cover }

    .info { display: flex; flex-direction: column; gap: 22px }
    .eyebrow { align-self: flex-start; display: inline-flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; color: var(--blue2); background: #fff; border: 1px solid var(--blue-border); padding: 5px 12px; border-radius: 999px }
    .eyebrow b { background: var(--grad); color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 11px }
    .info h1 { font-size: clamp(30px, 4vw, 42px); font-weight: 800 }
    .info h1 em { font-style: normal; background: var(--grad); -webkit-background-clip: text; background-clip: text; color: transparent }
    .lead { font-size: 16.5px; color: var(--fg2); max-width: 52ch }

    .facts { display: grid; gap: 10px }
    .fact { display: flex; gap: 12px; align-items: flex-start; padding: 12px 14px; border-radius: 14px; background: #fff; border: 1px solid var(--border) }
    .fact i { width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; display: grid; place-items: center; color: #fff; background: var(--grad) }
    .fact i svg { width: 17px; height: 17px }
    .fact b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px }
    .fact span { font-size: 13.5px; color: var(--fg2) }
    .fact.hl { background: var(--green-bg); border-color: rgba(16,185,129,.3) }

    .buy-box { display: flex; flex-direction: column; gap: 10px; padding: 18px; border-radius: 18px; background: #fff; border: 1px solid var(--border); box-shadow: 0 16px 40px -28px rgba(11,21,38,.4) }
    .buy-note { font-size: 13px; color: var(--fg2); text-align: center }
    .buy-note a { color: var(--blue2); font-weight: 600 }
    .buy-note a:hover { text-decoration: underline }

    /* details */
    .details { padding-block: 56px 72px; background: var(--bg); border-top: 1px solid var(--border) }
    .details .w { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px }
    .box { background: #fff; border: 1px solid var(--border); border-radius: 18px; padding: 24px }
    .box h2 { font-size: 20px; margin-bottom: 16px }
    .box.wide { grid-column: 1 / -1 }
    .spec { display: grid; grid-template-columns: max-content 1fr; gap: 0 24px }
    .spec dt, .spec dd { padding: 11px 0; border-top: 1px solid #EEF2F7; font-size: 14.5px }
    .spec dt:first-of-type, .spec dt:first-of-type + dd { border-top: 0; padding-top: 0 }
    .spec dt { color: var(--fg3); font-weight: 500 }
    .spec dd { font-weight: 600 }
    .list { display: grid; gap: 12px; list-style: none }
    .list li { display: flex; gap: 10px; font-size: 14.5px; color: var(--fg2) }
    .list li::before { content: ''; width: 20px; height: 20px; flex-shrink: 0; margin-top: 2px; border-radius: 50%; background: var(--green-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310B981' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6 9 17l-5-5'/%3E%3C/svg%3E") center / 12px no-repeat }
    .list li b { color: var(--fg); font-weight: 600 }
    .steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; list-style: none; counter-reset: s }
    .steps li { counter-increment: s; padding: 16px; border-radius: 14px; background: var(--bg); border: 1px solid var(--border) }
    .steps li::before { content: counter(s); display: grid; place-items: center; width: 28px; height: 28px; border-radius: 50%; background: var(--grad); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 14px; margin-bottom: 10px }
    .steps b { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; margin-bottom: 2px }
    .steps span { font-size: 13.5px; color: var(--fg2) }

    .end { padding-block: 56px; text-align: center }
    .end h2 { font-size: clamp(24px, 3vw, 32px); margin-bottom: 10px }
    .end p { color: var(--fg2); margin-bottom: 22px }
    .end .buy-lg { width: auto }

    .foot { padding-block: 24px; border-top: 1px solid var(--border); font-size: 13px; color: var(--fg3); text-align: center }

    .sticky-buy { display: none }

    @media (max-width: 900px) {
        .product .w { grid-template-columns: minmax(0, 1fr); gap: 28px }
        .gallery { position: static }
        .details .w { grid-template-columns: minmax(0, 1fr) }
        .steps { grid-template-columns: minmax(0, 1fr) }
    }
    @media (max-width: 640px) {
        .product { padding-top: 16px }
        .nav-btn { display: none }
        .thumbs { gap: 6px }
        .top .buy-sm { display: none }
        .buy-box .buy-lg { display: none }
        .sticky-buy {
            display: block; position: fixed; left: 0; right: 0; bottom: 0; z-index: 30;
            padding: 10px 16px calc(10px + env(safe-area-inset-bottom, 0px)); background: rgba(255,255,255,.95);
            backdrop-filter: blur(10px); border-top: 1px solid var(--border);
        }
        .foot { padding-bottom: 96px }
        .spec { grid-template-columns: minmax(0, 1fr); }
        .spec dt { padding-bottom: 0; }
        .spec dd { border-top: 0; padding-top: 2px }
    }
    @media (prefers-reduced-motion: reduce) {
        .slides { scroll-behavior: auto }
        .buy { transition: none }
    }
    </style>
</head>
<body>

@php
    $shopee = 'https://shopee.co.id/product/607077792/53118914602/';
    $wa = 'https://wa.me/6283842843671?text=' . rawurlencode('Halo, saya mau tanya tentang Provecho Card');
    $shp = '<span class="shp"><img src="/img/shopee-seeklogo.png" alt=""></span>';
    $photos = [
        'Provecho Google Review Card berdiri di meja dengan dudukan akrilik',
        'Cara kerja: tap atau scan, form ulasan Google terbuka, pelanggan kirim ulasan',
        'Card bisa ditempel di kaca atau berdiri di meja dengan dudukan akrilik',
        'Manfaat ulasan Google: lebih mudah ditemukan dan lebih dipercaya',
        'Cocok untuk kafe, restoran, barbershop, salon, bengkel, dan laundry',
        'Isi paket: papan akrilik 10×10 cm, chip NFC dan QR Code, dudukan akrilik',
        'Garansi seumur hidup: QR atau NFC bermasalah diganti produk baru',
    ];
@endphp

<header class="top">
    <div class="w">
        <a href="{{ route('landing') }}" class="logo"><img src="/img/logo.png" alt="">Provecho</a>
        <div style="display:flex;align-items:center;gap:16px">
            <a href="{{ route('landing') }}" class="back">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Beranda
            </a>
            <a href="{{ $shopee }}" target="_blank" rel="noopener" class="buy buy-sm">{!! $shp !!}Beli di Shopee</a>
        </div>
    </div>
</header>

<main>
<section class="product">
    <div class="w">

        <div class="gallery" id="gallery">
            <div class="stage">
                <div class="slides" id="slides" tabindex="0" aria-label="Foto produk, geser untuk melihat foto lain">
                    @foreach($photos as $i => $alt)
                        <img src="/img/produk/{{ $i + 1 }}.jpg" alt="{{ $alt }}" width="1200" height="1200" @if($i > 0) loading="lazy" @endif>
                    @endforeach
                </div>
                <button type="button" class="nav-btn prev" id="prev" aria-label="Foto sebelumnya" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" class="nav-btn next" id="next" aria-label="Foto berikutnya">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <span class="counter" id="counter">1 / {{ count($photos) }}</span>
            </div>
            <div class="thumbs" id="thumbs">
                @foreach($photos as $i => $alt)
                    <button type="button" data-i="{{ $i }}" aria-label="Foto {{ $i + 1 }}" @if($i === 0) aria-current="true" @endif>
                        <img src="/img/produk/t{{ $i + 1 }}.jpg" alt="" width="160" height="160">
                    </button>
                @endforeach
            </div>
        </div>

        <div class="info">
            <span class="eyebrow"><b>NFC + QR</b> Card ulasan Google</span>
            <h1>Provecho Google Review Card <em>10×10 cm</em></h1>
            <p class="lead">Papan akrilik dengan chip NFC dan QR Code. Pelanggan tap HP atau scan QR, halaman ulasan Google usaha kamu langsung terbuka. Tanpa aplikasi, tanpa login, tanpa biaya bulanan.</p>

            <div class="facts">
                <div class="fact">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9a7 7 0 0 1 0 6"/><path d="M8.5 7a11 11 0 0 1 0 10"/><rect x="12" y="3" width="9" height="18" rx="2"/></svg></i>
                    <div><b>Tap NFC atau scan QR</b><span>HP dengan NFC cukup ditempel, HP lain tinggal arahkan kamera ke QR.</span></div>
                </div>
                <div class="fact">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></i>
                    <div><b>Cetak UV, tahan lama</b><span>Warna menyatu dengan akrilik, tidak mudah pudar, dan aman dilap saat dibersihkan.</span></div>
                </div>
                <div class="fact">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M7 21 10 6h4l3 15"/></svg></i>
                    <div><b>Gratis dudukan akrilik</b><span>Bisa berdiri di meja kasir, atau ditempel di kaca, dinding, dan etalase.</span></div>
                </div>
                <div class="fact">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></i>
                    <div><b>Datang siap pakai</b><span>Kami hubungkan card ke halaman ulasan Google usahamu sebelum dikirim.</span></div>
                </div>
                <div class="fact hl">
                    <i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg></i>
                    <div><b>Garansi seumur hidup</b><span>QR atau chip NFC cacat produksi? Kami ganti dengan produk baru.</span></div>
                </div>
            </div>

            <div class="buy-box">
                <a href="{{ $shopee }}" target="_blank" rel="noopener" class="buy buy-lg">{!! $shp !!}Beli di Shopee</a>
                <p class="buy-note">Tulis nama usaha atau link Google Maps di catatan pesanan. Ada pertanyaan? <a href="{{ $wa }}" target="_blank" rel="noopener">Chat CS WhatsApp</a></p>
            </div>
        </div>

    </div>
</section>

<section class="details">
    <div class="w">
        <div class="box">
            <h2>Spesifikasi</h2>
            <dl class="spec">
                <dt>Ukuran</dt><dd>10 × 10 cm</dd>
                <dt>Bahan</dt><dd>Akrilik premium</dd>
                <dt>Cetak</dt><dd>UV print, tidak mudah pudar dan aman dilap</dd>
                <dt>Teknologi</dt><dd>Chip NFC + QR Code</dd>
                <dt>Bisa dipakai</dt><dd>Semua HP lewat QR, HP dengan NFC lewat tap</dd>
                <dt>Pemasangan</dt><dd>Berdiri dengan dudukan, atau ditempel</dd>
                <dt>Biaya bulanan</dt><dd>Tidak ada</dd>
                <dt>Garansi</dt><dd>Seumur hidup untuk cacat produksi QR dan NFC</dd>
            </dl>
        </div>

        <div class="box">
            <h2>Isi paket</h2>
            <ul class="list">
                <li><span><b>1 papan akrilik 10×10 cm</b> dengan chip NFC dan QR Code</span></li>
                <li><span><b>1 dudukan akrilik</b>, gratis, supaya card bisa berdiri di meja</span></li>
                <li><span><b>Sudah terhubung</b> ke halaman ulasan Google usaha kamu</span></li>
            </ul>
            <h2 style="margin-top:26px">Garansi seumur hidup</h2>
            <ul class="list">
                <li><span>QR atau chip NFC tidak berfungsi karena cacat produksi</span></li>
                <li><span>Ajukan klaim lewat chat Shopee atau WhatsApp</span></li>
                <li><span>Kami kirim produk pengganti yang baru</span></li>
            </ul>
        </div>

        <div class="box wide">
            <h2>Cara pesan</h2>
            <ol class="steps">
                <li><b>Checkout di Shopee</b><span>Bayar dengan metode apa pun yang ada di Shopee dan dapat perlindungan pembeli.</span></li>
                <li><b>Tulis nama usaha</b><span>Cantumkan nama usaha atau link Google Maps di catatan pesanan.</span></li>
                <li><b>Terima dan pajang</b><span>Card kami aktifkan sebelum dikirim. Begitu sampai, tinggal taruh di meja kasir.</span></li>
            </ol>
        </div>
    </div>
</section>

<section class="end">
    <div class="w">
        <h2>Siap kumpulkan lebih banyak ulasan?</h2>
        <p>Sekali beli, tanpa biaya bulanan, bergaransi seumur hidup.</p>
        <a href="{{ $shopee }}" target="_blank" rel="noopener" class="buy buy-lg">{!! $shp !!}Beli di Shopee</a>
    </div>
</section>
</main>

<footer class="foot">
    <div class="w">© {{ date('Y') }} Provecho · <a href="{{ route('landing') }}">Kembali ke beranda</a></div>
</footer>

<div class="sticky-buy">
    <a href="{{ $shopee }}" target="_blank" rel="noopener" class="buy buy-lg">{!! $shp !!}Beli di Shopee</a>
</div>

<script>
(function () {
    var slides = document.getElementById('slides');
    var thumbs = [].slice.call(document.querySelectorAll('#thumbs button'));
    var prev = document.getElementById('prev'), next = document.getElementById('next');
    var counter = document.getElementById('counter');
    var n = thumbs.length, cur = 0;

    function go(i) { slides.scrollTo({ left: i * slides.clientWidth }); }
    function mark(i) {
        cur = i;
        thumbs.forEach(function (t, j) { t.setAttribute('aria-current', j === i ? 'true' : 'false'); });
        prev.disabled = i === 0; next.disabled = i === n - 1;
        counter.textContent = (i + 1) + ' / ' + n;
    }

    thumbs.forEach(function (t, i) { t.addEventListener('click', function () { go(i); }); });
    prev.addEventListener('click', function () { go(cur - 1); });
    next.addEventListener('click', function () { go(cur + 1); });
    slides.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' && cur < n - 1) go(cur + 1);
        if (e.key === 'ArrowLeft' && cur > 0) go(cur - 1);
    });
    slides.addEventListener('scroll', function () {
        var i = Math.round(slides.scrollLeft / slides.clientWidth);
        if (i !== cur) mark(i);
    }, { passive: true });
})();
</script>
</body>
</html>
