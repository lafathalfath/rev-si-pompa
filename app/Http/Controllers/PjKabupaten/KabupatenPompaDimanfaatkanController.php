<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\PompaDimanfaatkan;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;

class KabupatenPompaDimanfaatkanController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan = $user->region->kecamatan;
        $desa = Desa::whereIn('kecamatan_id', $kecamatan->pluck('id'));
        $usulan = PompaUsulan::whereIn('desa_id', $desa->distinct()->pluck('id'));
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan->distinct()->pluck('id'));
        $dimanfaatkan = PompaDimanfaatkan::whereIn('pompa_diterima_id', $diterima->distinct()->pluck('id'))
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kabupaten.pompa_dimanfaatkan', [
            'api_token' => $token,
            'kecamatan' => $kecamatan,
            'dimanfaatkan' => $dimanfaatkan
        ]);
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $dimanfaatkan = PompaDimanfaatkan::find(Crypt::decryptString($id));
        if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        $request->validate([
            'total_unit' => 'required|min:1'
        ], [
            'total_unit.required' => 'jumlah unit dimanfaatkan tidak boleh kosong',
            'total_unit.min' => 'jumlah unit dimanfaatkan tidak boleh kurang dari 1'
        ]);
        if ($request->total_unit > $dimanfaatkan->pompa_diterima->total_unit) return back()->withErrors('jumlah unit dimanfaatkan tidak boleh lebih dari jumlah unit diusulkan');
        if (!$dimanfaatkan->update([
            'total_unit' => $request->total_unit,
            'updated_by' => $user->id,
            'status' => null
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil diperbarui');
    }

    public function approve($id) {
        $dimanfaatkan = PompaDimanfaatkan::find(Crypt::decryptString($id));
        if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        if (!$dimanfaatkan->update([
            'status' => 'diverifikasi',
            'verified_at' => Date::now()
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil diverifikasi');
    }

    public function deny($id) {
        $dimanfaatkan = PompaDimanfaatkan::find(Crypt::decryptString($id));
        if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        if (!$dimanfaatkan->update([
            'status' => 'ditolak'
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil ditolak');
    }

}
