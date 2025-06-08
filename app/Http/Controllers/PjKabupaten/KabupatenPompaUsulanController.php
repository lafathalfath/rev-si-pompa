<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;

class KabupatenPompaUsulanController extends Controller
{
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan_id = $user->region->kecamatan->pluck('id');
        $desa_id = Desa::whereIn('kecamatan_id', $kecamatan_id)
            ->select('id')
            ->distinct()
            ->pluck('id');
        $usulan = PompaUsulan::whereIn('desa_id', $desa_id)
            ->orderByDesc('created_at')
            ->get();
        $kecamatan = $user->region->kecamatan->select('id', 'name');
        return view('pj_kabupaten.pompa_usulan', [
            'usulan' => $usulan,
            'kecamatan' => $kecamatan,
            'api_token' => $token
        ]);
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $usulan = PompaUsulan::find(Crypt::decryptString($id));
        if (!$usulan) return back()->withErrors('Data tidak ditemukan');
        if ($usulan->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
        $request->validate([
            'luas_lahan' => 'required|numeric',
            'total_unit' => 'required|numeric|min:1'
        ], [
            'luas_lahan.required' => 'Luas lahan tidak boleh kosong',
            'total_unit.required' => 'Total unit tidak boleh kosong',
            'total_unit.min' => 'Total unit tidak boleh kosong'
        ]);
        $data = [
            'luas_lahan' => $request->luas_lahan,
            'total_unit' => $request->total_unit,
            'updated_by' => $user->id
        ];
        if (!$usulan->update($data)) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil diperbarui');
    }

    public function approve($id) {
        $usulan = PompaUsulan::find(Crypt::decryptString($id));
        if (!$usulan) return back()->withErrors('Data tidak ditemukan');
        if ($usulan->status != null) return back()->withErrors('status sudah diperbarui');
        $update = $usulan->update([
            'verified_at' => Date::now(),
            'status' => 'diverifikasi'
        ]);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil diverifikasi');
    }

    public function deny($id) {
        $usulan = PompaUsulan::find(Crypt::decryptString($id));
        if (!$usulan) return back()->withErrors('Data tidak ditemukan');
        if ($usulan->status != null) return back()->withErrors('status sudah diperbarui');
        $update = $usulan->update([
            'status' => 'ditolak'
        ]);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil ditolak');
    }

}
