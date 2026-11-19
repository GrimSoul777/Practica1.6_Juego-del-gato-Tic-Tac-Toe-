<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card shadow border-0" style="width: 450px;">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Crear cuenta</h2>
                <p class="text-center text-muted">Regístrate para jugar con otras personas</p>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form action="{{ route('registro.guardar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="name">Nombre</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                <p class="text-center mt-3 mb-0">¿Ya tienes cuenta?<a href="{{ route('login') }}">Inicia sesión</a></p>
            </div>
        </div>
    </div>
</body>
</html>