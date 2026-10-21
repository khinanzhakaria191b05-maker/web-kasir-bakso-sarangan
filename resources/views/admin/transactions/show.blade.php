@extends('layouts.admin')
@section('title', 'Detail Transaksi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    @if($order->status === 'batal')
    <div class="alert alert-danger">
        <strong>TRANSAKSI DIBATALKAN</strong><br>
        Alasan: {{ $order->void_reason }}<br>
        <small>Oleh {{ $order->voidedBy->name ?? '-' }} pada {{ $order->voided_at?->format('d/m/Y H:i') }}</small>
    </div>
    @else
    <form method="POST" action="{{ route('admin.transactions.void', $order) }}" class="card card-body mb-3 border-danger"
        onsubmit="return confirm('Batalkan transaksi ini? Tindakan ini tidak bisa diurungkan.')">
        @csrf @method('PATCH')
        <label class="form-label text-danger fw-semibold">Batalkan transaksi</label>
        <div class="input-group">
            <input type="text" name="void_reason" class="form-control" placeholder="Alasan (mis. salah input menu)" required maxlength="200">
            <button class="btn btn-danger">Batalkan</button>
        </div>
    </form>
    @endif
    <h3 class="mb-0">{{ $order->invoice_no }}</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <a href="{{ route('kasir.struk', $order) }}?print=1" target="_blank" class="btn btn-success"><i class="bi bi-printer me-1"></i>Cetak Struk</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Waktu</dt>
                    <dd class="col-7">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                    <dt class="col-5">Kasir</dt>
                    <dd class="col-7">{{ $order->user->name }}</dd>
                    <dt class="col-5">Tipe</dt>
                    <dd class="col-7">{{ $order->order_type === 'dine_in' ? 'Makan di Sini (Meja '.$order->table_number.')' : 'Bungkus' }}</dd>
                    <dt class="col-5">Bayar</dt>
                    <dd class="col-7">{{ $order->payment_method === 'tunai' ? 'Tunai' : 'QRIS / Transfer' }}</dd>
                    @if($order->payment_method === 'tunai')
                    <dt class="col-5">Diterima</dt>
                    <dd class="col-7">Rp {{ number_format($order->cash_received, 0, ',', '.') }}</dd>
                    <dt class="col-5">Kembali</dt>
                    <dd class="col-7">Rp {{ number_format($order->change_amount, 0, ',', '.') }}</dd>
                    @endif
                    @if($order->note)
                    <dt class="col-5">Catatan</dt>
                    <dd class="col-7">{{ $order->note }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Menu</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $it)
                        <tr>
                            <td>
                                {{ $it->product_name }}
                                @foreach($it->options ?? [] as $o)
                                <div class="small text-muted">+ {{ $o['name'] }}</div>
                                @endforeach
                            </td>
                            <td class="text-end">{{ number_format($it->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $it->qty }}</td>
                            <td class="text-end">{{ number_format($it->price * $it->qty, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection