@extends('layouts.admin')
@section('title', 'Kelola Menu')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Kelola Menu</h3>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Tambah Menu</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td>
                        @if($p->image)
                        <img src="{{ asset('storage/'.$p->image) }}" width="56" height="56" class="rounded object-fit-cover">
                        @else
                        <div class="bg-secondary-subtle rounded" style="width:56px;height:56px"></div>
                        @endif
                    </td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name }}</td>
                    <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $p) }}">
                            @csrf @method('PATCH')
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" {{ $p->is_available ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label small">{{ $p->is_available ? 'Aktif' : 'Habis' }}</label>
                            </div>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada menu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection