<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego del gato</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="container py-5">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Juego del gato</h1>
                <p class="text-muted mb-0">Hola, {{ Auth::user()->name }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
            </form>
        </header>

        @foreach (['success', 'error'] as $tipo)
            @if (session($tipo))
                <div class="alert alert-{{ $tipo === 'success' ? 'success' : 'danger' }}">{{ session($tipo) }}</div>
            @endif
        @endforeach

        <section class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5">Crear una partida</h2>
                    <p class="mb-0 text-muted">
                        Entrarás directamente y esperarás a que se una otro jugador.
                    </p>
                </div>

                <form action="{{ route('partidas.crear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Nueva partida</button>
                </form>
            </div>
        </section>

        <section class="card shadow-sm mb-4">
            <div class="card-header">
                <strong>Partidas disponibles</strong>
            </div>

            <div class="list-group list-group-flush">
                @forelse ($partidasDisponibles as $partida)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Partida #{{ $partida->id }}</strong>
                            <div class="text-muted">
                                Creada por {{ $partida->jugadorX->name }}
                            </div>
                        </div>

                        <form action="{{ route('partidas.unirse', $partida->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Unirme</button>
                        </form>
                    </div>
                @empty
                    <div class="list-group-item text-muted">
                        No hay partidas disponibles.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="card shadow-sm">
            <div class="card-header">
                <strong>Mis partidas e historial</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Partida</th>
                            <th>Oponente</th>
                            <th>Estado</th>
                            <th>Resultado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($misPartidas as $partida)
                            @php
                                $esJugadorX = $partida->jugadorX_id === Auth::id();
                                $oponente = $esJugadorX ? $partida->jugadorO : $partida->jugadorX;
                            @endphp
                            <tr>
                                <td>
                                    #{{ $partida->id }} ({{ $esJugadorX ? 'X' : 'O' }})
                                </td>
                                <td>{{ $oponente?->name ?? 'Esperando jugador' }}</td>
                                <td>{{ str_replace('_', ' ', $partida->estado) }}</td>
                                <td>
                                    {{ $partida->ganador_id ? 'Ganó '.$partida->ganador?->name : ($partida->resultado ?? '—') }}
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('partidas.mostrar', $partida->id) }}">
                                        Abrir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Aún no tienes partidas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>