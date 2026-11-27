@extends('layouts.admin')
@section('title', 'Varian dan topping')

@section('content')

@php
$nVarian = $jumlah['varian'] ?? 0;
$nTopping = $jumlah['topping'] ?? 0;
$tipe = request('type');
@endphp

<style>
    .nav-pills {
        --bs-nav-pills-link-active-bg: var(--brand);
    }

    .nav-pills .nav-link {
        color: #495057;
        font-size: .9rem;
        padding: .3rem .8rem;
    }

    .nav-pills .nav-link.active {
        color: #fff;
    }
</style>

<div class="mb-4">
    <h1 class="mb-0">Varian dan topping</h1>
    <div class="text-muted small">Pilihan tambahan yang muncul saat kasir memilih menu.</div>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-3">

    {{-- Tambah --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Tambah pilihan</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.options.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required
                            class="form-control" placeholder="Misalnya Ekstra tetelan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="type" class="form-select js-type">
                            <option value="varian">Varian (gratis)</option>
                            <option value="topping">Topping (berbayar)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga tambahan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" min="0" value="0" class="form-control js-price" disabled>
                        </div>
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Apa bedanya?</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge text-bg-info">Varian</span>
                        <span class="small fw-semibold">Pilih satu, gratis</span>
                    </div>
                    <div class="small text-muted">Contoh: teh dingin atau teh panas.</div>
                </li>
                <li class="list-group-item">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge text-bg-warning">Topping</span>
                        <span class="small fw-semibold">Boleh banyak, berbayar</span>
                    </div>
                    <div class="small text-muted">Contoh: ekstra tetelan.</div>
                </li>
                <li class="list-group-item small text-muted">
                    <i class="bi bi-info-circle me-1"></i>Setelah dibuat, tautkan ke menu lewat halaman Ubah menu.
                </li>
            </ul>
        </div>
    </div>

    {{-- Daftar --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span>Daftar pilihan</span>
                <ul class="nav nav-pills gap-1">
                    <li class="nav-item"><a class="nav-link {{ $tipe ? '' : 'active' }}" href="{{ route('admin.options.index') }}">Semua ({{ $nVarian + $nTopping }})</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tipe === 'varian' ? 'active' : '' }}" href="{{ route('admin.options.index', ['type' => 'varian']) }}">Varian ({{ $nVarian }})</a></li>
                    <li class="nav-item"><a class="nav-link {{ $tipe === 'topping' ? 'active' : '' }}" href="{{ route('admin.options.index', ['type' => 'topping']) }}">Topping ({{ $nTopping }})</a></li>
                </ul>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th class="text-end">Harga tambahan</th>
                            <th class="text-end">Dipakai di</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($options as $o)
                        <tr>
                            <td class="fw-semibold">{{ $o->name }}</td>
                            <td>
                                @if($o->type === 'varian')
                                <span class="badge text-bg-info">Varian</span>
                                @else
                                <span class="badge text-bg-warning">Topping</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if($o->type === 'topping')
                                + Rp {{ number_format($o->price, 0, ',', '.') }}
                                @else
                                <span class="text-muted">Gratis</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @if($o->products_count > 0)
                                {{ $o->products_count }} menu
                                @else
                                <span class="text-muted">Belum dipakai</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-success"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-action="{{ route('admin.options.update', $o) }}"
                                    data-name="{{ $o->name }}"
                                    data-type="{{ $o->type }}"
                                    data-price="{{ $o->price }}">
                                    <i class="bi bi-pencil me-1"></i>Ubah
                                </button>
                                <form method="POST" action="{{ route('admin.options.destroy', $o) }}" class="d-inline"
                                    data-confirm="Hapus {{ $o->name }}?{{ $o->products_count > 0 ? ' Pilihan ini juga hilang dari ' . $o->products_count . ' menu.' : '' }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                @if($tipe)
                                Belum ada {{ $tipe }}. Tambahkan lewat kolom di kiri.
                                @else
                                Belum ada varian atau topping. Mulai dengan menambahkan satu di kolom kiri.
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $options->links() }}</div>
    </div>

</div>

{{-- Jendela ubah --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="editForm" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h2 class="modal-title h6 fw-semibold" id="editTitle">Ubah pilihan</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editName" class="form-label">Nama</label>
                    <input type="text" name="name" id="editName" maxlength="100" required class="form-control">
                </div>
                <div class="row g-3">
                    <div class="col-7">
                        <label for="editType" class="form-label">Jenis</label>
                        <select name="type" id="editType" class="form-select js-type">
                            <option value="varian">Varian (gratis)</option>
                            <option value="topping">Topping (berbayar)</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <label for="editPrice" class="form-label">Harga</label>
                        <input type="number" name="price" id="editPrice" min="0" class="form-control js-price">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function syncPrice(scope) {
        var type = scope.querySelector('.js-type');
        var price = scope.querySelector('.js-price');
        var gratis = type.value === 'varian';
        price.disabled = gratis;
        if (gratis) price.value = 0;
    }

    document.querySelectorAll('form').forEach(function(f) {
        if (f.querySelector('.js-type')) {
            syncPrice(f);
            f.querySelector('.js-type').addEventListener('change', function() {
                syncPrice(f);
            });
        }
    });

    var modal = document.getElementById('editModal');
    var editForm = document.getElementById('editForm');
    modal.addEventListener('show.bs.modal', function(e) {
        var d = e.relatedTarget.dataset;
        editForm.action = d.action;
        document.getElementById('editName').value = d.name;
        document.getElementById('editType').value = d.type;
        document.getElementById('editPrice').value = d.price;
        syncPrice(editForm);
    });

    document.addEventListener('submit', function(e) {
        var msg = e.target.dataset.confirm;
        if (msg && !confirm(msg)) e.preventDefault();
    });
</script>

@endsection