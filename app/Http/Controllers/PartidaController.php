<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Partida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PartidaController extends Controller
{
    public function index(): View
    {
        $partidasDisponibles = Partida::with('jugadorX')
            ->where('estado', 'disponible')
            ->whereNull('jugadorO_id')
            ->where('jugadorX_id', '!=', Auth::id())
            ->orderByDesc('created_at')
            ->get();
        $misPartidas = Partida::with(['jugadorX', 'jugadorO'])
            ->where(function ($consulta) {
                $consulta->where('jugadorX_id', Auth::id())
                    ->orWhere('jugadorO_id', Auth::id());
            })
            ->latest()
            ->get();

        return view('partidas.index', compact('partidasDisponibles', 'misPartidas'));
    }

    public function crear(): RedirectResponse
    {
        $turnoInicial = random_int(0, 1) === 0 ? 'X' : 'O';
        $partida = Partida::create([
            'jugadorX_id' => Auth::id(),
            'jugadorO_id' => null,
            'tablero' => '---------',
            'turno' => $turnoInicial,
            'estado' => 'disponible',
            'ganador_id' => null,
            'resultado' => null,
        ]);

        return redirect()->route('partidas.mostrar', $partida)
            ->with('success', 'Partida creada. Espera a que se una otro jugador.');
    }

    public function unirse(int $id): RedirectResponse
    {
        $resultado = DB::transaction(function () use ($id) {
            $partida = Partida::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($partida->estado !== 'disponible' || $partida->jugadorO_id !== null) {
                return false;
            }

            if ($partida->jugadorX_id === Auth::id()) {
                return false;
            }

            $partida->jugadorO_id = Auth::id();
            $partida->estado = 'en_curso';
            $partida->save();

            return true;
        });

        if (! $resultado) {
            return redirect()->route('partidas.index')
                ->with('error', 'No puedes unirte a esta partida.');
        }

        return redirect()->route('partidas.mostrar', $id)
            ->with('success', 'Te has unido a la partida.');
    }

    public function mostrar(int $id): View
    {
        $partida = Partida::with(['jugadorX', 'jugadorO', 'ganador', 'movimientos'])
            ->findOrFail($id);

        if (
            $partida->jugadorX_id != Auth::id() &&
            $partida->jugadorO_id != Auth::id()
        ) {
            abort(403, 'No perteneces a esta partida.');
        }

        return view('partidas.jugar', compact('partida'));
    }

    public function mover(Request $request, int $id): RedirectResponse
    {
        $datos = $request->validate(['posicion' => 'required|integer|between:0,8']);

        $mensaje = DB::transaction(function () use ($id, $datos) {
            $partida = Partida::whereKey($id)->lockForUpdate()->firstOrFail();
            $usuarioId = Auth::id();

            if (! in_array($usuarioId, [$partida->jugadorX_id, $partida->jugadorO_id], true)) {
                abort(403, 'No perteneces a esta partida.');
            }

            if ($partida->estado !== 'en_curso') {
                return 'La partida no está en curso.';
            }

            $simboloUsuario = $partida->jugadorX_id === $usuarioId ? 'X' : 'O';
            if ($partida->turno !== $simboloUsuario) {
                return 'Aún no es tu turno.';
            }

            $tablero = str_split($partida->tablero);
            if ($tablero[$datos['posicion']] !== '-') {
                return 'Esa casilla ya está ocupada.';
            }

            $tablero[$datos['posicion']] = $simboloUsuario;
            $partida->tablero = implode('', $tablero);

            Movimiento::create([
                'partida_id' => $partida->id,
                'usuario_id' => $usuarioId,
                'simbolo' => $simboloUsuario,
                'posicion' => $datos['posicion'],
            ]);

            if ($this->hayGanador($tablero, $simboloUsuario)) {
                $partida->estado = 'finalizada';
                $partida->resultado = 'ganada';
                $partida->ganador_id = $usuarioId;
            } elseif (! in_array('-', $tablero, true)) {
                $partida->estado = 'finalizada';
                $partida->resultado = 'empate';
            } else {
                $partida->turno = $simboloUsuario === 'X' ? 'O' : 'X';
            }

            $partida->save();

            return null;
        });

        return redirect()->route('partidas.mostrar', $id)
            ->with($mensaje ? 'error' : 'success', $mensaje ?? 'Movimiento guardado.');
    }

    public function abandonar(int $id): RedirectResponse
    {
        $mensaje = DB::transaction(function () use ($id) {
            $partida = Partida::whereKey($id)->lockForUpdate()->firstOrFail();
            $usuarioId = Auth::id();

            if (! in_array($usuarioId, [$partida->jugadorX_id, $partida->jugadorO_id], true)) {
                abort(403, 'No perteneces a esta partida.');
            }
            if ($partida->estado !== 'en_curso') {
                return 'Solo puedes abandonar una partida en curso.';
            }

            $partida->estado = 'cancelada';
            $partida->resultado = 'abandonada';
            $partida->save();

            return null;
        });

        return redirect()->route('partidas.index')
            ->with($mensaje ? 'error' : 'success', $mensaje ?? 'Has abandonado la partida.');
    }

    public function estado(int $id)
    {
        $partida = Partida::with(['jugadorX:id,name', 'jugadorO:id,name', 'ganador:id,name'])
            ->findOrFail($id);

        if (! in_array(Auth::id(), [$partida->jugadorX_id, $partida->jugadorO_id], true)) {
            abort(403, 'No perteneces a esta partida.');
        }

        return response()->json([
            'tablero' => $partida->tablero,
            'turno' => $partida->turno,
            'estado' => $partida->estado,
            'resultado' => $partida->resultado,
            'ganador' => $partida->ganador?->name,
            'jugadorO' => $partida->jugadorO?->name,
        ]);
    }

    private function hayGanador(array $tablero, string $simbolo): bool
    {
        $lineas = [[0, 1, 2], [3, 4, 5], [6, 7, 8], [0, 3, 6], [1, 4, 7], [2, 5, 8], [0, 4, 8], [2, 4, 6]];

        foreach ($lineas as $linea) {
            if ($tablero[$linea[0]] === $simbolo && $tablero[$linea[1]] === $simbolo && $tablero[$linea[2]] === $simbolo) {
                return true;
            }
        }

        return false;
    }
}
