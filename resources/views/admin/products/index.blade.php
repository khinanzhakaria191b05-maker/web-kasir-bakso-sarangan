@extends('layouts.admin')
@section('title', 'Kelola menu')

@section('content')

<style>
    .thumb {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        display: block;
        background: #eef0ed;
    }

    .thumb-empty {
        width: 80px;
        height: 60px;
        border-radius: 6px;
        background: #eef0ed;
        color: var(--muted);
        font-size: .72rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    tr.habis .thumb {
        opacity: .4;
    }

    tr.habis .nama {
        color: var(--muted);
    }

    .form-switch .form-check-input {
        cursor: pointer;
    }

    .form-switch .form-check-input:checked {
        background-color: var(--brand);
        border-color: var(--brand);
    }

    .nav-pills {
        --bs-nav-pills-link-active-bg: var(--brand);
    }

    .nav-pills .nav-link {
        color: #495057;
        font-size: .9rem;
        padding: .35rem .85rem;
    }

    .nav-pills .nav-link.active {
        color: #fff;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="mb-0">Kelola menu</h1>
        <div class="text-muted small">Daftar menu yang tampil di layar kasir.</div>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i>Tambah menu
    </a>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total menu</div>
                <div class="fs-4 fw-semibold">{{ $total }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Dijual</div>
                <div class="fs-4 fw-semibold text-success">{{ $total - $habis }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Habis</div>
                <div class="fs-4 fw-semibold {{ $habis > 0 ? 'text-danger' : '' }}">{{ $habis }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter kategori dan pencarian --}}
<div class="card card-body mb-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <ul class="nav nav-pills gap-1">
            <li class="nav-item">
                <a class="nav-link {{ request('category') ? '' : 'active' }}"
                    href="{{ route('admin.products.index', ['q' => request('q')]) }}">Semua</a>
            </li>
            @foreach($categories as $c)
            <li class="nav-item">
                <a class="nav-link {{ request('category') == $c->id ? 'active' : '' }}"
                    href="{{ route('admin.products.index', ['category' => $c->id, 'q' => request('q')]) }}">{{ $c->name }} ({{ $c->products_count }})</a>
            </li>
            @endforeach
        </ul>

        <form method="GET" class="d-flex gap-2">
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama menu" style="width:220px">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th style="width:100px">Foto</th>
                <th>Menu</th>
                <th>Kategori</th>
                <th class="text-end">Harga</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            @php
            $nVarian = $p->options->where('type', 'varian')->count();
            $nTopping = $p->options->where('type', 'topping')->count();
            @endphp
            <tr class="{{ $p->is_available ? '' : 'habis' }}">
                <td>
                    @if($p->image)
                    <img src="{{ asset('storage/' . $p->image) }}" class="thumb" alt="">
                    @else
                    <div class="thumb-empty">Belum ada foto</div>
                    @endif
                </td>
                <td>
                    <div class="nama fw-semibold">{{ $p->name }}</div>
                    @if($nVarian || $nTopping)
                    <div class="small text-muted">
                        {{ collect([$nVarian ? $nVarian . ' varian' : null, $nTopping ? $nTopping . ' topping' : null])->filter()->implode(' dan ') }}
                    </div>
                    @endif
                </td>
                <td><span class="badge text-bg-light border">{{ $p->category->name }}</span></td>
                <td class="text-end text-nowrap">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.products.toggle', $p) }}">
                        @csrf @method('PATCH')
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="sw{{ $p->id }}" {{ $p->is_available ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small" for="sw{{ $p->id }}">{{ $p->is_available ? 'Dijual' : 'Habis' }}</label>
                        </div>
                    </form>
                </td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-pencil me-1"></i>Ubah
                    </a>
                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="d-inline"
                        onsubmit="return confirm('Hapus menu ini? Foto ikut terhapus.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    @if(request('q') || request('category'))
                    Tidak ada menu yang cocok. Coba kata kunci atau kategori lain.
                    @else
                    Belum ada menu. Klik "Tambah menu" untuk mulai.
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $products->links() }}</div>

@endsection