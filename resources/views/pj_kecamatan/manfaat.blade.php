@extends('layouts.authenticated')
@section('title')| Pompa Dimanfaatkan @endsection
@section('content')
    
<div>
    <div class="text-xl font-bold">Data Pompa Dimanfaatkan</div>
    <div>
            <div lass="flex flex-col" style="margin-bottom: 10px">
                <input type="text" value="Ciapus"> 
                <label for=""> kecamatan</label>
             </div>
            <div lass="flex flex-col" style="margin-bottom: 10px">
                <input type="text" value="Ciapus"> 
                <label for=""> kecamatan</label>
             </div>
            <div lass="flex flex-col" style="margin-bottom: 10px">
                <input type="text" value="Ciapus"> 
                <label for=""> kecamatan</label>
             </div>
    </div>
    <div class="mt-5 mb-1 flex justify-between items-center">
        <div class="flex justify-end"><a href="" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">+ Tambah Data</a></div>
    </div>
    <table class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                {{-- <th>Kelompok Tani</th> --}}
                {{-- <th>Desa</th> --}}
                <th>Luas Lahan (Ha)</th>
                {{-- <th>Total Usulan</th> --}}
                {{-- <th>Total Diterima</th> --}}
                <th>Total Dimanfaatkan</th>
                {{-- <th>Status</th> --}}
                {{-- <th>Aksi</th> --}}
            </tr>
        </thead>
        <tbody>
            {{-- @forelse ($dimanfaatkan as $dt) --}}
                <tr>
                    {{-- <td id="number_row"></td>
                    <td>{{ $dt->created_at }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $dt->pompa_diterima->pompa_usulan->poktan->name }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" 
                            onclick="detailPoktan('{{ $api_token }}', '{{ $dt->pompa_diterima->pompa_usulan->poktan->name }}')"
                        >Detail</button>
                    </td> --}}
                    {{-- <td>{{ $dt->pompa_diterima->pompa_usulan->desa->name }}</td>
                    <td>{{ $dt->pompa_diterima->pompa_usulan->luas_lahan }}</td>
                    <td>{{ $dt->pompa_diterima->pompa_usulan->total_unit }}</td>
                    <td>{{ $dt->pompa_diterima->total_unit }}</td>
                    <td>{{ $dt->total_unit }}</td>
                    <td>
                        @if ($dt->status == 'diverifikasi')
                            <div class="badge bg-[#090] text-white font-semibold rounded-sm">Terverifikasi</div>
                        @elseif($dt->status == 'ditolak')
                            <div class="badge text-white bg-red-600 font-semibold rounded-sm">Ditolak</div>
                        @else
                            <div class="badge text-black bg-[#ffc800] font-semibold rounded-sm">Belum Diverifikasi</div>
                        @endif
                    </td> --}}

                    {{-- <td>h</td>
                    <td>c</td>
                    <td></td>
                    <td>d</td>
                    <td>d</td> --}}
                    <td>d</td>
                    <td>d</td>
                    <td>d</td>
                    <td>d</td>
                    <td>
                            <a class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                            href="/manfaat_pengisiandata"
                            >Edit</a>
                            <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" >Hapus</button>
                    </td>
                </tr>
            {{-- @empty
                <tr><td colspan="9" class="text-center">Data Kosong</td></tr>
            @endforelse --}}
        </tbody>
    </table>

    <dialog id="detail_poktan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Detail Kelompok Tani</h3>
            <div id="detail_poktan" class="py-4">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Nama Kelompok Tani</label>
                    <input type="text" id="detail_poktan_name" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">No. HP</label>
                    <input type="text" id="detail_poktan_phone" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Alamat</label>
                    <textarea id="detail_poktan_address" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled></textarea>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">KTP</label>
                    <iframe src="" frameborder="0" class="border-1 border-black" id="detail_poktan_ktp" style="display: none;"></iframe>
                    <button type="button" class="btn btn-sm text-black rounded-sm bg-[#0bf] hover:bg-[#0ae]" onclick="showDetailKtp(this)">Lihat KTP</button>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Luas Lahan Dimiliki (Ha)</label>
                    <input type="text" id="detail_poktan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Bukti Kepemilikan Lahan</label>
                    <div id="detail_poktan_bukti" class="w-full flex flex-col gap-1" style="display: none;"></div>
                    <button type="button" class="btn btn-sm text-black rounded-sm bg-[#0bf] hover:bg-[#0ae]" onclick="showBukti(this)">Lihat Bukti Kepemilikan Lahan</button>
                </div>
            </div>
            <div class="modal-action"><form method="dialog"><button class="btn">tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <dialog id="edit_dimanfaatkan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Edit </h3>
            <form action="" method="POST" id="edit_dimanfaatkan" class="py-4">
                {{-- @csrf
                @method('PUT') --}}
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Desa</label>
                        <input type="text" id="edit_dimanfaatkan_desa" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Kelompok Tani</label>
                        <input type="text" id="edit_dimanfaatkan_poktan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Luas Lahan (Ha)</label>
                        <input type="text" id="edit_dimanfaatkan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diusulkan</label>
                        <input type="text" id="edit_dimanfaatkan_usulan_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diterima</label>
                        <input type="text" id="edit_dimanfaatkan_terima_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-col py-1">
                    <label for="total_unit" class="text-semibold">Total Unit Dimanfaatkan </label>
                    <input type="number" id="edit_dimanfaatkan_total_unit" min="0" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="edit_dimanfaatkan.submit()">Perbarui</button><form method="dialog"><button class="btn" onclick="closeEdit()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <dialog id="delete_dimanfaatkan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            {{-- <form action="" method="POST" id="delete_dimanfaatkan" class="py-4">
                @csrf
                @method('DELETE')
                Apakah Anda yakin ingin menghapus data Pompa Dimanfaatkan ini?
            </form> --}}
            <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="delete_dimanfaatkan.submit()">Hapus</button><form method="dialog"><button class="btn">Batal</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

</div>


@endsection