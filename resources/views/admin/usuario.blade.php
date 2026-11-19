<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $usuario->exists ? 'Editar usuario' : 'Crear usuario' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card shadow border-0" style="width: 500px;">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">
                    {{ $usuario->exists ? 'Editar usuario' : 'Crear usuario' }}
                </h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ $accion }}" method="POST">
                    @csrf

                    @if ($usuario->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="name">Nombre</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $usuario->name) }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">Correo</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $usuario->email) }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            Contraseña
                            @if ($usuario->exists)
                                (déjala vacía para conservarla)
                            @endif
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            @required(! $usuario->exists)
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="rol">Rol</label>
                        <select id="rol" name="rol" class="form-select">
                            <option value="Jugador" @selected(old('rol', $usuario->rol) === 'Jugador')>
                                Jugador
                            </option>
                            <option value="Administrador" @selected(old('rol', $usuario->rol) === 'Administrador')>
                                Administrador
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Guardar
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    <a href="{{ route('admin.index') }}">Volver al panel</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
