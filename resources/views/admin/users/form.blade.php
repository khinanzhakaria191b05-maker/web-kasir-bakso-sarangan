@extends('layouts.admin')
@section('title', $user->exists ? 'Edit Akun' : 'Tambah Akun')
@section('content')
<h3 class="mb-3">{{ $user->exists ? 'Edit Akun' : 'Tambah Akun Staf' }}</h3>

<form method="POST" class="card card-body" style="max-width:500px"
    action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if($user->exists) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Username (untuk login)</label>
        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" required>
        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select @error('role') is-invalid @enderror">
            <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
        </select>
        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ $user->exists ? 'Reset Password (kosongkan jika tidak diubah)' : 'Password' }}</label>
        <input type="text" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="Minimal 6 karakter" autocomplete="off" {{ $user->exists ? '' : 'required' }}>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
@endsection