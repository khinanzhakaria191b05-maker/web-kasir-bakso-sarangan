<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ $shopName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #198754;
            --brand-dark: #146c43;
            --brand-soft: #d1e7dd;
            --panel: #eef6f1;
        }

        body {
            min-height: 100vh;
            background: var(--brand);
            display: flex;
            align-items: center;
            padding: 16px;
        }

        .login-card {
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .18);
        }

        .form-side {
            padding: 48px 44px;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: var(--brand-soft);
            color: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin: 0 auto 12px;
        }

        .form-control.input-soft {
            background: #f1f3f5;
            border: 1px solid transparent;
            padding: 12px 14px;
        }

        .form-control.input-soft:focus {
            background: #fff;
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem rgba(25, 135, 84, .15);
        }

        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }

        .toggle-eye {
            background: #f1f3f5;
            border: 0;
            color: #6c757d;
        }

        .form-check-input:checked {
            background-color: var(--brand);
            border-color: var(--brand);
        }

        .art-side {
            background: var(--panel);
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .art-side img {
            width: 100%;
            max-width: 300px;
            mix-blend-mode: multiply;
            /* kalau gambar berlatar putih, latarnya melebur */
        }

        .art-side h5 {
            color: #1f2d27;
            margin-top: 8px;
        }

        @media (max-width: 767.98px) {
            .form-side {
                padding: 36px 24px;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="row g-0">

            {{-- KIRI: form --}}
            <div class="col-md-6 form-side">
                <div class="brand-icon"><i class="bi bi-shop"></i></div>
                <h3 class="text-center fw-bold mb-1">Login</h3>
                <p class="text-center text-muted small mb-4">{{ $shopName }}</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    @error('username')
                    <div class="alert alert-danger py-2 small">{{ $message }}</div>
                    @enderror

                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Username atau email</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                            class="form-control input-soft" autofocus required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted mb-1">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                class="form-control input-soft" required>
                            <button type="button" class="btn toggle-eye" id="togglePass" tabindex="-1" aria-label="Tampilkan password">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                    </div> -->

                    <button class="btn btn-brand w-100">Masuk</button>
                </form>

                <p class="text-center text-muted small mt-4 mb-0">
                    Belum punya akun? Hubungi admin.
                </p>
            </div>

            {{-- KANAN: ilustrasi (disembunyikan di HP) --}}
            <div class="col-md-6 art-side d-none d-md-flex">
                <img src="{{ asset('images/auth/loginkasir.png') }}" alt="Ilustrasi kasir">
                <h5 class="fw-bold">Sistem Kasir</h5>
                <p class="text-muted small mb-0 px-3">
                    Catat pesanan, hitung pembayaran, dan pantau penjualan {{ $shopName }} dengan mudah.
                </p>
            </div>

        </div>
    </div>

    <script>
        // Tampilkan / sembunyikan password
        document.getElementById('togglePass').addEventListener('click', function() {
            var input = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    </script>
</body>

</html>