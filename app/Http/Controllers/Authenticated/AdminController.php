<?php

namespace App\Http\Controllers\Authenticated;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Provinsi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function userList() {
        $user_id = Auth::user()->id;
        $users = User::where('is_deleted', false)->whereNot('id', $user_id)->get();
        $role_list = Role::select(['id', 'name'])->get();
        return view('admin.daftar_pj', [
            'users' => $users,
            'role_list' => $role_list
        ]);
    }

    public function createUser(Request $request) {
        $request->validate([
            'nip' => 'required|string|min:16|max:20',
            'name' => 'required|string',
            'role_id' => 'required|numeric',
            'region_id' => 'numeric'
        ], [
            'nip.required' => 'NIP tidak boleh kosong',
            'nip.min' => 'NIP tidak valid',
            'nip.max' => 'NIP tidak valid',
            'name.required' => 'Username tidak boleh kosong',
            'name.unque' => 'Username sudah terdaftar',
            'role_id.required' => 'Role tidak boleh kosong'
        ]);
        $existing_user = User::where('nip', $request->nip)->first();
        $exist_name = User::where('name', $request->name)->first();
        if ($existing_user && !$existing_user->is_deleted) return back()->withErrors('NIP sudah terdaftar');
        if ($exist_name && !$exist_name->is_deleted) return back()->withErrors('Username sudah terdaftar');
        $user = [
            'nip' => $request->nip,
            'name' => $request->name,
            'email' => null,
            'phone_number' => null,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->nip),
            'is_deleted' => false,
            'created_at' => Date::now()
        ];
        if (($request->role_id != 1 || $request->role_id != 2) && !$request->region_id) return back()->withErrors('wilayah penanggung jawab tidak boleh kosong');
        $user_id = $existing_user?->id;
        if ($existing_user) $user = $existing_user->update($user);
        else {
            $user = User::create($user);
            $user_id = $user->id;
        }
        if (!$user) return back()->withErrors('Terjadi kesalahan');
        elseif ($request->role_id == 3) {
            $provinsi = Provinsi::find($request->region_id);
            $provinsi->update(['pj_id' => $user_id]);
        } elseif ($request->role_id == 4) {
            $kabupaten = Kabupaten::find($request->region_id);
            $kabupaten->update(['pj_id' => $user_id]);
        } elseif ($request->role_id == 5) {
            $kecamatan = Kecamatan::find($request->region_id);
            $kecamatan->update(['pj_id' => $user_id]);
        }
        return back()->with('success', 'Penanggung jawab baru berhasil ditambehkan');
    }

    public function editUser($id, Request $request) {
        $id = Crypt::decryptString($id);
        $user = User::find($id);
        if (!$user) return back()->withErrors('Penanggung jawab tidak ditemukan');
        $request->validate([
            'role_id' => 'required|numeric',
            'region_id' => 'numeric'
        ], [
            'role_id.required' => 'Role tidak boleh kosong'
        ]);
        $region = null;
        // $user->update(['role_id' => $request->role_id]);
        if ($request->role_id != 1 || $request->role_id != 2) {
            if (!$request->region_id) return back()->withErrors('wilayah tidak boleh kosong');
            elseif ($request->role_id == 3) $region = new Provinsi();
            elseif ($request->role_id == 4) $region = new Kabupaten();
            elseif ($request->role_id == 5) $region = new Kecamatan();
            $region = $region->find($request->region_id);
            if (!$region) return back()->withErrors('Terjadi kesalahan');
            elseif ($region->pj_id != null) return back()->withErrors('wilayah sudah memiliki penanggung jawab');
            // $region->update(['pj_id', $user->id]);
        }
        $prev_region = $user->region;
        if ($prev_region) $prev_region->update(['pj_id' => null]);
        $user->update(['role_id' => $request->role_id]);
        if ($region) $region->update(['pj_id' => $user->id]);
        return back()->with('success', 'penanggung jawab berhasil diperbarui');
    }

    public function deleteUser($id) {
        $id = Crypt::decryptString($id);
        $user = User::find($id);
        if (!$user) return back()->withErrors('penanggung jawab tidak ditemukan');
        if ($user->region) {
            $region = null;
            if ($user->role_id == 3) $region = new Provinsi();
            elseif ($user->role_id == 4) $region = new Kabupaten();
            elseif ($user->role_id == 5) $region = new Kecamatan();
            $region = $region->where('pj_id', $user->id)->first();
            $region->update(['pj_id' => null]);
        }
        $user->update(['is_deleted' => true]);
        return back()->with('success', 'penanggung jawab berhasil dihapus');
    }
}
