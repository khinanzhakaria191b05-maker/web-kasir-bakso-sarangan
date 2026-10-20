@extends('layouts.admin')
@section('title', $product->exists ? 'Edit Menu' : 'Tambah Menu')
@section('content')
<h3 class="mb-3">{{ $product->exists ? 'Edit Menu' : 'Tambah Menu' }}</h3>

<form method="POST" enctype="multipart/form-data" class="card card-body" style="max-width:600px"
    action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Nama Menu</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="price" min="0" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Foto (maks 2MB)</label>
        @if($product->image)
        <div class="mb-2"><img src="{{ asset('storage/'.$product->image) }}" width="100" class="rounded"></div>
        @endif
        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    @php
    $selected = old('options', $product->exists ? $product->options->pluck('id')->all() : []);
    @endphp

    <div class="mb-3">
        <label class="form-label">Varian & Topping untuk menu ini</label>
        @if($options->isEmpty())
        <div class="text-muted small">
            Belum ada. <a href="{{ route('admin.options.index') }}">Buat dulu di sini</a>.
        </div>
        @else
        <div class="border rounded p-2" style="max-height:220px; overflow:auto">
            @foreach($options as $o)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="options[]" value="{{ $o->id }}" id="opt{{ $o->id }}" @checked(in_array($o->id, $selected))>
                <label class="form-check-label" for="opt{{ $o->id }}">
                    {{ $o->name }}
                    <span class="badge {{ $o->type === 'varian' ? 'text-bg-info' : 'text-bg-warning' }}">{{ $o->type }}</span>
                    @if($o->type === 'topping') <small class="text-muted">+Rp {{ number_format($o->price, 0, ',', '.') }}</small> @endif
                </label>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection