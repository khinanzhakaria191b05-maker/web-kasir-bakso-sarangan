<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Mie Ayam Bakso Sarangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        #sidebar {
            width: 250px;
        }

        @media (min-width: 992px) {
            #sidebar {
                min-height: 100vh;
            }
        }

        .nav-link.active {
            background: #198754;
            color: #fff !important;
            border-radius: .5rem;
        }
    </style>
</head>

<body class="bg-light">

    {{-- Top bar khusus HP --}}
    <nav class="navbar bg-white border-bottom d-lg-none">
        <div class="container-fluid">
            <button class="btn" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                <i class="bi bi-list fs-3"></i>
            </button>
            <span class="fw-bold">Sarangan</span>
        </div>
    </nav>

    <div class="d-flex">
        {{-- Sidebar: tetap di kiri saat PC, jadi hamburger saat HP --}}
        <div class="offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1" id="sidebar">
            <div class="offcanvas-header">
                <h6 class="mb-0 fw-bold">Menu</h6>
                <button class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <h5 class="fw-bold text-success mb-4 d-none d-lg-block">Mie Ayam Bakso<br>Sarangan</h5>

                <ul class="nav nav-pills flex-column gap-1 mb-auto">
                    <li><a href="{{ route('admin.dashboard') }}" class="nav-link text-dark {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li><a href="{{ route('admin.products.index') }}" class="nav-link text-dark {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-cup-hot me-2"></i>Kelola Menu</a></li>
                    <li><a href="{{ route('admin.categories.index') }}" class="nav-link text-dark {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-tags me-2"></i>Kategori</a></li>
                    <li><a href="{{ route('admin.options.index') }}" class="nav-link text-dark {{ request()->routeIs('admin.options.*') ? 'active' : '' }}"><i class="bi bi-sliders me-2"></i>Varian & Topping</a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="nav-link text-dark {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-people me-2"></i>Kelola Pengguna</a></li>
                    <li><a href="{{ route('kasir') }}" class="nav-link text-dark"><i class="bi bi-cash-register me-2"></i>Layar Kasir</a></li>
                </ul>

                <hr>
                <div class="small text-muted mb-2"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }} (Admin)</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100">Logout</button>
                </form>
            </div>
        </div>

        <main class="flex-grow-1 p-3 p-lg-4" style="min-width:0">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>