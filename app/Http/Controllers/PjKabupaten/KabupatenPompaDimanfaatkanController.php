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
        $pompa = Pompa::whereIn('desa_id', $desa->distinct()->pluck('id')->unique())->where(function ($q) {
            $q->where('status_id', 3)
            ->orWhere('status_id', 5);
        });
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        if ($request->s) {
            if ($request->s == 'pending') $pompa = $pompa->where('dimanfaatkan_unit', 0);
            elseif ($request->s == 'ongoing') $pompa = $pompa->whereColumn('dimanfaatkan_unit', '!=', 'diterima_unit');
            elseif ($request->s == 'completed') $pompa = $pompa->whereColumn('dimanfaatkan_unit', '=', 'diterima_unit');
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
        elseif ($pompa->status_id == 3) return back()->withErrors('data belum siap diverifikasi');
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

}
