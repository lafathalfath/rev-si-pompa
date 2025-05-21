<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\LuasTanam;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KecamatanLuasTanamController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $desa = $user->region->desa;
        $usulan_ids = PompaUsulan::whereIn('desa_id', $desa->pluck('id'))->distinct()->pluck('id');
        $diterima_ids = PompaDiterima::whereIn('pompa_usulan_id', $usulan_ids)->distinct()->pluck('id');
        $luas_tanam = LuasTanam::whereIn('pompa_diterima_id', $diterima_ids)
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kecamatan.luas_tanam', [
            'api_token' => $token,
            'luas_tanam' => $luas_tanam,
            'desa' => $desa
        ]);
    }
}
