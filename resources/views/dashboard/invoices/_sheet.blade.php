{{-- Isi nota, dipakai bersama oleh PDF (dompdf) dan preview di browser. --}}
@php $rp = fn ($n) => \App\Models\Invoice::rupiah($n); @endphp
<style>
.nota { font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; color: #111827; line-height: 1.4; }
.nota table { width: 100%; border-collapse: collapse; }
/* Netralkan gaya tabel global dashboard; aturan di bawah lebih spesifik. */
.nota th, .nota td { padding: 0; border: 0; background: none; color: inherit; letter-spacing: normal; white-space: normal; vertical-align: top; }
.nota tr:hover { background: none; }
.nota .head { border-bottom: 2px solid {{ $brand['color'] }}; }
.nota .head td { vertical-align: middle; padding-bottom: 8px; }
.nota .logo { height: 38px; }
.nota .nota-brand { font-size: 12pt; font-weight: bold; }
.nota .title b { font-size: 15pt; color: {{ $brand['color'] }}; letter-spacing: 1px; }
.nota .muted { color: #6B7280; }
.nota .info { margin: 12px 0; }
.nota .info td { vertical-align: top; padding: 1px 0; }
.nota .items th { background: {{ $brand['soft'] }}; text-align: left; font-size: 8pt; text-transform: uppercase; padding: 6px 8px; }
.nota .items td { padding: 6px 8px; border-bottom: 1px solid #E5E7EB; }
.nota .sum { width: 55%; margin-left: 45%; margin-top: 8px; }
.nota .sum td { padding: 3px 8px; }
.nota .sum .grand td { border-top: 2px solid #111827; font-size: 12pt; font-weight: bold; padding-top: 6px; }
.nota .notes { margin-top: 14px; padding: 8px 10px; background: #F9FAFB; border-left: 3px solid {{ $brand['color'] }}; }
.nota .foot { margin-top: 22px; text-align: center; font-size: 8pt; color: #6B7280; }
.nota .mono { font-family: DejaVu Sans Mono, monospace; }
.nota .nowrap { white-space: nowrap; }
/* Ditaruh paling akhir dan sama spesifiknya dengan ".nota .items th", supaya judul kolom Harga ikut rata kanan. */
.nota .right, .nota .items .right { text-align: right; }
</style>

<div class="nota">
    <table class="head">
        <tr>
            <td><img class="logo" src="{{ $brand['logo'] }}" alt=""><br><span class="nota-brand">{{ $brand['name'] }}</span></td>
            <td class="title right"><b>NOTA</b><br><span class="mono nowrap">{{ $invoice->number }}</span></td>
        </tr>
    </table>

    <table class="info">
        <tr>
            <td style="width:50%">
                <span class="muted">Kepada</span><br>
                <b>{{ $invoice->buyer_name }}</b><br>
                {{ $invoice->buyer_phone }}
            </td>
            <td class="right">
                <span class="muted">Tanggal</span> {{ $invoice->created_at->translatedFormat('d F Y') }}<br>
                <span class="muted">Pembayaran</span> {{ \App\Models\Invoice::PAYMENT_METHODS[$invoice->payment_method] ?? $invoice->payment_method }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead><tr><th style="width:8%">#</th><th>Item</th><th class="right">Harga</th></tr></thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>Card Google Review <span class="mono">{{ $item->card_id }}</span></td>
                <td class="right">{{ $rp($item->price) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="sum">
        <tr><td>Subtotal ({{ $invoice->items->count() }} card)</td><td class="right">{{ $rp($invoice->subtotal) }}</td></tr>
        @if($invoice->discount)<tr><td>Diskon</td><td class="right">-{{ $rp($invoice->discount) }}</td></tr>@endif
        <tr class="grand"><td>Total</td><td class="right">{{ $rp($invoice->total) }}</td></tr>
    </table>

    @if($invoice->notes)
    <div class="notes">{{ $invoice->notes }}</div>
    @endif

    <div class="foot">
        Simpan nota ini. Untuk klaim garansi, ganti lokasi card, atau pertanyaan lain,
        hubungi kami di <b>{{ \App\Models\Invoice::CONTACT_PHONE }}</b> dan sebutkan nomor nota.<br>
        Terima kasih telah berbelanja di {{ $brand['name'] }}.
    </div>
</div>
