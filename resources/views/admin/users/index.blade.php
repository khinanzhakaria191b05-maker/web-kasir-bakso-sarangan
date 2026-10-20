@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Kelola Pengguna</h3>
    <a href="{{ route('admin.users.create') }}" class="btn btn-success"><i class="bi bi-person-plus"></i> Tambah Akun</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr class="{{ $u->is_active ? '' : 'text-muted' }}">
                    <td>
                        {{ $u->name }}
                        @if($u->id === auth()->id()) <span class="badge text-bg-light border">Kamu</span> @endif
                    </td>
                    <td>{{ $u->username }}</td>
                    <td>
                        <span class="badge {{ $u->role === 'admin' ? 'text-bg-danger' : 'text-bg-primary' }}">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $u->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="d-inline"
                            onsubmit="return confirm('{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->name }}?')">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection