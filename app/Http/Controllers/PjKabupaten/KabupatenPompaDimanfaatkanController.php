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

class KabupatenPompaDimanfaatkanController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $kecamatan = $user->region->kecamatan;
        $desa = Desa::whereIn('kecamatan_id', $kecamatan->pluck('id')->unique());
        $pompa = Pompa::whereIn('desa_id', $desa->distinct()->pluck('id')->unique())->where('status_id', 3);
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        if ($request->s) {
            if ($request->s == 'pending') $pompa = $pompa->where('dimanfaatkan_unit', 0);
            elseif ($request->s == 'ongoing') $pompa = $pompa->where('dimanfaatkan_unit', '!=', 'diterima_unit');
            elseif ($request->s == 'completed') $pompa = $pompa->where('dimanfaatkan_unit', '=', 'diterima_unit');
        }
        $pompa = $pompa->orderByDesc('created_at')->get();
        return view('pj_kabupaten.pompa_dimanfaatkan', [
            'kecamatan' => $kecamatan,
            'pompa' => $pompa
        ]);
    }

    public function detail($id) {
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('data tidak ditemukan');
        return view('pj_kabupaten.detail_pompa_dimanfaatkan', ['pompa' => $pompa]);
    }
    
    public function verify($id) {
        $user = Auth::user();
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('data tidak ditemukan');
        if ($pompa->status_id == 4) return back()->withErrors('data sudah diverifikasi');
        elseif ($pompa->status_id == 2) return back()->withErrors('data sudah ditolak');
        elseif ($pompa->status_id == 1) return back()->withErrors('data belum pada tahap penerimaan');
        if ($pompa->dimanfaatkan_unit != $pompa->diterima_unit) return back()->withErrors('pompa diterima belum dimanfaatkan sepenuhnya');
        $update_data = [
            'updated_by' => $user->id,
            'status_id' => 4,
            'verified_at' => Date::now()
        ];
        $pompa->update($update_data);
        return back()->with('success', 'data berhasil diverifikasi');
    }

    // public function update($id, Request $request) {
    //     $user = Auth::user();
    //     $dimanfaatkan = Pompa::find(Crypt::decryptString($id));
    //     if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
    //     if ($dimanfaatkan->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
    //     $request->validate([
    //         'total_unit' => 'required|min:1'
    //     ], [
    //         'total_unit.required' => 'jumlah unit dimanfaatkan tidak boleh kosong',
    //         'total_unit.min' => 'jumlah unit dimanfaatkan tidak boleh kurang dari 1'
    //     ]);
    //     if ($request->total_unit > $dimanfaatkan->pompa_diterima->total_unit) return back()->withErrors('jumlah unit dimanfaatkan tidak boleh lebih dari jumlah unit diusulkan');
    //     if (!$dimanfaatkan->update([
    //         'total_unit' => $request->total_unit,
    //         'updated_by' => $user->id,
    //         'status' => null
    //     ])) return back()->withErrors('terjadi kesalahan');
    //     return back()->with('success', 'data pompa dimanfaatkan berhasil diperbarui');
    // }

    // public function approve($id) {
    //     $dimanfaatkan = Pompa::find(Crypt::decryptString($id));
    //     if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
    //     if (!$dimanfaatkan->update([
    //         'status' => 'diverifikasi',
    //         'verified_at' => Date::now()
    //     ])) return back()->withErrors('terjadi kesalahan');
    //     return back()->with('success', 'data pompa dimanfaatkan berhasil diverifikasi');
    // }

    // public function deny($id) {
    //     $dimanfaatkan = Pompa::find(Crypt::decryptString($id));
    //     if (!$dimanfaatkan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
    //     if (!$dimanfaatkan->update([
    //         'status' => 'ditolak'
    //     ])) return back()->withErrors('terjadi kesalahan');
    //     return back()->with('success', 'data pompa dimanfaatkan berhasil ditolak');
    // }

}
