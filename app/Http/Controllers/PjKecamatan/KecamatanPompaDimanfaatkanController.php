<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\PompaDimanfaatkan;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class KecamatanPompaDimanfaatkanController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $usulan = PompaUsulan::whereIn('desa_id', $desa->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        $dimanfaatkan = PompaDimanfaatkan::whereIn('pompa_diterima_id', $diterima->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kecamatan.pompa_dimanfaatkan', [
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'dimanfaatkan' => $dimanfaatkan,
            'api_token' => $token
        ]);
    }

    public function create(Request $request) {
        $user = Auth::user();
        $selected_diterima = null;
        if ($request->diterima) $selected_diterima = PompaDiterima::find(Crypt::decryptString($request->diterima));
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $usulan = PompaUsulan::whereIn('desa_id', $desa->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan->pluck('id'))
            ->where('status', 'diverifikasi')
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kecamatan.create_pompa_dimanfaatkan', [
            'selected_diterima' => $selected_diterima,
            'diterima' => $diterima
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'pompa_diterima_id' => 'required|string',
            'total_unit' => 'required'
        ], [
            'pompa_diterima_id.required' => 'pompa diterima tidak boleh kosong',
            'total_unit.required' => 'jumlah pompa dimanfaatkan tidak boleh kosong',
        ]);
        $diterima = PompaDiterima::find(Crypt::decryptString($request->pompa_diterima_id));
        if (!$diterima) return back()->withErrors('data pompa diterima tidak ditemukan');
        if ($request->total_unit > $diterima->total_unit) return back()->withErrors('jumlah pompa dimanfaatkan tidak boleh lebih dari jumlah pompa diterima');
        PompaDimanfaatkan::create([
            'pompa_diterima_Id' => $diterima->id,
            'total_unit' => $request->total_unit,
            'created_by' => $user->id
        ]);
        return redirect()->route('kecamatan.dimanfaatkan')->with('success', 'data pompa dimanfaatkan berhasil ditambahkan');
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $dimanfaatkan = PompaDimanfaatkan::find(Crypt::decryptString($id));
        if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        $request->validate([
            'total_unit' => 'required'
        ], [
            'total_unit.required' => 'jumlah pompa dimanfaatkan tidak boleh kosong',
        ]);
        if ($request->total_unit > $dimanfaatkan->pompa_diterima->total_unit) return back()->withErrors('jumlah pompa dimanfaatkan tidak boleh lebih dari jumlah pompa diterima');
        $update = $dimanfaatkan->update([
            'updated_by' => $user->id,
            'total_unit' => $request->total_unit,
            'status' => null
        ]);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil diperbarui');
    }
    
    public function destroy($id) {
        $dimanfaatkan = PompaDimanfaatkan::find(Crypt::decryptString($id));
        if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        if (!$dimanfaatkan->delete()) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil dihapus');
    }
}
