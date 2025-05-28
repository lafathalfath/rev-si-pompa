<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
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
        $desa = Desa::whereIn('kecamatan_id', $kecamatan->pluck('id'));
        $usulan = PompaUsulan::whereIn('desa_id', $desa->distinct()->pluck('id'));
        $desa = $desa->get();
        $diterima = PompaDiterima::whereIn('pompa_usulan_id', $usulan->distinct()->pluck('id'))->orderByDesc('created_at')->get();
        $usulan = $usulan->get();
        return view('pj_kabupaten.pompa_diterima', [
            'api_token' => $token,
            // 'desa' => $desa,
            'kecamatan' => $kecamatan,
            'usulan' => $usulan,
            'diterima' => $diterima
        ]);
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $diterima = PompaDiterima::find(Crypt::decryptString($id));
        if (!$diterima) return back()->withErrors('data pompa diterima tidak ditemukan');
        if ($diterima->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
        $request->validate([
            'total_unit' => 'required|min:1'
        ], [
            'total_unit.required' => 'jumlah unit diterima tidak boleh kosong',
            'total_unit.min' => 'jumlah unit diterima tidak boleh kurang dari 1'
        ]);
        if ($request->total_unit > $diterima->pompa_usulan->total_unit) return back()->withErrors('jumlah unit diterima tidak boleh lebih dari jumlah unit diusulkan');
        if (!$diterima->update([
            'total_unit' => $request->total_unit,
            'updated_by' => $user->id,
            'status' => null
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa diterima berhasil diperbarui');
    }

    public function approve($id) {
        $diterima = PompaDiterima::find(Crypt::decryptString($id));
        if (!$diterima) return back()->withErrors('data pompa diterima tidak ditemukan');
        if ($diterima->status != null) return back()->withErrors('status sudah diperbarui');
        if (!$diterima->update([
            'status' => 'diverifikasi',
            'verified_at' => Date::now()
        ])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa diterima berhasil diverifikasi');
    }

    public function deny($id) {
        $diterima = PompaDiterima::find(Crypt::decryptString($id));
        if (!$diterima) return back()->withErrors('data pompa diterima tidak ditemukan');
        if ($diterima->status != null) return back()->withErrors('status sudah diperbarui');
        $diterima = $diterima->update([
            'status' => 'ditolak'
        ]);
        if (!$diterima) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa diterima berhasil ditolak');
    }

}
