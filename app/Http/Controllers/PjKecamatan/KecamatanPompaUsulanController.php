<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Poktan;
use App\Models\PompaUsulan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KecamatanPompaUsulanController extends Controller
{
    
    public function index() {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $usulan = DB::table('kecamatan')
            ->where('kecamatan.pj_id', $user->id)
            ->join('desa', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->join('pompa_usulan', 'pompa_usulan.desa_id', '=', 'desa.id')
            ->join('poktan', 'pompa_usulan.poktan_id', '=', 'poktan.id')
            // ->select('pompa_usulan.*', 'desa.* as desa', 'poktan.* as poktan')
            ->select('pompa_usulan.*', 'desa.name as desa', 'poktan.name as poktan')
            ->orderByDesc('created_at')
            ->get();
        $desa = $user->region->desa;
        // dd($usulan);
        // $usulan = $user->region->desa;
        return view('pj_kecamatan.pompa_usulan', [
            'usulan' => $usulan,
            'desa' => $desa,
            'api_token' => $token
        ]);
    }

    public function create(Request $request) {
        // $poktan = Poktan::get();
        $user = Auth::user();
        $user = User::find($user->id);
        $token_creation = $user->createToken($user->name);
        $token = str_replace($token_creation->accessToken->id.'|', '', $token_creation->plainTextToken);
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $poktan = null;
        if ($request->poktan) $poktan = Poktan::find(Crypt::decryptString($request->poktan))->orderByDesc('created_at');
        return view('pj_kecamatan.create_pompa_usulan', [
            'api_token' => $token,
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'poktan' => $poktan
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'poktan_id' => 'required|numeric',
            'desa_id' => 'required|numeric',
            'luas_lahan' => 'required|numeric',
            'total_unit' => 'required|numeric'
        ], [
            'poktan_id.required' => 'kelompok tani tidak boleh kosong',
            'desa_id.required' => 'desa tidak boleh kosong',
            'luas_lahan.required' => 'luas lahan tidak boleh kosong',
            'total_unit.required' => 'jumlah pompa diusulkan tidak boleh kosong'
        ]);
        // dd($request->all());
        $data = [
            'desa_id' => $request->desa_id,
            'poktan_id' => $request->poktan_id,
            'luas_lahan' => $request->luas_lahan,
            'total_unit' => $request->total_unit,
            'created_by' => $user->id,
        ];
        PompaUsulan::create($data);
        return redirect()->route('kecamatan.usulan')->with('success', 'Usulan Berhasil Ditambahkan');
    }

    public function update($id, Request $request) {
        $id = Crypt::decryptString($id);
        $usulan = PompaUsulan::find($id);
        if (!$usulan) return back()->withErrors('data pompa usulan tidak ditemukan');
        if ($usulan->status == 'diverifikasi') return back()->withErrors('data pompa diusulkan sudah diverifikasi');
        $request->validate([
            'desa_id' => 'required|numeric',
            'luas_lahan' => 'required|numeric',
            'total_unit' => 'required|numeric'
        ], [
            'desa_id.required' => 'desa tidak boleh kosong',
            'luas_lahan.required' => 'luas lahan tidak boleh kosong',
            'total_unit.required' => 'jumlah pompa diusulkan tidak boleh kosong'
        ]);
        $data = [
            'desa_id' => $request->desa_id,
            'luas_lahan' => $request->luas_lahan,
            'total_unit' => $request->total_unit,
            'status' => null
        ];
        if ($usulan->update($data)) return back()->with('success', 'data pompa diusulkan berhasil diperbarui');
    }

    public function destroy($id) {
        $usulan = PompaUsulan::find(Crypt::decryptString($id));
        if (!$usulan) return back()->withErrors('data pompa usulan tidak ditemukan');
        if ($usulan->delete()) return back()->with('success', 'data pompa usulan berhasil dihapus');
    }

}
