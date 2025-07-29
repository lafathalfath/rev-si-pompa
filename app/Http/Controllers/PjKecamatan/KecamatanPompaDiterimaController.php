<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Pompa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KecamatanPompaDiterimaController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $pompa = Pompa::whereIn('desa_id', $desa->pluck('id')->unique())
            ->where('status_id', 3)
            ->whereColumn('diterima_unit', '>', 'dimanfaatkan_unit');
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        $pompa = $pompa->orderByDesc('created_at')->get();
        return view('pj_kecamatan.pompa_diterima', [
            'desa' => $desa,
            'pompa' => $pompa,
            'api_token' => $token
        ]);
    }
}
