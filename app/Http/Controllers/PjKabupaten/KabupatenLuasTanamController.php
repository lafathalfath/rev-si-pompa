<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\LuasTanam;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;

class KabupatenLuasTanamController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatans = $user->region->kecamatan;
        $desa_ids = Desa::select('id')->whereIn("kecamatan_id", $kecamatans->pluck('id'))->distinct()->pluck('id');
        $usulan_ids = PompaUsulan::select('id')->whereIn('desa_id', $desa_ids)->distinct()->pluck('id');
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan_ids)->get();
        $luas_tanam = LuasTanam::whereIn('pompa_diterima_id', $diterima->pluck('id'))->orderByDesc('created_at')->get();
        return view('pj_kabupaten.luas_tanam', [
            'diterima' => $diterima,
            'luas_tanam' => $luas_tanam,
            'kecamatan' => $kecamatans,
            'api_token' => $token
        ]);
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $luas_tanam = LuasTanam::find(Crypt::decryptString($id));
        if (!$luas_tanam) return back()->withErrors('data luas tanam harian tidak ditemukan');
        if ($luas_tanam->status == 'diverifikasi') return back()->withErrors('data terverifikasi tidak dapat diubah');
        $request->validate([
            'luas_tanam' => 'required'
        ], [
            'luas_tanam.required' => 'total luas tanam harian tidak boleh kosong'
        ]);
        $diterima = $luas_tanam->pompa_diterima;
        $usulan = $diterima->pompa_usulan;
        if ($request->luas_tanam > $usulan->luas_lahan) return back()->withErrors('luas tanam harian tidak boleh lebih dari luas lahan diusulkan');
        if (!$luas_tanam->update([
            'luas_tanam' => $request->luas_tanam,
            'updated_by' => $user->id
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data luas tanam harian berhasil diperbarui');
    }

    public function approve($id) {
        $luas_tanam = LuasTanam::find(Crypt::decryptString($id));
        if (!$luas_tanam) return back()->withErrors('data luas tanam harian tidak ditemukan');
        if ($luas_tanam->status != null) return back()->withErrors('status sudah diperbarui');
        if (!$luas_tanam->update([
            'status' => 'diverifikasi',
            'verified_at' => Date::now()
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data luas_tanam harian berhasil diverifikasi');
    }

    public function deny($id) {
        $luas_tanam = LuasTanam::find(Crypt::decryptString($id));
        if (!$luas_tanam) return back()->withErrors('data luas tanam harian tidak ditemukan');
        if ($luas_tanam->status != null) return back()->withErrors('status sudah diperbarui');
        if (!$luas_tanam->update([
            'status' => 'ditolak',
            'verified_at' => Date::now()
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data luas_tanam harian berhasil ditolak');
    }

}
