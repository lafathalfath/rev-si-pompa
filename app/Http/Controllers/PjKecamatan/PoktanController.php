<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Poktan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PoktanController extends Controller
{
    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|unique:poktan',
            'phone_number' => 'required|string',
            'desa_id' => 'required|numeric',
            'address' => 'required|string',
            'luas_lahan' => 'required|numeric',
            'ktp' => 'required|file|mimes:pdf',
            'bukti_kepemilikan' => 'required|array',
            'bukti_kepemilikan.*' => 'file|mimes:pdf'
        ], [
            'name.required' => 'nama kelompok tani tidak boleh kosong',
            'name.unique' => 'nama kelompok tani sudah ada',
            'phone_number.required' => 'no. hp kelompok tani tidak boleh kosong',
            'desa_id.required' => 'desa tidak boleh kosong',
            'address.required' => 'alamat kelompok tani tidak boleh kosong',
            'luas_lahan.required' => 'luas lahan tidak boleh kosong',
            'ktp.required' => 'ktp tidak boleh kosong',
            'ktp.file' => 'format tidak didukung',
            'ktp.mimes' => 'file tidak didukung',
            'bukti_kepemilikan.required' => 'bukti kepemilikan tanah tidak boleh kosong',
            'bukti_kepemilikan.*.file' => 'format tidak didukung',
            'bukti_kepemilikan.*.mimes' => 'file tidak didukung',
        ]);
        $filename_ktp = $request->ktp->hashName();
        $target_dir = storage_path('/app/public/');
        if (!File::exists($target_dir)) File::makeDirectory($target_dir, 0755, true);
        $request->ktp->move($target_dir, $filename_ktp);
        $bukti_kepemilikan = [];
        $bukti_urls = [];
        foreach ($request->bukti_kepemilikan as $buk) {
            $filename_bukti = $buk->hashName();
            $buk->move($target_dir, $filename_bukti);
            $bukti_urls[] = "/storage/$filename_bukti";
            $bukti_kepemilikan[] = [
                'url' => "/storage/$filename_bukti",
                'created_by' => $user->id
            ];
        }
        if (Document::insert($bukti_kepemilikan)) $bukti_kepemilikan = Document::select('id')->whereIn('url', $bukti_urls)->distinct()->pluck('id')->unique();
        $poktan = [
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'desa_id' => $request->desa_id,
                'address' => $request->address,
                'luas_lahan' => $request->luas_lahan,
                'ktp' => "/storage/$filename_ktp",
                'created_by' => $user->id
        ];
        $poktan = Poktan::create($poktan);
        $poktan->kepemilikan_tanah()->sync($bukti_kepemilikan);
        return redirect()->route('kecamatan.usulan.create', ['poktan' => Crypt::encryptString($poktan->id)])->with('success', 'Kelompok tani baru berhasil dibuat');
    }
}
