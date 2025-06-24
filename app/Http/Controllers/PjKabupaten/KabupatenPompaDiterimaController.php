<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Pompa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;

class KabupatenPompaDiterimaController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan = $user->region->kecamatan;
        $desa = Desa::whereIn('kecamatan_id', $kecamatan->pluck('id')->unique())->distinct()->pluck('id')->unique();
        $usulan = Pompa::whereIn('desa_id', $desa)
            ->where('status_id', 1)
            ->orderByDesc('created_at')
            ->get();
        $diterima = Pompa::whereIn('desa_id', $desa)
            ->where('status_id', 3)
            ->where('diterima_unit', '>', 'dimanfaatkan_unit')
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kabupaten.pompa_diterima', [
            'api_token' => $token,
            // 'desa' => $desa,
            'kecamatan' => $kecamatan,
            'usulan' => $usulan,
            'diterima' => $diterima
        ]);
    }

}
