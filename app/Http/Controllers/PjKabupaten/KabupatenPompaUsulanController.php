<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use App\Mail\NotificationEmail;
use App\Models\Desa;
use App\Models\Notification;
use App\Models\NotificationLink;
use App\Models\Pompa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class KabupatenPompaUsulanController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $token = User::find($user->id)->createToken($user->name);
        $token = str_replace($token->accessToken->id.'|', '', $token->plainTextToken);
        $kecamatan_id = $user->region->kecamatan->pluck('id')->unique();
        $desa_id = Desa::whereIn('kecamatan_id', $kecamatan_id)
            ->select('id')
            ->distinct()
            ->pluck('id')->unique();
        $pompa = [];
        $pompa = Pompa::whereIn('desa_id', $desa_id)->where('status_id', 1);
        if ($request->src) $pompa = $pompa->where('id', Crypt::decryptString($request->src));
        $pompa = $pompa->orderByDesc('created_at')->get();
        $kecamatan = $user->region->kecamatan->select('id', 'name');
        return view('pj_kabupaten.pompa_usulan', [
            'pompa' => $pompa,
            'kecamatan' => $kecamatan,
            'api_token' => $token
        ]);
    }

    public function update($id, Request $request) {
        $user = Auth::user();
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('Data tidak ditemukan');
        if ($pompa->status == 'diverifikasi') return back()->withErrors('data yang telah diverifikasi tidak dapat diubah');
        $request->validate([
            'luas_lahan' => 'required|numeric',
            'diusulkan_unit' => 'required|numeric|min:1'
        ], [
            'luas_lahan.required' => 'Luas lahan tidak boleh kosong',
            'diusulkan_unit.required' => 'Jumlah pompa diusulkan tidak boleh kosong',
            'diusulkan_unit.min' => 'Total unit tidak boleh kosong'
        ]);
        if ($request->luas_lahan > $pompa->poktan->luas_lahan) return back()->withErrors('Usulan luas lahan tidak boleh lebih dari luas lahan dimiliki kelompok tani');
        $data = [
            'luas_lahan' => $request->luas_lahan,
            'diusulkan_unit' => $request->diusulkan_unit,
            'updated_by' => $user->id
        ];
        if (!$pompa->update($data)) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil diperbarui');
    }

    public function approve($id, Request $request) {
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('Data tidak ditemukan');
        if ($pompa->status_id != 1) return back()->withErrors('status sudah diperbarui');
        $request->validate([
            'diterima_unit' => 'required|min:1'
        ], [
            'diterima_unit.required' => 'jumlah pompa diterima tidak boleh kosong',
            'diterima_unit.min' => 'jumlah pompa diterima tidak boleh kurang dari 1',
        ]);
        if ($request->diterima_unit > $pompa->diusulkan_unit) return back()->withErrors('Jumlah pompa diterima tidak boleh lebih dari pompa diusulkan');
        $update = $pompa->update([
            'diterima_unit' => $request->diterima_unit,
            'status_id' => 3
        ]);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil disetujui');
    }

    public function deny($id) {
        $user = Auth::user();
        $pompa = Pompa::find(Crypt::decryptString($id));
        if (!$pompa) return back()->withErrors('Data tidak ditemukan');
        if ($pompa->status_id != 1) return back()->withErrors('status sudah diperbarui');
        $notification_data = [
            'sender_id' => $user->id,
            'receiver_id' => $pompa->created_by,
            'subject' => 'Usulan Ditolak',
            'title' => 'Usulan Pompa Kelompok Tani '. $pompa->poktan->name,
            'message' => "Penanggung Jawab Kabupaten ". $user->region->name ." menolak usulan pompa untuk kelompok tani ". $pompa->poktan->name ." di desa ". $pompa->desa->name ."."
        ];
        $notification = Notification::create($notification_data);
        $link = [
            'notification_id' => $notification->id,
            'name' => 'buka halaman pompa ditolak',
            'url' => route('kecamatan.history.denied', ['src' => Crypt::encryptString($pompa->id)])
        ];
        NotificationLink::create($link);
        $pj_kecamatan_email = $pompa->desa->kecamatan->pj->email;
        Mail::to($pj_kecamatan_email)->send(new NotificationEmail([...$notification_data, 'links' => [$link]]));
        $update = $pompa->update([
            'status_id' => 2,
            'updated_by' => $user->id
        ]);
        if (!$update) return back()->withErrors('terjadi kesalahan');
        return back()->with('success', 'data berhasil ditolak');
    }

}
