<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Notification;
use App\Models\NotificationLink;
use App\Models\PemanfaatanPompa;
use App\Models\Pompa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;

class KecamatanPompaDimanfaatkanController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan = $user->region;
        $desa = $kecamatan->desa;
        $pompa = Pompa::whereIn('desa_id', $desa->pluck('id')->unique());
        if ($request->s) {
            if ($request->s == 'pending') $pompa = $pompa->where('dimanfaatkan_unit', '=', 0);
            elseif ($request->s == 'ongoing') $pompa = $pompa->whereColumn('dimanfaatkan_unit', '!=', 'diterima_unit');
            elseif ($request->s == 'completed') $pompa = $pompa->whereColumn('dimanfaatkan_unit', '=', 'diterima_unit');
        }
        $pompa = $pompa->where('status_id', 3)
            ->orderByDesc('created_at')
            ->get();
        return view('pj_kecamatan.pompa_dimanfaatkan', [
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'pompa' => $pompa,
            'api_token' => $token
        ]);
    }

    public function detail($id) {
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('data tidak ditemukan');
        return view('pj_kecamatan.detail_pompa_dimanfaatkan', [
            'pompa' => $pompa
        ]);
    }

    public function store($id, Request $request) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $decrypted_id = Crypt::decryptString($id);
        $pompa = Pompa::find($decrypted_id);
        if (!$pompa) return back()->withErrors('data tidak ditemukan');
        if ($pompa->status_id == 1) return back()->withErrors('belum ada pompa diterima pada data ini');
        if ($pompa->status_id == 2) return back()->withErrors('data sudah ditolak');
        if ($pompa->status_id == 4) return back()->withErrors('data pemanfaatan sudah diverifikasi');
        if ($pompa->diterima_unit == 0) return back()->withErrors('belum ada pompa diterima pada data ini');
        $request->validate([
            'total_unit' => 'required|min:1',
            'luas_tanam' => 'required|min:0.0001',
            'bukti' => 'required|mimes:pdf',
        ], [
            'total_unit.required' => 'jumlah pompa dimanfaatkan tidak boleh kosong',
            'total_unit.min' => 'jumlah pompa dimanfaatkan tidak kurang dari 1',
            'luas_tanam.required' => 'luas tanam tidak boleh kosong',
            'luas_tanam.min' => 'luas tanam kurang dari jumlah minimum',
            'bukti.required' => 'bukti pemanfaatan tidak boleh kosong',
            'bukti.mimes' => 'bukti pemanfaatan tidak sesuai',
        ]);
        if ($request->total_unit > $pompa->diterima_unit) return back()->withErrors('jumlah pompa dimanfaatkan tidak boleh lebih dari jumlah pompa diterima');
        if ($request->luas_tanam > $pompa->luas_lahan) return back()->withErrors('luas pemanfaatan lahan tidak boleh lebih dari luas lahan diusulkan');
        $filename = $request->bukti->hashName();
        $target_dir = storage_path('/app/public/evidence');
        if (!File::exists($target_dir)) File::makeDirectory($target_dir, 0755, true);
        $request->bukti->move($target_dir, $filename);
        $bukti_url = "/storage/evidence/$filename";
        $doc = Document::create([
            'url' => $bukti_url,
            'created_by' => $user->id
        ]);
        PemanfaatanPompa::create([
            'pompa_id' => $decrypted_id,
            'total_unit' => $request->total_unit,
            'luas_tanam' => $request->luas_tanam,
            'bukti_id' => $doc->id,
            'created_by' => $user->id
        ]);
        // if ($pompa->dimanfaatkan_unit + $request->total_unit == $pompa->diterima_unit) {
        //     $notification_data = [
        //         'sender_id' => $user->id,
        //         'receiver_id' => $kecamatan->kabupaten->pj_id,
        //         'subject' => 'Data Baru',
        //         'title' => 'Pemanfaatan Baru Pompa',
        //         'message' => "Penanggung Jawab Kecamatan $kecamatan->name selesai menambahkan pemanfaatan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ".$pompa->desa->name."."
        //     ];
        //     $notification = Notification::create($notification_data);
        //     $link = [
        //         'notification_id' => $notification->id,
        //         'name' => 'buka halaman detail pompa dimanfaatkan',
        //         'url' => route('kabupaten.dimanfaatkan.detail', Crypt::encryptString($pompa->id))
        //     ];
        //     NotificationLink::create($link);
        // }
        $update_data = [
            'dimanfaatkan_unit' => $pompa->dimanfaatkan_unit + $request->total_unit,
            'total_tanam' => $pompa->total_tanam + $request->luas_tanam,
            'updated_by' => $user->id
        ];
        $pompa->update($update_data);
        return redirect()->route('kecamatan.dimanfaatkan.detail', $id)->with('success', 'data pompa dimanfaatkan berhasil ditambahkan');
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $pemanfaatan = PemanfaatanPompa::find(Crypt::decryptString($id));
        if (!$pemanfaatan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        $pompa = $pemanfaatan->pompa;
        $request->validate([
            'total_unit' => 'required|min:1',
            'luas_tanam' => 'required|min:0.0001',
            'bukti' => 'mimes:pdf',
        ], [
            'total_unit.required' => 'jumlah pompa dimanfaatkan tidak boleh kosong',
            'total_unit.min' => 'jumlah pompa dimanfaatkan tidak kurang dari 1',
            'luas_tanam.required' => 'luas tanam tidak boleh kosong',
            'luas_tanam.min' => 'luas tanam kurang dari jumlah minimum',
            'bukti.mimes' => 'bukti pemanfaatan tidak sesuai',
        ]);
        if ($request->total_unit > $pompa->diterima_unit) return back()->withErrors('jumlah pompa dimanfaatkan tidak boleh lebih dari jumlah pompa diterima');
        if ($request->luas_tanam > $pompa->luas_lahan) return back()->withErrors('luas pemanfaatan lahan tidak boleh lebih dari luas lahan diusulkan');
        $update_pompa_data = [
            'dimanfaatkan_unit' => $pompa->dimanfaatkan_unit - $pemanfaatan->total_unit + $request->total_unit,
            'total_tanam' => $pompa->total_tanam - $pemanfaatan->luas_tanam + $request->luas_tanam
        ];
        $update_data = [
            'total_unit' => $request->total_unit,
            'luas_tanam' => $request->luas_tanam,
            'updated_by' => $user->id
        ];
        $prev_doc = null;
        if ($request->hasFile('bukti')) {
            $prev_doc = Document::find($pemanfaatan->bukti_id);
            $filename = $request->bukti->hashName();
            $target_dir = storage_path('/app/public/evidence');
            if (!File::exists($target_dir)) File::makeDirectory($target_dir, 0755, true);
            $request->bukti->move($target_dir, $filename);
            $bukti_url = "/storage/evidence/$filename";
            $doc = Document::create([
                'url' => $bukti_url,
                'created_by' => $user->id
            ]);
            $update_data['bukti_id'] = $doc->id;
        }
        $prev_total_unit = $pemanfaatan->total_unit;
        $update = $pemanfaatan->update($update_data);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        if ($prev_doc) $prev_doc->delete();
        $update_to_complete = $pompa->dimanfaatkan_unit + $request->total_unit - $prev_total_unit == $pompa->diterima_unit;
        // if ($pompa->dimanfaatkan_unit == $pompa->diterima_unit || $update_to_complete) {
        //     $message_action = $update_to_complete ? 'selesai menambahkan' : 'mengubah';
        //     $notification_data = [
        //         'sender_id' => $user->id,
        //         'receiver_id' => $kecamatan->kabupaten->pj_id,
        //         'subject' => $update_to_complete ? 'Data Baru' : 'Data Diubah',
        //         'title' => $update_to_complete ? 'Pemanfaatan Baru Pompa' : 'Perubahan Pemanfaatan Pompa',
        //         'message' => "Penanggung Jawab Kecamatan $kecamatan->name ". $message_action." pemanfaatan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ".$pompa->desa->name."."
        //     ];
        //     $notification = Notification::create($notification_data);
        //     $link = [
        //         'notification_id' => $notification->id,
        //         'name' => 'buka halaman detail pompa dimanfaatkan',
        //         'url' => route('kabupaten.dimanfaatkan.detail', Crypt::encryptString($pompa->id))
        //     ];
        //     NotificationLink::create($link);
        // }
        $update_pompa = $pompa->update($update_pompa_data);
        if (!$update_pompa) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data pompa dimanfaatkan berhasil diperbarui');
    }
    
    public function destroy($id) {
        $user = Auth::user();
        $kecamatan = $user->region;
        $pemanfaatan = PemanfaatanPompa::find(Crypt::decryptString($id));
        if (!$pemanfaatan) return back()->withErrors('data pompa dimanfaatkan tidak ditemukan');
        $pompa = $pemanfaatan->pompa;
        $update_pompa_data = [
            'dimanfaatkan_unit' => $pompa->dimanfaatkan_unit - $pemanfaatan->total_unit,
            'total_tanam' => $pompa->total_tanam - $pemanfaatan->luas_tanam
        ];
        // if ($pompa->dimanfaatkan_unit == $pompa->diterima_unit) {
        //     $notification_data = [
        //         'sender_id' => $user->id,
        //         'receiver_id' => $kecamatan->kabupaten->pj_id,
        //         'subject' => 'Data Dihapus',
        //         'title' => 'Pemanfaatan Pompa Dihapus',
        //         'message' => "Penanggung Jawab Kecamatan $kecamatan->name menghapus pemanfaatan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ".$pompa->desa->name."."
        //     ];
        //     $notification = Notification::create($notification_data);
        //     $link = [
        //         'notification_id' => $notification->id,
        //         'name' => 'buka halaman detail pompa dimanfaatkan',
        //         'url' => route('kabupaten.dimanfaatkan.detail', Crypt::encryptString($pompa->id))
        //     ];
        //     NotificationLink::create($link);
        // }
        $pompa->update($update_pompa_data);
        $bukti = $pemanfaatan->bukti;
        if ($bukti) {
            $bukti_ent = Document::find($bukti->id);
            $bukti_path = str_replace('/storage', storage_path('/app/public'), $bukti->url);
            if (File::exists($bukti_path)) File::delete($bukti_path);
            $pemanfaatan->delete();
            $bukti_ent->delete();
        }
        return back()->with('success', 'data pompa dimanfaatkan berhasil dihapus');
    }
}
