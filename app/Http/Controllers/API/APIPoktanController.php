<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Poktan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\error;

class APIPoktanController extends Controller
{
    
    public function getPoktan(Request $request) {
        $user = Auth::user();
        if ($user->role_id != 5) return error('Unauthorized');
        $kecamatan_id = $user->region->id;
        // $poktan = DB::table('poktan')
        //     ->join('desa', 'poktan.desa_id', '=', 'desa.id')
        //     ->join('desa', 'desa.kecamatan_id', '=', $kecamatan_id)
        //     ->select('desa.*');
        $poktan = DB::table('kecamatan')
            ->where('kecamatan.id', '=', $kecamatan_id)
            ->join('desa', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->join('poktan', 'poktan.desa_id', '=', 'desa.id');
        if ($request->search) $poktan = $poktan->where('poktan.name', 'LIKE', "%$request->search%");
        $poktan = $poktan->select('poktan.*', 'desa.name as desa')->get();
        return response()->json($poktan);
    }

    public function getPoktanByName($name) {
        $poktan = Poktan::where('name', $name)
            ->with('kepemilikan_tanah')->first();
        $desa = $poktan->desa;
        $poktan->full_address = $poktan->address.", ".$desa->name.", ".$desa->kecamatan->name.", ".$desa->kecamatan->kabupaten->name.", ".$desa->kecamatan->kabupaten->provinsi->name;
        return response()->json($poktan);
    }
    
}
