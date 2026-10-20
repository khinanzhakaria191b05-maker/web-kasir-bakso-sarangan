<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Mie Ayam Bakso Sarangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container" style="max-width:400px">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">Mie Ayam Bakso Sarangan</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" autofocus>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <button class="btn btn-success w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>