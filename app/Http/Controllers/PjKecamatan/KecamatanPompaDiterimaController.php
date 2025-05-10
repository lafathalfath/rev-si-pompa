<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KecamatanPompaDiterimaController extends Controller
{
    public function index() {
        $user = Auth::user();
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $usulan = PompaUsulan::whereIn('desa_id', $desa->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        // dd($diterima);
        return view('pj_kecamatan.pompa_diterima', [
            'desa' => $desa,
            'usulan' => $usulan,
            'diterima' => $diterima
        ]);
    }
}
