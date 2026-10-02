    .card-item {
        width: 100mm;
        height: 100mm;
        position: relative;
        border-radius: 5mm;
        overflow: hidden;
        background: url('/img/review.png') center/cover no-repeat;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .qr-overlay {
        position: absolute;
        right: 9%;
        top: 63%;
        width: 25%;
        aspect-ratio: 1;
        padding: 0;
    }
    .qr-overlay svg { width: 100%; height: 100%; display: block; }

    .id-overlay {
        position: absolute;
        bottom: 0.8%;
        left: 0;
        right: 0;
        text-align: center;
        font-family: 'Inter', sans-serif;
        font-size: 4.5pt;
        font-weight: 500;
        color: #8c8686;
        letter-spacing: .06em;
    }
