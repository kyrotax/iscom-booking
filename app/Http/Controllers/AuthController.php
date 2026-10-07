<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('booking.step1'));
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nim' => 'required|string|max:20|unique:users',
            'major' => 'required|string|max:100',
            'semester' => 'required|integer|min:1|max:14',
            'whatsapp' => 'required|string|max:25',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'major' => $validated['major'],
            'semester' => $validated['semester'],
            'whatsapp' => $validated['whatsapp'],
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
        ]);

        Auth::login($user);

        return redirect()->route('booking.step1')->with('success', 'Akun berhasil dibuat! Silakan lanjutkan pengisian formulir booking.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil keluar.');
    }
}
