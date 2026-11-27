<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk {{ $order->invoice_no }}</title>
    <style>
        @page {
            margin: 14px 12px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .r {
            text-align: right;
        }

        .c {
            text-align: center;
        }

        .b {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        h1 {
            font-size: 12px;
            margin: 0 0 3px;
            text-align: center;
        }

        .sub {
            padding-left: 8px;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @php
    $n = fn ($v) => number_format($v, 0, ',', '.');
    $pct = fn ($v) => rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
    $shop = \App\Models\Setting::get('shop_name', 'MIE AYAM BAKSO SARANGAN');
    @endphp

    <h1>{{ $shop }}</h1>
    @if(\App\Models\Setting::get('shop_address')) <div class="c">{{ \App\Models\Setting::get('shop_address') }}</div> @endif
    @if(\App\Models\Setting::get('shop_phone')) <div class="c">Telp/WA: {{ \App\Models\Setting::get('shop_phone') }}</div> @endif
    @if($order->status === 'batal') <div class="c b" style="margin-top:4px">*** DIBATALKAN ***</div> @endif

    <div class="line"></div>

    <table>
        <tr>
            <td>No</td>
            <td class="r">{{ $order->invoice_no }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="r">{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="r">{{ $order->user->name }}</td>
        </tr>
        <tr>
            <td>Tipe</td>
            <td class="r">{{ $order->order_type === 'dine_in' ? 'Makan di sini, meja ' . $order->table_number : 'Bungkus' }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach($order->items as $it)
        <tr>
            <td colspan="2" class="b">{{ $it->product_name }}</td>
        </tr>
        @foreach($it->options ?? [] as $op)
        <tr>
            <td colspan="2" class="sub">+ {{ $op['name'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td>{{ $it->qty }} x {{ $n($it->price) }}</td>
            <td class="r">{{ $n($it->price * $it->qty) }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="r">Rp {{ $n($order->subtotal) }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td>Diskon{{ $order->discount_type === 'percent' ? ' ' . $pct($order->discount_value) . '%' : '' }}</td>
            <td class="r">- {{ $n($order->discount_amount) }}</td>
        </tr>
        @endif
        @if($order->tax_amount > 0)
        <tr>
            <td>PPN {{ $pct($order->tax_rate) }}%</td>
            <td class="r">{{ $n($order->tax_amount) }}</td>
        </tr>
        @endif
        <tr class="b" style="font-size:11px">
            <td>TOTAL</td>
            <td class="r">Rp {{ $n($order->total) }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="r">{{ $order->payment_method === 'tunai' ? 'Tunai' : 'QRIS / Transfer' }}</td>
        </tr>
        @if($order->payment_method === 'tunai')
        <tr>
            <td>Diterima</td>
            <td class="r">Rp {{ $n($order->cash_received) }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="r">Rp {{ $n($order->change_amount) }}</td>
        </tr>
        @endif
    </table>

    @if($order->note)
    <div class="line"></div>
    <div>Catatan: {{ $order->note }}</div>
    @endif

    <div class="line"></div>
    <div class="c">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih, selamat menikmati!') }}</div>
</body>

</html>