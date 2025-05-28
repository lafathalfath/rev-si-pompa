<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\LuasTanam;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

    public function create(Request $request) {
        $user = Auth::user();
        $selected_diterima = null;
        if ($request->diterima) $selected_diterima = PompaDiterima::find(Crypt::decryptString($request->diterima));
        $desa = $user->region->desa;
        $usulan_ids = PompaUsulan::whereIn('desa_id', $desa->pluck('id'))->distinct()->pluck('id');
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan_ids)
            ->where('status', 'diverifikasi')
            ->whereNot('verified_at', null)
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kecamatan.create_luas_tanam', [
            'selected_diterima' => $selected_diterima,
            'diterima' => $diterima
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'pompa_diterima_id' => 'required',
            'luas_tanam' => 'required'
        ], [
            'pompa_diterima_id.required' => 'pompa diterima tidak boleh kosong',
            'luas_tanam.required' => 'luas tanam tidak boleh kosong',
        ]);
        $diterima = PompaDiterima::find(Crypt::decryptString($request->pompa_diterima_id));
        if (!$diterima) return back()->withErrors('data pompa diterima tidak ditemukan');
        if ($request->luas_tanam > $diterima->pompa_usulan->luas_lahan) return back()->withErrors('luas tanam tidak boleh lebih dari luas lahan diusulkan');
        LuasTanam::create([
            'pompa_diterima_id' => $diterima->id,
            'luas_tanam' => $request->luas_tanam,
            'unit_digunakan' => 0,
            'created_by' => $user->id
        ]);
        return redirect()->route('kecamatan.luas_tanam')->with('success', 'data luas tanam berhasil ditambahkan');
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $luas_tanam = LuasTanam::find(Crypt::decryptString($id));
        if (!$luas_tanam) return back()->withErrors('data luas tanam harian tidak ditemukan');
        if ($luas_tanam->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
        $request->validate([
            'luas_tanam' => 'required'
        ], [
            'luas_tanam.required' => 'total luas tanam harian tidak boleh kosong'
        ]);
        $diterima = $luas_tanam->pompa_diterima;
        $luas_lahan = $diterima->pompa_usulan->luas_lahan;
        if ($request->luas_tanam > $luas_lahan) return back()->withErrors('luas tanam harian tidak boleh lebih dari luas lahan diusulkan');
        if (!$luas_tanam->update([
            'luas_tanam' => $request->luas_tanam,
            'updated_by' => $user->id
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data luas tanam harian berhasil diperbarui');
    }

    public function destroy($id) {
        $luas_tanam = LuasTanam::find(Crypt::decryptString($id));
        if (!$luas_tanam) return back()->withErrors('data luas tanam harian tidak ditemukan');
        if ($luas_tanam->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat dihapus');
        if (!$luas_tanam->delete()) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data luas tanam harian berhasil dihapus');
    }
}
