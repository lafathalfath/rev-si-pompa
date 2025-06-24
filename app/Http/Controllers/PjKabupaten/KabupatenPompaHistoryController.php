<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Pompa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KabupatenPompaHistoryController extends Controller
{
    
    public function verified() {
        $user = Auth::user();
        $region = $user->region;
        if (!$region->kecamatan) return back()->withErrors('forbidden');
        $desa_ids = Desa::select('id')
            ->whereIn('kecamatan_id', $region->kecamatan->pluck('id')->unique())
            ->distinct()
            ->pluck('id')->unique();
        $pompa = Pompa::whereIn('desa_id', $desa_ids)
            ->where('status_id', 4)
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kabupaten.verified_history', [
            'pompa' => $pompa,
            'kecamatan' => $region->kecamatan
    ]);
    }
    
    public function denied() {
        $user = Auth::user();
        $region = $user->region;
        if (!$region->kecamatan) return back()->withErrors('forbidden');
        $desa_ids = Desa::select('id')
            ->whereIn('kecamatan_id', $region->kecamatan->pluck('id')->unique())
            ->distinct()
            ->pluck('id')->unique();
        $pompa = Pompa::whereIn('desa_id', $desa_ids)
            ->where('status_id', 2)
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kabupaten.denied_history', [
            'pompa' => $pompa,
            'kecamatan' => $region->kecamatan
    ]);
    }

}
