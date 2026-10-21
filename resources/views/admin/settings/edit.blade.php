@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')
<h3 class="mb-3">Pengaturan Toko</h3>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
    class="card card-body" style="max-width:600px">
    @csrf @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nama Toko (tampil di struk)</label>
        <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror"
            value="{{ old('shop_name', \App\Models\Setting::get('shop_name', 'MIE AYAM BAKSO SARANGAN')) }}" required>
        @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <input type="text" name="shop_address" class="form-control"
            value="{{ old('shop_address', \App\Models\Setting::get('shop_address')) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Nomor Telepon / WhatsApp</label>
        <input type="text" name="shop_phone" class="form-control"
            value="{{ old('shop_phone', \App\Models\Setting::get('shop_phone')) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Teks Penutup Struk</label>
        <input type="text" name="receipt_footer" class="form-control"
            value="{{ old('receipt_footer', \App\Models\Setting::get('receipt_footer', 'Terima kasih, selamat menikmati!')) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar QRIS (maks 3MB)</label>
        @php $qris = \App\Models\Setting::get('qris_image'); @endphp
        @if($qris)
        <div class="mb-2"><img src="{{ asset('storage/'.$qris) }}" style="max-height:200px" class="rounded border"></div>
        @endif
        <input type="file" name="qris_image" accept="image/*" class="form-control @error('qris_image') is-invalid @enderror">
        @error('qris_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Kosongkan jika tidak ingin mengganti gambar.</div>
    </div>

    <button class="btn btn-success align-self-start">Simpan Pengaturan</button>
</form>
@endsection