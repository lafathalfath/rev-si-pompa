<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\PompaDiterima;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KecamatanPompaDiterimaController extends Controller
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
        // dd($diterima);
        return view('pj_kecamatan.pompa_diterima', [
            'desa' => $desa,
            'usulan' => $usulan,
            'diterima' => $diterima,
            'api_token' => $token
        ]);
    }

    public function create(Request $request) {
        $user = Auth::user();
        $desa = $user->region->desa;
        $desa_id = $desa->pluck('id');
        $usulan_id = DB::table('pompa_usulan')
            ->whereIn('pompa_usulan.desa_id', $desa_id)
            ->join('pompa_diterima', 'pompa_diterima.pompa_usulan_id', '=', 'pompa_usulan.id')
            ->select('pompa_usulan.id')
            ->distinct()->pluck('id');
        $usulan = PompaUsulan::whereNotIn('id', $usulan_id)
            ->where('status', 'diverifikasi')
            ->orderByDesc('created_at')
            ->get();
        $selected_usulan = null;
        if ($request->usulan) $selected_usulan = PompaUsulan::find(Crypt::decryptString($request->usulan));
        return view('pj_kecamatan.create_pompa_diterima', [
            'usulan' => $usulan,
            'desa' => $desa,
            'selected_usulan' => $selected_usulan
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'pompa_usulan_id' => 'required|string',
            'total_unit' => 'required|numeric|min:1'
        ], [
            'pompa_usulan_id.required' => 'Usulan tidak boleh kosong',
            'total_unit.required' => 'Total unit diterima tidak boleh kosong',
            'total_unit.min' => 'Total unit diterima tidak boleh kosong'
        ]);
        $usulan_id = Crypt::decryptString($request->pompa_usulan_id);
        $data = [
            'pompa_usulan_id' => $usulan_id,
            'total_unit' => $request->total_unit,
            'created_by' => $user->id
        ];
        $pompa_usulan = PompaUsulan::find($usulan_id);
        if (!$pompa_usulan) return back()->withErrors('terjadi kesalahan');
        if ($request->total_unit > $pompa_usulan->total_unit) return back()->withErrors('Jumlah pompa diterima tidak bisa lebih besar dari jumlah pompa diusulkan');
        PompaDiterima::create($data);
        return redirect()->route('kecamatan.diterima')->with('success', 'Data pompa diterima berhasil ditambahkan');
    }

    public function update($id, Request $request) {
        $diterima = PompaDiterima::find(Crypt::decryptString($id));
        if (!$diterima) return back()->withErrors('Data pompa tidak ditemukan');
        if ($diterima->status == 'diverifikasi') return back()->withErrors('Data diverifikasi tidak dapat diubah');
        $request->validate([
            'total_unit' => 'required|numeric|min:1'
        ], [
            'total_unit.required' => 'Total unit diterima tidak boleh kosong',
            'total_unit.min' => 'Total unit diterima tidak boleh kosong'
        ]);
        if ($diterima->pompa_usulan->total_unit < $request->total_unit) return back()->withErrors('Jumlah pompa diterima tidak boleh lebih besar dari jumlah pompa diusulkan');
        if (!$diterima->update(['total_unit' => $request->total_unit, 'status' => null, 'updated_by' => Auth::user()->id])) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'Data pompa diterima berhasil diperbarui');
    }
    
    public function destroy($id) {
        $diterima = PompaDiterima::find(Crypt::decryptString($id));
        if (!$diterima) return back()->withErrors('Data pompa tidak ditemukan');
        if ($diterima->status == 'diverifikasi') return back()->withErrors('Data diverifikasi tidak dapat dihapus');
        $diterima->delete();
        return back()->with('success', 'Data pompa diterima berhasil dihapus');
    }
    
}
