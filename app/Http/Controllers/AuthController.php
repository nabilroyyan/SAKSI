<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function index ()
    {
        return view('login');;//ketikda mengases sesicontroller maka akan ter terdireck ke views login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            if ($user->hasRole('superadmin')) {
                return redirect()->intended('/superadmin/dashboard'); // Route untuk superadmin
            } elseif ($user->hasRole('tatip')) {
                return redirect()->intended('/superadmin/dashboard'); // Route untuk guru
            } elseif ($user->hasRole('bk')) {
                return redirect()->intended('/superadmin/dashboard'); // Route untuk guru
            } else {
                Auth::logout(); // kalau tidak punya role
                return redirect('/login')->withErrors(['email' => 'email tidak dikenali.']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }


    //controller logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
