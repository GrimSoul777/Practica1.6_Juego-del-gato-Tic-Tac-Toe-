<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container py-5">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Administración</h1>
                <p class="text-muted mb-0">Usuarios e historial general de partidas</p>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
            </form>
        </header>

        @foreach (['success', 'error'] as $tipo)
            @if (session($tipo))
                <div class="alert alert-{{ $tipo === 'success' ? 'success' : 'danger' }}">
                    {{ session($tipo) }}
                </div>
            @endif
        @endforeach

        <section class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Usuarios ({{ $usuarios->count() }})</strong>
                <a class="btn btn-sm btn-primary" href="{{ route('admin.usuarios.crear') }}">
                    Crear usuario
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->name }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->rol }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a
                                            class="btn btn-sm btn-outline-primary"
                                            href="{{ route('admin.usuarios.editar', $usuario) }}"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="{{ route('admin.usuarios.eliminar', $usuario) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Eliminar este usuario?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay usuarios.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card shadow-sm">
            <div class="card-header">
                <strong>Todas las partidas</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador X</th>
                            <th>Jugador O</th>
                            <th>Estado</th>
                            <th>Resultado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partidas as $partida)
                            <tr>
                                <td>{{ $partida->id }}</td>
                                <td>{{ $partida->jugadorX?->name }}</td>
                                <td>{{ $partida->jugadorO?->name ?? 'Esperando' }}</td>
                                <td>{{ $partida->estado }}</td>
                                <td>{{ $partida->ganador?->name ?? $partida->resultado ?? '—' }}</td>
                                <td>
                                    <a
                                        href="{{ route('admin.partidas.ver', $partida) }}"
                                        class="btn btn-sm btn-outline-primary mb-1"
                                    >
                                        Ver tablero
                                    </a>

                                    @if (in_array($partida->estado, ['en_curso', 'disponible']))
                                        <form
                                            action="{{ route('admin.partidas.cancelar', $partida) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Cancelar
                                            </button>
                                        </form>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay partidas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>