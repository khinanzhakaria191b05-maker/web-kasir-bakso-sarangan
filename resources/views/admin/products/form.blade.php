@extends('layouts.admin')
@section('title', $product->exists ? 'Ubah menu' : 'Tambah menu')

@section('content')

@php
$selected = old('options', $product->exists ? $product->options->pluck('id')->all() : []);
$varian = $options->where('type', 'varian');
$topping = $options->where('type', 'topping');
@endphp

<style>
    .preview {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        border-radius: 8px;
        background: #eef0ed;
        display: block;
        border: 1px solid var(--line);
    }

    .preview-empty {
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: 8px;
        background: #f1f3f2;
        border: 1px dashed #b8c2bb;
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .9rem;
    }

    .form-check-input:checked {
        background-color: var(--brand);
        border-color: var(--brand);
    }
</style>

<a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
    <i class="bi bi-arrow-left me-1"></i>Kembali
</a>

<h1 class="mb-4">{{ $product->exists ? 'Ubah menu' : 'Tambah menu' }}</h1>

<form method="POST" enctype="multipart/form-data"
    action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif

    <div class="row g-3">

        {{-- Kiri --}}
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-header">Informasi menu</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama menu</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih kategori</option>
                                @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Harga</label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" min="0" value="{{ old('price', $product->price) }}"
                                    class="form-control @error('price') is-invalid @enderror" required>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Varian dan topping</div>
                <div class="card-body">
                    <div class="text-muted small mb-3">
                        Centang pilihan yang berlaku untuk menu ini. Daftarnya diatur di halaman
                        <a href="{{ route('admin.options.index') }}">Varian dan topping</a>.
                    </div>

                    @if($options->isEmpty())
                    <div class="text-muted">Belum ada varian atau topping.</div>
                    @else
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold small mb-2">Varian <span class="text-muted fw-normal">(pilih satu, gratis)</span></div>
                            @forelse($varian as $o)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options[]" value="{{ $o->id }}" id="opt{{ $o->id }}" @checked(in_array($o->id, $selected))>
                                <label class="form-check-label" for="opt{{ $o->id }}">{{ $o->name }}</label>
                            </div>
                            @empty
                            <div class="small text-muted">Belum ada varian.</div>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold small mb-2">Topping <span class="text-muted fw-normal">(boleh banyak, berbayar)</span></div>
                            @forelse($topping as $o)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="options[]" value="{{ $o->id }}" id="opt{{ $o->id }}" @checked(in_array($o->id, $selected))>
                                <label class="form-check-label" for="opt{{ $o->id }}">
                                    {{ $o->name }} <span class="text-muted small">+{{ number_format($o->price, 0, ',', '.') }}</span>
                                </label>
                            </div>
                            @empty
                            <div class="small text-muted">Belum ada topping.</div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kanan: foto --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Foto menu</div>
                <div class="card-body">
                    @if($product->image)
                    <img id="preview" src="{{ asset('storage/' . $product->image) }}" class="preview mb-3" alt="">
                    <div id="previewEmpty" class="preview-empty mb-3 d-none">Pratinjau foto</div>
                    @else
                    <img id="preview" src="" class="preview mb-3 d-none" alt="">
                    <div id="previewEmpty" class="preview-empty mb-3">Pratinjau foto</div>
                    @endif

                    <input type="file" name="image" id="imageInput" accept="image/*"
                        class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">
                        Gunakan foto berukuran 4:3, minimal 600 x 450 px, maksimal 2 MB.
                        @if($product->exists) Kosongkan jika tidak ingin mengganti foto. @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-success px-4">Simpan menu</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

<script>
    document.getElementById('imageInput').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var img = document.getElementById('preview');
        img.src = URL.createObjectURL(file);
        img.classList.remove('d-none');
        document.getElementById('previewEmpty').classList.add('d-none');
    });
</script>

@endsection