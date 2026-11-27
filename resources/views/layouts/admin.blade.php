<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - {{ $shopName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #1f2d27;
            --muted: #6c757d;
            --line: #e9ecef;
            --brand: #198754;
            --brand-dark: #146c43;
            --qris: #2f6fb3;
        }

        body {
            background: #f5f7f6;
            color: var(--ink);
        }

        /* Sidebar */
        .sidebar-col {
            background: #fff;
        }

        .offcanvas-lg {
            --bs-offcanvas-width: 270px;
        }

        .sidebar-col .offcanvas-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 16px 14px;
        }

        @media (min-width: 992px) {
            .sidebar-col {
                width: 250px;
                flex-shrink: 0;
                position: sticky;
                top: 0;
                height: 100vh;
                display: flex;
                flex-direction: column;
                border-right: 1px solid var(--line);
            }

            .sidebar-col .offcanvas-lg {
                display: flex;
                flex-direction: column;
                flex: 1;
                min-height: 0;
            }
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 6px 18px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .brand-name {
            font-weight: 600;
            line-height: 1.25;
            font-size: .95rem;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            margin-bottom: 2px;
            border-radius: 8px;
            color: #495057;
            text-decoration: none;
            font-size: .93rem;
        }

        .side-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
        }

        .side-link:hover {
            background: #f1f3f5;
            color: var(--ink);
        }

        .side-link.active {
            background: var(--brand);
            color: #fff;
            font-weight: 500;
        }

        .side-sep {
            border-top: 1px solid var(--line);
            margin: 10px 4px;
        }

        .user-box {
            margin-top: auto;
            border-top: 1px solid var(--line);
            padding-top: 14px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #d1e7dd;
            color: var(--brand-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Top bar HP */
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--line);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Konten */
        .content {
            flex-grow: 1;
            min-width: 0;
            padding: 20px 16px 40px;
        }

        @media (min-width: 992px) {
            .content {
                padding: 28px 32px 48px;
                max-width: 1240px;
            }
        }

        .content h1 {
            font-size: 1.4rem;
            font-weight: 600;
        }

        /* Kartu dan tabel: dibuat seragam di semua halaman */
        .card {
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            font-weight: 600;
            padding: 14px 18px;
            border-radius: 12px 12px 0 0 !important;
        }

        .content .table-responsive {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }

        .content .card .table-responsive {
            border: 0;
            box-shadow: none;
            border-radius: 0;
        }

        .table {
            margin: 0;
            --bs-table-bg: transparent;
        }

        .table> :not(caption)>*>* {
            padding: .75rem 1rem;
        }

        .table thead th {
            background: #f8f9fa;
            color: #495057;
            font-size: .82rem;
            font-weight: 600;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        .table tbody tr:last-child td {
            border-bottom: 0;
        }

        .table tbody tr:hover td {
            background: #f8faf9;
        }

        .btn,
        .form-control,
        .form-select {
            border-radius: 8px;
        }

        .btn-success {
            --bs-btn-bg: var(--brand);
            --bs-btn-border-color: var(--brand);
            --bs-btn-hover-bg: var(--brand-dark);
            --bs-btn-hover-border-color: var(--brand-dark);
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>
</head>

<body>

    <div class="topbar d-lg-none">
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Buka menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-semibold text-truncate">{{ $shopName }}</span>
    </div>

    <div class="d-flex">
        <aside class="sidebar-col">
            <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
                <div class="offcanvas-header border-bottom d-lg-none">
                    <h6 class="mb-0 fw-semibold">{{ $shopName }}</h6>
                    <button class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
                </div>

                <div class="offcanvas-body">
                    <div class="brand d-none d-lg-flex">
                        <div class="brand-icon"><i class="bi bi-shop"></i></div>
                        <div class="brand-name">{{ $shopName }}</div>
                    </div>

                    <nav>
                        <a href="{{ route('admin.dashboard') }}" class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
                        <a href="{{ route('admin.transactions.index') }}" class="side-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}"><i class="bi bi-clock-history"></i>Riwayat transaksi</a>

                        <div class="side-sep"></div>

                        <a href="{{ route('admin.products.index') }}" class="side-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-cup-hot"></i>Kelola menu</a>
                        <a href="{{ route('admin.categories.index') }}" class="side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i>Kategori</a>
                        <a href="{{ route('admin.options.index') }}" class="side-link {{ request()->routeIs('admin.options.*') ? 'active' : '' }}"><i class="bi bi-sliders"></i>Varian dan topping</a>

                        <div class="side-sep"></div>

                        <a href="{{ route('admin.users.index') }}" class="side-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people"></i>Pengguna</a>
                        <a href="{{ route('admin.settings.edit') }}" class="side-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="bi bi-gear"></i>Pengaturan</a>

                        <div class="side-sep"></div>

                        <a href="{{ route('kasir') }}" class="side-link"><i class="bi bi-cash-coin"></i>Layar kasir</a>
                    </nav>

                    <div class="user-box">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <div class="overflow-hidden">
                                <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                                <div class="small text-muted">{{ ucfirst(auth()->user()->role) }}</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <main class="content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>