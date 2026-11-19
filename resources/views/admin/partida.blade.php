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
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .simbolo-x {
            color: #0d6efd;
        }

        .simbolo-o {
            color: #dc3545;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5" style="max-width: 700px;">
        <div class="mb-3">
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                ← Volver al panel
            </a>
        </div>

        <section class="card shadow-sm">
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Partida #{{ $partida->id }}</h1>
            </div>

            <div class="card-body text-center">
                <p class="mb-1">
                    <strong>Jugador X:</strong>
                    {{ $partida->jugadorX?->name ?? 'Usuario eliminado' }}
                </p>
                <p>
                    <strong>Jugador O:</strong>
                    {{ $partida->jugadorO?->name ?? 'Sin jugador' }}
                </p>

                <div class="alert alert-secondary">
                    <strong>Estado:</strong> {{ str_replace('_', ' ', $partida->estado) }}
                    <br>
                    <strong>Resultado:</strong>
                    @if ($partida->ganador)
                        Ganó {{ $partida->ganador->name }}
                    @elseif ($partida->resultado === 'empate')
                        Empate
                    @elseif ($partida->resultado)
                        {{ ucfirst($partida->resultado) }}
                    @else
                        Sin resultado todavía
                    @endif
                </div>

                <h2 class="h5 mb-3">
                    {{ $partida->estado === 'finalizada' ? 'Tablero final' : 'Tablero actual' }}
                </h2>
                <div class="tablero my-4">
                    @for ($i = 0; $i < 9; $i++)
                        @php
                            $casilla = $partida->tablero[$i];
                            $claseSimbolo = $casilla === 'X'
                                ? 'simbolo-x'
                                : ($casilla === 'O' ? 'simbolo-o' : 'text-muted');
                        @endphp

                        <div class="border rounded bg-white casilla {{ $claseSimbolo }}">
                            {{ $casilla === '-' ? '—' : $casilla }}
                        </div>
                    @endfor
                </div>

                <p class="text-muted mb-0">
                    Turno registrado: {{ $partida->turno }}
                </p>
            </div>
        </section>
    </div>
</body>
</html>
