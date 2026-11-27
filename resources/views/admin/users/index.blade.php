@extends('layouts.admin')
@section('title', 'Pengguna')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="mb-0">Pengguna</h1>
        <div class="text-muted small">Akun admin dan kasir yang bisa masuk ke sistem.</div>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
        <i class="bi bi-person-plus me-1"></i>Tambah akun
    </a>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total akun</div>
                <div class="fs-4 fw-semibold">{{ $total }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Aktif</div>
                <div class="fs-4 fw-semibold text-success">{{ $aktif }}</div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Nonaktif</div>
                <div class="fs-4 fw-semibold {{ $total - $aktif > 0 ? 'text-danger' : '' }}">{{ $total - $aktif }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel --}}
<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Peran</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr class="{{ $u->is_active ? '' : 'text-muted' }}">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                            style="width:34px;height:34px;background:#d1e7dd;color:#146c43">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                        <div>
                            <div class="fw-semibold">{{ $u->name }}</div>
                            @if($u->id === auth()->id())
                            <div class="small text-muted">Akun kamu</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    @if($u->role === 'admin')
                    <span class="badge text-bg-dark">Admin</span>
                    @else
                    <span class="badge text-bg-info">Kasir</span>
                    @endif
                </td>
                <td>
                    @if($u->is_active)
                    <span class="badge text-bg-success">Aktif</span>
                    @else
                    <span class="badge text-bg-secondary">Nonaktif</span>
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-pencil me-1"></i>Ubah
                    </a>

                    @if($u->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="d-inline"
                        data-confirm="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->name }}?">
                        @csrf @method('PATCH')
                        @if($u->is_active)
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-slash-circle me-1"></i>Nonaktifkan</button>
                        @else
                        <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle me-1"></i>Aktifkan</button>
                        @endif
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3">{{ $users->links() }}</div>

<script>
    document.addEventListener('submit', function(e) {
        var msg = e.target.dataset.confirm;
        if (msg && !confirm(msg)) e.preventDefault();
    });
</script>

@endsection