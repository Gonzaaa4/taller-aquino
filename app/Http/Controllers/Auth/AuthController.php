<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ── Login ────────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectSegunRol(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresá un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credenciales = $request->only('email', 'password');
        $recordar     = $request->boolean('remember');

        if (Auth::attempt($credenciales, $recordar)) {
            $request->session()->regenerate();
            $usuario = Auth::user();

            if (! $usuario->activo) {
                Auth::logout();
                return back()->with('error', 'Tu cuenta está suspendida. Contactá al taller.');
            }

            return $this->redirectSegunRol($usuario);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Correo o contraseña incorrectos.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    // ── Registro (solo clientes se registran solos) ──────────────
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectSegunRol(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'dni'      => 'required|string|max:20|unique:users,dni',
            'telefono' => 'required|string|max:30',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'       => 'El nombre es obligatorio.',
            'apellido.required'   => 'El apellido es obligatorio.',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.unique'        => 'Este correo ya está registrado.',
            'dni.required'        => 'El DNI es obligatorio.',
            'dni.unique'          => 'Este DNI ya está registrado.',
            'telefono.required'   => 'El teléfono es obligatorio.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'dni'      => $request->dni,
            'telefono' => $request->telefono,
            'direccion'=> $request->direccion,
            'rol'      => 'cliente',
            'password' => Hash::make($request->password),
        ]);

        Auth::login($usuario);

        return redirect()->route('cliente.dashboard')
            ->with('success', '¡Bienvenido/a al Taller Aquino, ' . $usuario->name . '!');
    }

    // ── Helper: redirige al dashboard según rol ──────────────────
    private function redirectSegunRol(User $usuario)
    {
        return match($usuario->rol) {
            'admin', 'administrativo' => redirect()->route('admin.dashboard'),
            'mecanico'                => redirect()->route('admin.dashboard'),
            'cliente'                 => redirect()->route('cliente.dashboard'),
            default                   => redirect()->route('home'),
        };
    }
}
