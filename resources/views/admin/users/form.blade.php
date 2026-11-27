@extends('layouts.admin')
@section('title', $user->exists ? 'Ubah akun' : 'Tambah akun')

@section('content')

@php
$diriSendiri = $user->exists && $user->id === auth()->id();
$peran = old('role', $user->role ?? 'kasir');
@endphp

<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
    <i class="bi bi-arrow-left me-1"></i>Kembali
</a>

<h1 class="mb-4">{{ $user->exists ? 'Ubah akun' : 'Tambah akun' }}</h1>

<form method="POST"
    action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif

    <div class="row g-3 align-items-start">

        {{-- Kiri: data akun --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Data akun</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                class="form-control @error('username') is-invalid @enderror" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Dipakai untuk masuk. Tanpa spasi.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Peran</label>
                        @if($diriSendiri)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <select class="form-select" disabled>
                            <option>{{ ucfirst($user->role) }}</option>
                        </select>
                        <div class="form-text">Peran akun kamu sendiri tidak bisa diubah.</div>
                        @else
                        <select name="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="kasir" @selected($peran==='kasir' )>Kasir (hanya layar kasir)</option>
                            <option value="admin" @selected($peran==='admin' )>Admin (semua menu pengelolaan)</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: kata sandi --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Kata sandi</div>
                <div class="card-body">
                    <label class="form-label">{{ $user->exists ? 'Kata sandi baru' : 'Kata sandi' }}</label>
                    <input type="text" name="password" autocomplete="off" placeholder="Minimal 6 karakter"
                        class="form-control @error('password') is-invalid @enderror" {{ $user->exists ? '' : 'required' }}>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">
                        @if($user->exists)
                        Kosongkan jika tidak ingin mengganti kata sandi.
                        @else
                        Sampaikan kata sandi ini ke staf yang bersangkutan.
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-success px-4">Simpan akun</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>

@endsection