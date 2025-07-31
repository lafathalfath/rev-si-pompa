<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Pompa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class KecamatanPompaHistoryController extends Controller
{
    
    public function denied(Request $request) {
        $user = Auth::user();
        $region = $user->region;
        if (!$region->desa) return back()->withErrors('forbidden');
        $desa = $region->desa;
        $pompa = Pompa::whereIn('desa_id', $desa->pluck('id')->unique())->where('status_id', 2);
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        $pompa = $pompa->orderByDesc('created_at')->get();
        return view('pj_kecamatan.denied_history', [
            'pompa' => $pompa,
            'desa' => $region->desa
        ]);
    }
    
    public function verified(Request $request) {
        $user = Auth::user();
        $region = $user->region;
        if (!$region->desa) return back()->withErrors('forbidden');
        $desa = $region->desa;
        $pompa = Pompa::whereIn('desa_id', $desa->pluck('id')->unique())->where('status_id', 4);
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        $pompa = $pompa->orderByDesc('created_at')->get();
        return view('pj_kecamatan.verified_history', [
            'pompa' => $pompa,
            'desa' => $desa
        ]);
    }

}
