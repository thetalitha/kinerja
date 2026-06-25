<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function loginProses(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:8',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', 'Akun kamu telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.');
        }

        if (!in_array($user->role, ['admin', 'mentor'])) {
            Auth::logout();
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', 'Akses ditolak. Hanya admin dan mentor yang diizinkan.');
        }

        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'mentor' => redirect()->route('mentor.dashboard'),
            default  => redirect()->route('login')->with('error', 'Role tidak dikenali.'),
        };
    }
}