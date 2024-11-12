<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends Controller
{
    // Muestra el formulario de inicio de sesión para usuarios generales
    public function showLoginForm()
    {
        return view('auth.login'); // Vista para el login de usuarios generales
    }

    // Procesa el inicio de sesión de los usuarios
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Intento de autenticación usando el guardado 'web' con el proveedor 'app_users'
        if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('visitas.index'); // Redirigir a 'visitas.index' si el login es exitoso
        }

        return redirect()->back()->withErrors(['loginError' => 'Credenciales incorrectas']);
    }




    // Muestra la vista de visitas para usuarios autenticados
    public function visitas()
    {
        return view('visitas');
    }
}
