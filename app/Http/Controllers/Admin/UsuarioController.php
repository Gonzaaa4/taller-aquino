<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::when($request->rol, fn($q) => $q->where('rol', $request->rol))
            ->when($request->buscar, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->buscar}%")
                  ->orWhere('apellido', 'like', "%{$request->buscar}%")
                  ->orWhere('email', 'like', "%{$request->buscar}%");
            }))
            ->orderBy('rol')->orderBy('name')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.form', ['usuario' => new User()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'dni'      => 'nullable|string|max:20|unique:users,dni',
            'telefono' => 'nullable|string|max:30',
            'rol'      => 'required|in:admin,mecanico,administrativo,cliente',
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name'     => $request->name,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'dni'      => $request->dni,
            'telefono' => $request->telefono,
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.form', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$usuario->id}",
            'dni'      => "nullable|string|max:20|unique:users,dni,{$usuario->id}",
            'telefono' => 'nullable|string|max:30',
            'rol'      => 'required|in:admin,mecanico,administrativo,cliente',
        ]);

        $datos = $request->only('name', 'apellido', 'email', 'dni', 'telefono', 'rol');

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::min(8)]]);
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No podés desactivar tu propio usuario.');
        }
        $usuario->update(['activo' => ! $usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }
}
