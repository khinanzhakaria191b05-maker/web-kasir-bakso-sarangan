@extends('layouts.admin')
@section('title', 'Varian & Topping')
@section('content')
<h3 class="mb-3">Varian & Topping</h3>

{{-- Form tambah --}}
<form method="POST" action="{{ route('admin.options.store') }}" class="card card-body mb-4">
    @csrf
    <div class="row g-2">
        <div class="col-12 col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Nama (mis. Ekstra Tetelan)" required>
        </div>
        <div class="col-6 col-md-3">
            <select name="type" class="form-select option-type">
                <option value="varian">Varian (gratis)</option>
                <option value="topping">Topping (berbayar)</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <input type="number" name="price" min="0" value="0" class="form-control option-price" placeholder="Harga" disabled>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-success w-100">Tambah</button>
        </div>
    </div>
    @if($errors->any())
    <div class="text-danger small mt-2">{{ $errors->first() }}</div>
    @endif
</form>

{{-- Daftar --}}
<div class="card">
    <ul class="list-group list-group-flush">
        @forelse($options as $o)
        <li class="list-group-item">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <form method="POST" action="{{ route('admin.options.update', $o) }}" class="row g-2 flex-grow-1">
                    @csrf @method('PUT')
                    <div class="col-12 col-md-4">
                        <input type="text" name="name" value="{{ $o->name }}" class="form-control" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="type" class="form-select option-type">
                            <option value="varian" @selected($o->type === 'varian')>Varian (gratis)</option>
                            <option value="topping" @selected($o->type === 'topping')>Topping (berbayar)</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <input type="number" name="price" min="0" value="{{ $o->price }}" class="form-control option-price">
                    </div>
                    <div class="col-12 col-md-2">
                        <button class="btn btn-outline-primary w-100">Simpan</button>
                    </div>
                </form>
                <span class="badge text-bg-secondary">{{ $o->products_count }} menu</span>
                <form method="POST" action="{{ route('admin.options.destroy', $o) }}" onsubmit="return confirm('Hapus opsi ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </li>
        @empty
        <li class="list-group-item text-muted">Belum ada varian/topping.</li>
        @endforelse
    </ul>
</div>

<script>
    // Kolom harga hanya aktif kalau tipe = topping
    function syncPrice(select) {
        const price = select.closest('form').querySelector('.option-price');
        price.disabled = select.value === 'varian';
        if (select.value === 'varian') price.value = 0;
    }
    document.querySelectorAll('.option-type').forEach(s => {
        syncPrice(s);
        s.addEventListener('change', () => syncPrice(s));
    });
</script>
@endsection