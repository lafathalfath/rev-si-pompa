<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIRegionController extends Controller
{
    public function getProvinsi(Request $request) {
        $provinsi = Provinsi::select(['id', 'name']);
        if ($request->search) $provinsi = $provinsi->where('name', 'LIKE', "%$request->search%");
        $provinsi = $provinsi->get();
        return response()->json($provinsi);
    }
    
    
    public function getKabupaten(Request $request) {
        // $kabupaten = Kabupaten::select(['id', 'name']);
        // if ($request->search) $kabupaten = $kabupaten->where('name', 'LIKE', "%$request->search%");
        // $kabupaten = $kabupaten->get();
        $kabupaten = DB::table('kabupaten');
        if ($request->search) $kabupaten = $kabupaten->where('kabupaten.name', 'LIKE', "%$request->search%");
        $kabupaten = $kabupaten
            ->join('provinsi', 'kabupaten.provinsi_id', '=', 'provinsi.id')
            ->select('kabupaten.id', 'kabupaten.name', 'provinsi.name as provinsi')
            ->get();
        return response()->json($kabupaten);
    }
    
    public function getKecamatan(Request $request) {
        // $kecamatan = Kecamatan::select(['id', 'name']);
        // if ($request->search) $kecamatan = $kecamatan->where('name', 'LIKE', "%$request->search%");
        // $kecamatan = $kecamatan->get();
        $kecamatan = DB::table('kecamatan');
        if ($request->search) $kecamatan = $kecamatan->where('kecamatan.name', 'LIKE', "%$request->search%");
        $kecamatan = $kecamatan
            ->join('kabupaten', 'kecamatan.kabupaten_id', '=', 'kabupaten.id')
            ->join('provinsi', 'kabupaten.provinsi_id', '=', 'provinsi.id')
            ->select('kecamatan.id', 'kecamatan.name', 'kabupaten.name as kabupaten', 'provinsi.name as provinsi')
            ->get();
        return response()->json($kecamatan);
    }
    
    public function getDesa(Request $request) {
        // $desa = Desa::select(['id', 'name']);
        // if ($request->search) $desa = $desa->where('name', 'LIKE', "%$request->search%");
        // $desa = $desa->get();
        $desa = DB::table('desa');
        if ($request->search) $desa = $desa->where('desa.name', 'LIKE', "%$request->search%");
        $desa = $desa
            ->join('kecamatan', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->join('kabupaten', 'kecamatan.kabupaten_id', '=', 'kabupaten.id')
            ->join('provinsi', 'kabupaten.provinsi_id', '=', 'provinsi.id')
            ->select('desa.id', 'desa.name', 'kecamatan.name as kecamatan', 'kabupaten.name as kabupaten', 'provinsi.name as provinsi')
            ->get();
        return response()->json($desa);
    }
}
