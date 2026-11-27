@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')

@php
$namaToko = old('shop_name', \App\Models\Setting::get('shop_name', 'Mie Ayam Bakso Sarangan'));
$alamat = old('shop_address', \App\Models\Setting::get('shop_address'));
$telepon = old('shop_phone', \App\Models\Setting::get('shop_phone'));
$penutup = old('receipt_footer', \App\Models\Setting::get('receipt_footer', 'Terima kasih, selamat menikmati!'));
$ppnAktif = old('tax_enabled', \App\Models\Setting::get('tax_enabled', '0')) == '1';
$ppnTarif = old('tax_rate', \App\Models\Setting::get('tax_rate', 11));
$qris = \App\Models\Setting::get('qris_image');
@endphp

<style>
    .form-check-input:checked {
        background-color: var(--brand);
        border-color: var(--brand);
    }

    .qris-box {
        width: 100%;
        max-width: 260px;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #fff;
        display: block;
    }

    .qris-empty {
        width: 100%;
        max-width: 260px;
        aspect-ratio: 1 / 1;
        border: 1px dashed #b8c2bb;
        border-radius: 8px;
        background: #f1f3f2;
        color: var(--muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .9rem;
        text-align: center;
        padding: 12px;
    }
</style>

<div class="mb-4">
    <h1 class="mb-0">Pengaturan</h1>
    <div class="text-muted small">Perubahan langsung dipakai di layar kasir, struk, dan halaman login.</div>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-3 align-items-start">

        {{-- Kiri --}}
        <div class="col-lg-7">

            <div class="card mb-3">
                <div class="card-header">Identitas toko</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama toko</label>
                        <input type="text" name="shop_name" value="{{ $namaToko }}" maxlength="100" required
                            class="form-control @error('shop_name') is-invalid @enderror">
                        @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Tampil di sidebar, halaman login, layar kasir, dan struk.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="shop_address" value="{{ $alamat }}" maxlength="200"
                            class="form-control" placeholder="Jalan, nomor, kota">
                    </div>

                    <div>
                        <label class="form-label">Nomor telepon atau WhatsApp</label>
                        <input type="text" name="shop_phone" value="{{ $telepon }}" maxlength="30"
                            class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Struk</div>
                <div class="card-body">
                    <label class="form-label">Teks penutup</label>
                    <input type="text" name="receipt_footer" value="{{ $penutup }}" maxlength="200" class="form-control">
                    <div class="form-text">Tertulis di bagian paling bawah struk.</div>
                </div>
            </div>

        </div>

        {{-- Kanan --}}
        <div class="col-lg-5">

            <div class="card mb-3">
                <div class="card-header">Pajak (PPN)</div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="tax_enabled" value="1" id="taxEnabled" {{ $ppnAktif ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="taxEnabled">Aktifkan PPN di kasir</label>
                    </div>

                    <label class="form-label">Tarif PPN</label>
                    <div class="input-group has-validation">
                        <input type="number" step="0.01" min="0" max="100" name="tax_rate" value="{{ $ppnTarif }}"
                            class="form-control @error('tax_rate') is-invalid @enderror">
                        <span class="input-group-text">%</span>
                        @error('tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-text">
                        Ditambahkan di atas harga menu dan dihitung setelah diskon. Tarif hanya berlaku saat PPN diaktifkan.
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Pembayaran QRIS</div>
                <div class="card-body">
                    @if($qris)
                    <img id="qrisPreview" src="{{ asset('storage/' . $qris) }}" class="qris-box mb-3" alt="QRIS">
                    <div id="qrisEmpty" class="qris-empty mb-3 d-none">Pratinjau QRIS</div>
                    @else
                    <img id="qrisPreview" src="" class="qris-box mb-3 d-none" alt="QRIS">
                    <div id="qrisEmpty" class="qris-empty mb-3">Belum ada gambar QRIS</div>
                    @endif

                    <input type="file" name="qris_image" id="qrisInput" accept="image/*"
                        class="form-control @error('qris_image') is-invalid @enderror">
                    @error('qris_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">
                        Gambar tampil saat kasir memilih pembayaran QRIS. Maksimal 3 MB.
                        @if($qris) Kosongkan jika tidak ingin menggantinya. @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-success px-4">Simpan pengaturan</button>
    </div>
</form>

<script>
    document.getElementById('qrisInput').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var img = document.getElementById('qrisPreview');
        img.src = URL.createObjectURL(file);
        img.classList.remove('d-none');
        document.getElementById('qrisEmpty').classList.add('d-none');
    });
</script>

@endsection 