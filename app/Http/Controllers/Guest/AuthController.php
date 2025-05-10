<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    public function loginView() { return view('guest.login'); }

    public function login(Request $request) {
        $request->validate([
            'name_or_email_or_nip' => 'required|string',
            'password' => 'required|string|min:8'
        ], [
            'name_or_email_or_nip.required' => 'username atau email tidak boleh kosong',
            'password.required' => 'password tidak boleh kosong',
            'password.min' => 'password tidak valid'
        ]);
        $attempt_by = 'email';
        $user = null;
        if (str_contains($request->name_or_email_or_nip, '@')) $user = User::where('email', $request->name_or_email_or_nip)->first();
        else {
            $attempt_by = 'name';
            $user = User::where('name', $request->name_or_email_or_nip)->first();
            if (!$user) {
                $attempt_by = 'nip';
                $user = User::where('nip', $request->name_or_email_or_nip)->first();
            }
        }
        if (!$user) return back()->withErrors('username atau email atau nip tidak terdaftar')->withInput();
        if ($user->is_deleted) return back()->withErrors('username atau email atau nip tidak terdaftar');
        $isPasswordValid = Hash::check($request->password, $user->password);
        if (!$isPasswordValid) return back()->withErrors('password tidak valid')->withInput();
        $attempt = Auth::attempt([$attempt_by => $request->name_or_email_or_nip, 'password' => $request->password]);
        if (!$attempt) return back()->withErrors('terjadi kesalahan');
        // dd($attempt);
        return back()->with('success', 'login berhasil')->withInput();
        return redirect()->route('dashboard')->with('success', 'login berhasil');
    }

    // public function register(Request $request) {
    //     $request->validate([
    //         'name' => 'required|string|unique:users',
    //         'email' => 'required|email|unique:users',
    //         'phone_number' => 'required|min:10',
    //         'password' => 'required|string|max:8|confirmed'
    //     ], [
    //         'name.required' => 'username tidak boleh kosong',
    //         'name.unique' => 'username sudah terdaftar',
    //         'email.required' => 'email tidak boleh kosong',
    //         'email.unique' => 'email sudah terdaftar',
    //         'password.required' => 'password tidak boleh kosong',
    //         'password.max' => 'password tidak valid',
    //         'password.confirmed' => 'konfirmasi password tidak valid'
    //     ]);
    //     $cred = [
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone_number' => $request->phone_number,
    //         'password' => $request->password,
    //     ]
    // }

    public function logout() {
        Auth::logout();
        return redirect()->route('auth.login')->with('success', 'logout berhasil');
    }

}
