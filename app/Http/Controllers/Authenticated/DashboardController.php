<?php

namespace App\Http\Controllers\Authenticated;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    
    public function index() {
        return view('dashboard');
    }

    public function activation() {
        return view('activation');
    }

    public function activate(Request $request) {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'name.required' => 'Username tidak boleh kosong',
            'name.unique' => 'Username tidak bisa digunakan',
            'phone_number.required' => 'No. HP tidak boleh kosong',
            'phone_number.unique' => 'No. HP tidak bisa digunakan',
            'email.required' => 'Email tidak boleh kosong',
            'email.emaail' => 'Email tidak valid',
            'email.unique' => 'Email tidak bisa digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password tidak valid',
            'password.confirmed' => 'Konfirmasi password salah'
        ]);
        // dd($request->all());
        $user = Auth::user();
        $user = User::find($user->id);
        if ($user->nip == $request->name) return back()->withErrors('Username tidak boleh sama dengan NIP');
        if ($user->nip == $request->password) return back()->withErrors('Password tidak boleh sama dengan NIP');
        $update_data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'password_changed' => true
        ];
        $updated = $user->update($update_data);
        if (!$updated) return back()->withErrors('Terjadi Kesalahan');
        return redirect()->route('dashboard')->with('success', 'Aktifasi berhasil');
    }

}
