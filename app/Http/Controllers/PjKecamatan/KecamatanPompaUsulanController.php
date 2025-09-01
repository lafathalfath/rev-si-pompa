<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Notification;
use App\Models\NotificationLink;
use App\Models\Poktan;
use App\Models\Pompa;
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
        $pompa = DB::table('kecamatan')
            ->where('kecamatan.pj_id', $user->id)
            ->join('desa', 'desa.kecamatan_id', '=', 'kecamatan.id')
            ->join('pompa', 'pompa.desa_id', '=', 'desa.id')
            ->join('poktan', 'pompa.poktan_id', '=', 'poktan.id')
            ->join('users', 'pompa.updated_by', '=', 'users.id')
            ->join('role', 'users.role_id', '=', 'role.id')
            ->where('pompa.status_id', 1)
            ->select('pompa.*', 'desa.name as desa', 'poktan.name as poktan', 'poktan.luas_lahan as poktan_luas_lahan', 'users.name as update_by', 'role.name as update_by_role')
            ->orderByDesc('created_at')
            ->get();
        $desa = $user->region->desa;
        return view('pj_kecamatan.pompa_usulan', [
            'pompa' => $pompa,
            'desa' => $desa,
            'api_token' => $token
        ]);
    }

    public function create(Request $request) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $selected_poktan = null;
        if ($request->poktan) $selected_poktan = Poktan::find(Crypt::decryptString($request->poktan));
        $poktan = Poktan::whereIn('desa_id', $desa->pluck('id')->unique())->get();
        return view('pj_kecamatan.create_pompa_usulan', [
            'kecamatan' => $kecamatan,
            'desa' => $desa,
            'poktan' => $poktan,
            'selected_poktan' => $selected_poktan
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        $request->validate([
            'poktan_id' => 'required|numeric',
            'desa_id' => 'required|numeric',
            'luas_lahan' => 'required|numeric',
            'diusulkan_unit' => 'required|numeric'
        ], [
            'poktan_id.required' => 'kelompok tani tidak boleh kosong',
            'desa_id.required' => 'desa tidak boleh kosong',
            'luas_lahan.required' => 'luas lahan tidak boleh kosong',
            'diusulkan_unit.required' => 'jumlah pompa diusulkan tidak boleh kosong'
        ]);
        $poktan = Poktan::find($request->poktan_id);
        if ($poktan->luas_lahan < $request->luas_lahan) return back()->withErrors('Usulan luas lahan tidak boleh lebih besar dari luas lahan dimiliki kelompok tani');
        $data = [
            'desa_id' => $request->desa_id,
            'poktan_id' => $request->poktan_id,
            'luas_lahan' => $request->luas_lahan,
            'diusulkan_unit' => $request->diusulkan_unit,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ];
        $pompa = Pompa::create($data);
        $desa = Desa::find($request->desa_id);
        $kecamatan = $desa->kecamatan;
        $pj_kabupaten_id = $kecamatan->kabupaten->pj_id;
        // $notification_data = [
        //     'sender_id' => $user->id,
        //     'receiver_id' => $pj_kabupaten_id,
        //     'subject' => 'Data Baru',
        //     'title' => 'Usulan Baru Pompa',
        //     'message' => "Penanggung Jawab Kecamatan $kecamatan->name menambahkan usulan baru pompa untuk kelompok tani ". $pompa->poktan->name ." di desa $desa->name."
        // ];
        // $notification = Notification::create($notification_data);
        // $link = [
        //     'notification_id' => $notification->id,
        //     'name' => 'buka halaman pompa usulan',
        //     'url' => route('kabupaten.usulan', ['src' => Crypt::encryptString($pompa->id)])
        // ];
        // NotificationLink::create($link);
        return redirect()->route('kecamatan.usulan')->with('success', 'Usulan Berhasil Ditambahkan');
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $id = Crypt::decryptString($id);
        $pompa = Pompa::find($id);
        if (!$pompa) return back()->withErrors('data pompa usulan tidak ditemukan');
        if ($pompa->status_id != 1) return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
        $request->validate([
            'desa_id' => 'required|numeric',
            'luas_lahan' => 'required|numeric',
            'diusulkan_unit' => 'required|numeric'
        ], [
            'desa_id.required' => 'desa tidak boleh kosong',
            'luas_lahan.required' => 'luas lahan tidak boleh kosong',
            'diusulkan_unit.required' => 'jumlah pompa diusulkan tidak boleh kosong'
        ]);
        if ($pompa->poktan->luas_lahan < $request->luas_lahan) return back()->withErrors('Usulan luas lahan tidak boleh lebih dari luas lahan dimiliki kelompok tani');
        $data = [
            'desa_id' => $request->desa_id,
            'luas_lahan' => $request->luas_lahan,
            'diusulkan_unit' => $request->diusulkan_unit,
            'updated_by' => $user->id,
            'status_id' => 1
        ];
        // $notification_data = [
        //     'sender_id' => $user->id,
        //     'receiver_id' => $kecamatan->kabupaten->pj_id,
        //     'subject' => 'Data Diperbarui',
        //     'title' => 'Perubahan Usulan Pompa',
        //     'message' => "Penanggung Jawab Kecamatan $kecamatan->name mengubah usulan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ".$pompa->desa->name."."
        // ];
        // $notification = Notification::create($notification_data);
        // $link = [
        //     'notification_id' => $notification->id,
        //     'name' => 'buka halaman pompa usulan',
        //     'url' => route('kabupaten.usulan', ['src' => Crypt::encryptString($pompa->id)])
        // ];
        // NotificationLink::create($link);
        if ($pompa->update($data)) return back()->with('success', 'data pompa diusulkan berhasil diperbarui');
    }

    public function destroy($id) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('data pompa usulan tidak ditemukan');
        if ($pompa->status_id != 1) return back()->withErrors('data yang telah diverifikasi tidak dapat dihapus');
        // $notification_data = [
        //     'sender_id' => $user->id,
        //     'receiver_id' => $kecamatan->kabupaten->pj_id,
        //     'subject' => 'Data Dihapus',
        //     'title' => 'Usulan Pompa Dihapus',
        //     'message' => "Penanggung Jawab Kecamatan $kecamatan->name menghapus usulan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ".$pompa->desa->name."."
        // ];
        // $notification = Notification::create($notification_data);
        // $link = [
        //     'notification_id' => $notification->id,
        //     'name' => 'buka halaman pompa usulan',
        //     'url' => route('kabupaten.usulan')
        // ];
        // NotificationLink::create($link);
        if ($pompa->delete()) return back()->with('success', 'data pompa usulan berhasil dihapus');
    }

}
