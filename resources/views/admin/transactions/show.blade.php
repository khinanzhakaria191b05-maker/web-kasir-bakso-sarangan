@extends('layouts.admin')
@section('title', 'Rincian ' . $order->invoice_no)

@section('content')

@php
$rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
$pct = fn ($n) => rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
@endphp

{{-- Tombol atas --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ url()->previous() === url()->current() ? route('admin.transactions.index') : url()->previous() }}"
        class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('kasir.struk', $order) }}?print=1" target="_blank" class="btn btn-outline-success btn-sm">
            <i class="bi bi-printer me-1"></i>Cetak ulang struk
        </a>
        <a href="{{ route('admin.transactions.pdf', $order) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>Unduh PDF
        </a>
    </div>
</div>

<div class="mb-4">
    <h1 class="mb-1">
        {{ $order->invoice_no }}
        @if($order->status === 'batal')
        <span class="badge text-bg-danger align-middle fs-6">Dibatalkan</span>
        @endif
    </h1>
    <div class="text-muted small">{{ $order->created_at->translatedFormat('l, j F Y') }}, pukul {{ $order->created_at->format('H:i') }}</div>
</div>

@if($order->status === 'batal')
<div class="alert alert-danger">
    <div class="fw-semibold">Transaksi ini dibatalkan</div>
    <div>Alasan: {{ $order->void_reason }}</div>
    <div class="small mt-1">Oleh {{ $order->voidedBy->name ?? 'akun yang sudah dihapus' }}, {{ $order->voided_at?->format('d/m/Y H:i') }}</div>
</div>
@endif

<div class="row g-3">

    {{-- Daftar pesanan --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Daftar pesanan</div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $it)
                        <tr>
                            <td>
                                {{ $it->product_name }}
                                @foreach($it->options ?? [] as $op)
                                <div class="small text-muted">+ {{ $op['name'] }}</div>
                                @endforeach
                            </td>
                            <td class="text-end">{{ number_format($it->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ $it->qty }}</td>
                            <td class="text-end">{{ number_format($it->price * $it->qty, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Subtotal</td>
                            <td class="text-end">{{ $rp($order->subtotal) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr class="text-danger">
                            <td colspan="3" class="text-end">Diskon{{ $order->discount_type === 'percent' ? ' ' . $pct($order->discount_value) . '%' : '' }}</td>
                            <td class="text-end">- {{ $rp($order->discount_amount) }}</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td colspan="3" class="text-end text-muted">PPN {{ $pct($order->tax_rate) }}%</td>
                            <td class="text-end">{{ $rp($order->tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="fs-5">
                            <td colspan="3" class="text-end fw-semibold">Total</td>
                            <td class="text-end fw-semibold text-success">{{ $rp($order->total) }}</td>
                        </tr>
                        @if($order->payment_method === 'tunai')
                        <tr>
                            <td colspan="3" class="text-end text-muted">Uang diterima</td>
                            <td class="text-end">{{ $rp($order->cash_received) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Kembalian</td>
                            <td class="text-end">{{ $rp($order->change_amount) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Informasi --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Informasi</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="text-muted">Kasir</span><span class="text-end">{{ $order->user->name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="text-muted">Pesanan</span>
                    <span class="text-end">{{ $order->order_type === 'dine_in' ? 'Makan di sini, meja ' . $order->table_number : 'Bungkus' }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="text-muted">Pembayaran</span>
                    <span>
                        @if($order->payment_method === 'tunai')
                        <span class="badge text-bg-success">Tunai</span>
                        @else
                        <span class="badge text-bg-primary">QRIS / transfer</span>
                        @endif
                    </span>
                </li>
                @if($order->note)
                <li class="list-group-item d-flex justify-content-between gap-3">
                    <span class="text-muted">Catatan</span><span class="text-end">{{ $order->note }}</span>
                </li>
                @endif
            </ul>
        </div>

        @if($order->status !== 'batal')
        <div class="card border-danger">
            <div class="card-body">
                <div class="fw-semibold text-danger mb-1">Batalkan transaksi</div>
                <div class="small text-muted mb-3">Transaksi tetap tercatat di riwayat, tapi tidak dihitung di laporan. Tindakan ini tidak bisa diurungkan.</div>
                <form method="POST" action="{{ route('admin.transactions.void', $order) }}"
                    onsubmit="return confirm('Batalkan transaksi ini?')">
                    @csrf @method('PATCH')
                    <input type="text" name="void_reason" maxlength="200" required class="form-control mb-2"
                        placeholder="Alasan, misalnya salah input menu">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Batalkan transaksi</button>
                </form>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection