@extends('layouts.admin')
@section('title', 'Riwayat transaksi')

@section('content')

@php
  $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
  $periode = $from->translatedFormat('d M Y');
  if ($from->toDateString() !== $to->toDateString()) { $periode .= ' – ' . $to->translatedFormat('d M Y'); }
@endphp

<div class="mb-4">
  <h1 class="mb-0">Riwayat transaksi</h1>
  <div class="text-muted small">{{ $periode }}</div>
</div>

{{-- Filter --}}
<form method="GET" class="card card-body mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-6 col-md-2">
      <label class="form-label small mb-1">Dari</label>
      <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label small mb-1">Sampai</label>
      <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label small mb-1">Kasir</label>
      <select name="user_id" class="form-select">
        <option value="">Semua kasir</option>
        @foreach($kasirs as $k)
          <option value="{{ $k->id }}" @selected(request('user_id') == $k->id)>{{ $k->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-2">
      <label class="form-label small mb-1">Pembayaran</label>
      <select name="method" class="form-select">
        <option value="">Semua</option>
        <option value="tunai" @selected(request('method') === 'tunai')>Tunai</option>
        <option value="qris" @selected(request('method') === 'qris')>QRIS</option>
      </select>
    </div>
    <div class="col-12 col-md-3 d-flex gap-2">
      <button class="btn btn-success flex-grow-1">Tampilkan</button>
      <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">Atur ulang</a>
    </div>
  </div>
</form>

{{-- Ringkasan hasil --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-lg-3">
    <div class="card h-100"><div class="card-body">
      <div class="text-muted small mb-1">Jumlah transaksi</div>
      <div class="fs-4 fw-semibold">{{ number_format($orders->total(), 0, ',', '.') }}</div>
    </div></div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100"><div class="card-body">
      <div class="text-muted small mb-1">Total diterima</div>
      <div class="fs-4 fw-semibold text-success">{{ $rp($totalFiltered) }}</div>
    </div></div>
  </div>
  <div class="col-12 col-lg-6 d-flex align-items-center">
    <div class="text-muted small">Transaksi yang dibatalkan tetap tampil di daftar, tapi tidak ikut dijumlahkan.</div>
  </div>
</div>

{{-- Tabel --}}
<div class="table-responsive">
  <table class="table align-middle">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nomor</th>
        <th>Kasir</th>
        <th>Pesanan</th>
        <th class="text-end">Porsi</th>
        <th>Pembayaran</th>
        <th class="text-end">Total</th>
        <th class="text-end">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $o)
        <tr class="{{ $o->status === 'batal' ? 'text-muted' : '' }}">
          <td class="text-nowrap">{{ $o->created_at->format('d/m/Y H:i') }}</td>
          <td class="text-nowrap">{{ $o->invoice_no }}</td>
          <td>{{ $o->user->name }}</td>
          <td class="text-nowrap">{{ $o->order_type === 'dine_in' ? 'Makan di sini, meja ' . $o->table_number : 'Bungkus' }}</td>
          <td class="text-end">{{ $o->porsi }}</td>
          <td>
            @if($o->payment_method === 'tunai')
              <span class="badge text-bg-success">Tunai</span>
            @else
              <span class="badge text-bg-primary">QRIS</span>
            @endif
          </td>
          <td class="text-end text-nowrap">
            @if($o->status === 'batal')
              <span class="badge text-bg-danger me-1">Batal</span>
              <span class="text-decoration-line-through">{{ number_format($o->total, 0, ',', '.') }}</span>
            @else
              <span class="fw-semibold">{{ number_format($o->total, 0, ',', '.') }}</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('admin.transactions.show', $o) }}" class="btn btn-sm btn-outline-success">
              <i class="bi bi-eye me-1"></i>Rincian
            </a>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi yang cocok. Coba ubah tanggal atau filter di atas.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-3">{{ $orders->links() }}</div>

@endsection