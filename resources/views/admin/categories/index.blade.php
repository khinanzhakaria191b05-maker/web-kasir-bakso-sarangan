@extends('layouts.admin')
@section('title', 'Kategori')

@section('content')

<div class="mb-4">
    <h1 class="mb-0">Kategori</h1>
    <div class="text-muted small">Kategori tampil sebagai tab di layar kasir.</div>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-3">

    {{-- Tambah kategori --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Tambah kategori</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <label class="form-label">Nama kategori</label>
                    <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required
                        class="form-control mb-3" placeholder="Misalnya Bakso">
                    <button class="btn btn-success w-100">
                        <i class="bi bi-plus-lg me-1"></i>Tambah kategori
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar kategori --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar kategori</span>
                <span class="badge text-bg-light border">{{ $categories->count() }} kategori</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th class="text-end">Jumlah menu</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $c)
                        <tr>
                            <td class="fw-semibold">{{ $c->name }}</td>
                            <td class="text-end">
                                @if($c->products_count > 0)
                                <a href="{{ route('admin.products.index', ['category' => $c->id]) }}"
                                    class="badge text-bg-success text-decoration-none">{{ $c->products_count }} menu</a>
                                @else
                                <span class="badge text-bg-light border">Kosong</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-action="{{ route('admin.categories.update', $c) }}"
                                    data-name="{{ $c->name }}">
                                    <i class="bi bi-pencil me-1"></i>Ubah
                                </button>

                                @if($c->products_count === 0)
                                <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                                </form>
                                @else
                                <span class="small text-muted ms-2">Masih dipakai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Belum ada kategori. Tambahkan yang pertama di kolom kiri, misalnya Bakso atau Minuman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Jendela ubah nama --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" id="editForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h2 class="modal-title h6 fw-semibold" id="editTitle">Ubah kategori</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <label for="editName" class="form-label">Nama kategori</label>
                <input type="text" name="name" id="editName" maxlength="100" required class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById('editModal');
    modal.addEventListener('show.bs.modal', function(e) {
        document.getElementById('editForm').action = e.relatedTarget.dataset.action;
        document.getElementById('editName').value = e.relatedTarget.dataset.name;
    });
    modal.addEventListener('shown.bs.modal', function() {
        document.getElementById('editName').select();
    });
</script>

@endsection