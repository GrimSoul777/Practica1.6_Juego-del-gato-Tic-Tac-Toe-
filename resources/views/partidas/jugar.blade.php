<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida #{{ $partida->id }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .tablero {
            display: grid;
            grid-template-columns: repeat(3, minmax(70px, 100px));
            gap: 8px;
            justify-content: center;
        }

        .casilla {
            height: 90px;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .tablero .casilla.simbolo-x {
            color: #0d6efd;
        }

        .tablero .casilla.simbolo-o {
            color: #dc3545;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5" style="max-width: 650px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('partidas.index') }}" class="btn btn-outline-secondary">
                ← Mis partidas
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
            </form>
        </div>

        @foreach (['success', 'error'] as $tipo)
            @if (session($tipo))
                <div class="alert alert-{{ $tipo === 'success' ? 'success' : 'danger' }}">
                    {{ session($tipo) }}
                </div>
            @endif
        @endforeach

        <section
            id="juego"
            class="card shadow-sm"
            data-estado-url="{{ route('partidas.estado', $partida->id) }}"
            data-tablero="{{ $partida->tablero }}"
            data-turno="{{ $partida->turno }}"
            data-estado="{{ $partida->estado }}"
            data-jugador="{{ $partida->jugadorX_id === Auth::id() ? 'X' : 'O' }}"
        >
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Juego del gato · Partida #{{ $partida->id }}</h1>
            </div>

            <div class="card-body text-center">
                <p class="mb-1">X: {{ $partida->jugadorX->name }}</p>
                <p>
                    O:
                    <span id="jugador-o">
                        {{ $partida->jugadorO?->name ?? 'Esperando que se una otro jugador…' }}
                    </span>
                </p>

                <div
                    id="mensaje"
                    class="alert {{ $partida->estado === 'disponible' ? 'alert-info' : 'alert-primary' }}"
                >
                    @if ($partida->estado === 'disponible')
                        La partida está esperando a otro jugador.
                    @elseif ($partida->estado === 'en_curso')
                        Turno de {{ $partida->turno }}
                    @elseif ($partida->ganador)
                        Gano {{ $partida->ganador->name }}
                    @else
                        {{ ucfirst($partida->resultado ?? $partida->estado) }}
                    @endif
                </div>

                <div id="tablero" class="tablero my-4">
                    @for ($i = 0; $i < 9; $i++)
                        @php
                            $casilla = $partida->tablero[$i];
                            $miSimbolo = $partida->jugadorX_id === Auth::id() ? 'X' : 'O';
                            $esMiTurno = $partida->turno === $miSimbolo;
                            $claseSimbolo = $casilla === 'X'
                                ? 'simbolo-x'
                                : ($casilla === 'O' ? 'simbolo-o' : '');
                        @endphp

                        <form action="{{ route('partidas.mover', $partida->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="posicion" value="{{ $i }}">
                            <button
                                type="submit"
                                class="btn btn-outline-dark w-100 casilla {{ $claseSimbolo }}"
                                aria-label="Casilla {{ $i + 1 }}"
                                @disabled($partida->estado !== 'en_curso' || ! $esMiTurno || $casilla !== '-')
                            >
                                {{ $casilla === '-' ? '' : $casilla }}
                            </button>
                        </form>
                    @endfor
                </div>

                @if ($partida->estado === 'en_curso')
                    <form
                        action="{{ route('partidas.abandonar', $partida->id) }}"
                        method="POST"
                        onsubmit="return confirm('¿Quieres abandonar esta partida?')"
                    >
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            Abandonar partida
                        </button>
                    </form>
                @endif
            </div>
        </section>
    </div>

    <script>
        const juego = document.getElementById('juego');
        async function actualizarPartida() {
            try {
                const respuesta = await fetch(juego.dataset.estadoUrl, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!respuesta.ok) {
                    return;
                }

                const datos = await respuesta.json();
                const cambioElTablero = datos.tablero !== juego.dataset.tablero;
                const cambioElTurno = datos.turno !== juego.dataset.turno;
                const cambioElEstado = datos.estado !== juego.dataset.estado;

                if (cambioElTablero || cambioElTurno || cambioElEstado) {
                    window.location.reload();
                    return;
                }

                if (datos.jugadorO) {
                    document.getElementById('jugador-o').textContent = datos.jugadorO;
                }
            } catch (error) {
                console.log('No se pudo consultar el estado de la partida.');
            }
        }

        setInterval(actualizarPartida, 3000);
    </script>
</body>
</html>