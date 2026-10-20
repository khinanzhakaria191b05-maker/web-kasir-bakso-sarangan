@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<h3 class="mb-3">Kategori Menu</h3>

<form method="POST" action="{{ route('admin.categories.store') }}" class="row g-2 mb-4">
    @csrf
    <div class="col-12 col-md-6">
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama kategori baru (mis. Bakso)" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-auto"><button class="btn btn-success">Tambah</button></div>
</form>

<div class="card">
    <ul class="list-group list-group-flush">
        @forelse($categories as $c)
        <li class="list-group-item">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form method="POST" action="{{ route('admin.categories.update', $c) }}" class="d-flex gap-2 flex-grow-1">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $c->name }}" class="form-control" required>
                    <button class="btn btn-outline-primary">Simpan</button>
                </form>
                <span class="badge text-bg-secondary">{{ $c->products_count }} menu</span>
                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </li>
        @empty
        <li class="list-group-item text-muted">Belum ada kategori.</li>
        @endforelse
    </ul>
</div>
@endsection