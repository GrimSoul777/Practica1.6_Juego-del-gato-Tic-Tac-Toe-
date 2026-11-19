<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $usuarios = User::orderBy('name')->get();
        $partidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])->latest()->get();
        return view('admin.index', compact('usuarios', 'partidas'));
    }

    public function verPartida(Partida $partida): View
    {
        $partida->load(['jugadorX', 'jugadorO', 'ganador']);
        return view('admin.partida', compact('partida'));
    }

    public function crearUsuario(): View
    {
        return view('admin.usuario', ['usuario' => new User, 'accion' => route('admin.usuarios.guardar')]);
    }

    public function guardarUsuario(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|in:Administrador,Jugador',
        ]);
        $datos['password'] = Hash::make($datos['password']);
        User::create($datos);
        return redirect()->route('admin.index')->with('success', 'Usuario creado.');
    }

    public function editarUsuario(User $usuario): View
    {
        return view('admin.usuario', [
            'usuario' => $usuario,
            'accion' => route('admin.usuarios.actualizar', $usuario),
        ]);
    }

    public function actualizarUsuario(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$usuario->id,
            'password' => 'nullable|string|min:8',
            'rol' => 'required|in:Administrador,Jugador',
        ]);
        if (empty($datos['password'])) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }
        $usuario->update($datos);
        return redirect()->route('admin.index')->with('success', 'Usuario actualizado.');
    }

    public function eliminarUsuario(User $usuario): RedirectResponse
    {
        if ($usuario->is(auth()->user())) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        return redirect()->route('admin.index')->with('success', 'Usuario eliminado.');
    }

    public function cancelarPartida(Partida $partida): RedirectResponse
    {
        if ($partida->estado === 'en_curso' || $partida->estado === 'disponible') {
            $partida->update(['estado' => 'cancelada', 'resultado' => 'cancelada']);
        }
        return redirect()->route('admin.index')->with('success', 'Partida cancelada.');
    }
}