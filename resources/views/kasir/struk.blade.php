<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk {{ $order->invoice_no }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            width: 72mm;
            margin: 0 auto;
            padding: 4mm 0;
            font: 12px/1.4 'Courier New', monospace;
            color: #000;
        }

        .c {
            text-align: center;
        }

        .r {
            text-align: right;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        hr {
            border: 0;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .sub {
            padding-left: 8px;
            font-size: 11px;
        }

        h1 {
            font-size: 15px;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="c">
        <h1>{{ $shopName }}</h1>
        @if(\App\Models\Setting::get('shop_address')) <div>{{ \App\Models\Setting::get('shop_address') }}</div> @endif
        @if(\App\Models\Setting::get('shop_phone')) <div>Telp/WA: {{ \App\Models\Setting::get('shop_phone') }}</div> @endif
        @if($order->status === 'batal') <div><strong>*** DIBATALKAN ***</strong></div> @endif
    </div>
    <div class="c">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih, selamat menikmati!') }}</div>
    <hr>
    <div class="row"><span>No</span><span>{{ $order->invoice_no }}</span></div>
    <div class="row"><span>Tanggal</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="row"><span>Kasir</span><span>{{ $order->user->name }}</span></div>
    <div class="row">
        <span>Tipe</span>
        <span>{{ $order->order_type === 'dine_in' ? 'Makan di Sini - Meja '.$order->table_number : 'Bungkus' }}</span>
    </div>
    <hr>

    @foreach($order->items as $it)
    <div>{{ $it->product_name }}</div>
    @foreach($it->options ?? [] as $o)
    <div class="sub">+ {{ $o['name'] }}@if($o['price'] > 0) ({{ number_format($o['price'], 0, ',', '.') }})@endif</div>
    @endforeach
    <div class="row">
        <span>{{ $it->qty }} x {{ number_format($it->price, 0, ',', '.') }}</span>
        <span>{{ number_format($it->price * $it->qty, 0, ',', '.') }}</span>
    </div>
    @endforeach

    <hr>
    <div class="row"><strong>TOTAL</strong><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
    <div class="row"><span>Bayar</span><span>{{ $order->payment_method === 'tunai' ? 'Tunai' : 'QRIS/Transfer' }}</span></div>
    @if($order->payment_method === 'tunai')
    <div class="row"><span>Diterima</span><span>Rp {{ number_format($order->cash_received, 0, ',', '.') }}</span></div>
    <div class="row"><span>Kembali</span><span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span></div>
    @endif

    @if($order->note)
    <hr>
    <div>Catatan: {{ $order->note }}</div>
    @endif

    <hr>
    <div class="c">Terima kasih,<br>selamat menikmati!</div>

    @if(request('print'))
    <script>
        window.onload = () => window.print();
    </script>
    @endif
</body>

</html>