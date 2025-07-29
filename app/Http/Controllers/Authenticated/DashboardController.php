<?php

namespace App\Http\Controllers\Authenticated;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Pompa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    
    public function index() {
        $user = Auth::user();
        if ($user->role_id == 4) {
            $kecamatan_ids = $user->region->kecamatan->pluck('id')->unique();
            $desa_ids = Desa::whereIn('kecamatan_id', $kecamatan_ids)->distinct()->pluck('id');

            $usulan_per_kecamatan = DB::table('desa')
                ->whereIn('desa.id', $desa_ids)
                ->join('pompa', 'pompa.desa_id', '=', 'desa.id')
                ->join('kecamatan', 'kecamatan.id', '=', 'desa.kecamatan_id')
                ->select('kecamatan.name', DB::raw('count(pompa.id) as total'))
                ->groupBy('kecamatan.name')
                ->orderByDesc('total')
                ->get();

            $pompaQuery = Pompa::whereIn('desa_id', $desa_ids);
            $total_pompa = $pompaQuery->count();

            $usulan_pending = (clone $pompaQuery)->where('status_id', 1)->count();
            $usulan_ditolak = (clone $pompaQuery)->where('status_id', 2)->count();
            $diverifikasi = (clone $pompaQuery)->where('status_id', 4)->count();

            $pemanfaatan = (clone $pompaQuery)->where('status_id', 3)->get();
            $pemanfaatan_pending = $pemanfaatan->where('dimanfaatkan_unit', 0)->count();
            $pemanfaatan_ongoing = $pemanfaatan->filter(function ($item) {
                return $item->dimanfaatkan_unit != $item->diterima_unit;
            })->count();
            $pemanfaatan_completed = $pemanfaatan->where('dimanfaatkan_unit', '=', 'diterima_unit')->count();
            return view('pj_kabupaten.dashboard', [
                'usulan_per_kecamatan' => $usulan_per_kecamatan,
                'total_pompa' => $total_pompa,
                'usulan_pending' => $usulan_pending,
                'usulan_ditolak' => $usulan_ditolak,
                'pemanfaatan_pending' => $pemanfaatan_pending,
                'pemanfaatan_ongoing' => $pemanfaatan_ongoing,
                'pemanfaatan_completed' => $pemanfaatan_completed,
                'diverifikasi' => $diverifikasi

            ]);
        } elseif ($user->role_id == 5) {
            $desa_ids = $user->region->desa->pluck('id')->unique();

            $usulan_per_desa = DB::table('desa')
                ->whereIn('desa.id', $desa_ids)
                ->join('pompa', 'desa.id', '=', 'pompa.desa_id')
                ->select('desa.name', DB::raw('count(pompa.id) as total'))
                ->groupBy('desa.name')
                ->orderByDesc('total')
                ->get();

            $pompaQuery = Pompa::whereIn('desa_id', $desa_ids);
            $total_pompa = $pompaQuery->count();

            $usulan_pending = (clone $pompaQuery)->where('status_id', 1)->count();
            $usulan_ditolak = (clone $pompaQuery)->where('status_id', 2)->count();
            $diverifikasi = (clone $pompaQuery)->where('status_id', 4)->count();

            $pemanfaatan = (clone $pompaQuery)->where('status_id', 3)->get();
            $pemanfaatan_pending = $pemanfaatan->where('dimanfaatkan_unit', 0)->count();
            $pemanfaatan_ongoing = $pemanfaatan->filter(function ($item) {
                return $item->dimanfaatkan_unit != $item->diterima_unit;
            })->count();
            $pemanfaatan_completed = $pemanfaatan->filter(function ($item) {
                return $item->dimanfaatkan_unit == $item->diterima_unit;
            })->count();
            return view('pj_kecamatan.dashboard', [
                'total_pompa' => $total_pompa,
                'usulan_pending' => $usulan_pending,
                'usulan_ditolak' => $usulan_ditolak,
                'pemanfaatan_pending' => $pemanfaatan_pending,
                'pemanfaatan_ongoing' => $pemanfaatan_ongoing,
                'pemanfaatan_completed' => $pemanfaatan_completed,
                'diverifikasi' => $diverifikasi,
                'usulan_per_desa' => $usulan_per_desa
            ]);
        }
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
        return redirect()->route('dashboard')->with('success', 'Aktivasi berhasil');
    }

}
