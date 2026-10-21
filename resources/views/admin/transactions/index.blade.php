@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')
@section('content')
<h3 class="mb-3">Riwayat Transaksi</h3>

<form method="GET" class="row g-2 align-items-end mb-3">
    <div class="col-6 col-md-2">
        <label class="form-label small mb-0">Dari</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-2">
        <label class="form-label small mb-0">Sampai</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small mb-0">Kasir</label>
        <select name="user_id" class="form-select">
            <option value="">Semua kasir</option>
            @foreach($kasirs as $k)
            <option value="{{ $k->id }}" @selected(request('user_id')==$k->id)>{{ $k->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-2">
        <label class="form-label small mb-0">Metode</label>
        <select name="method" class="form-select">
            <option value="">Semua</option>
            <option value="tunai" @selected(request('method')==='tunai' )>Tunai</option>
            <option value="qris" @selected(request('method')==='qris' )>QRIS</option>
        </select>
    </div>
    <div class="col-12 col-md-3 d-flex gap-2">
        <button class="btn btn-success flex-grow-1">Filter</button>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="alert alert-success py-2 d-flex justify-content-between">
    <span>{{ $orders->total() }} transaksi ditemukan</span>
    <strong>Total: Rp {{ number_format($totalFiltered, 0, ',', '.') }}</strong>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Invoice</th>
                    <th>Kasir</th>
                    <th>Tipe</th>
                    <th class="text-end">Porsi</th>
                    <th>Bayar</th>
                    <th class="text-end">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td class="text-nowrap">{{ $o->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $o->invoice_no }}</td>
                    <td>{{ $o->user->name }}</td>
                    <td>{{ $o->order_type === 'dine_in' ? 'Meja '.$o->table_number : 'Bungkus' }}</td>
                    <td class="text-end">{{ $o->porsi }}</td>
                    <td><span class="badge {{ $o->payment_method === 'tunai' ? 'text-bg-success' : 'text-bg-primary' }}">{{ strtoupper($o->payment_method) }}</span></td>
                    <td class="text-end fw-semibold">
                        @if($o->status === 'batal')
                        <span class="badge text-bg-danger">BATAL</span>
                        <del class="text-muted">{{ number_format($o->total, 0, ',', '.') }}</del>
                        @else
                        {{ number_format($o->total, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-end fw-semibold">{{ number_format($o->total, 0, ',', '.') }}</td>
                    <td class="text-end"><a href="{{ route('admin.transactions.show', $o) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $orders->links() }}</div>
@endsection