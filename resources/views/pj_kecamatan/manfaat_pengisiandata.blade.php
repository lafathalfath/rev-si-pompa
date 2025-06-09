@extends('layouts.authenticated')
@section('title')| Pengisian Data Pompa Dimanfaatkan @endsection
@section('content')
    
<div>
    <div class="text-xl font-bold"> Pengisian Data Pompa Dimanfaatkan </div>
    

    <div class="w-full flex justify-center mt-5 mb-1">
        <div class="w-full px-5 py-2 flex flex-col gap-2 border-1 border-gray-300 rounded-lg shadow-xl">
            <div class="flex items-center w-full">
                <label for="" class="w-52">Desa</label>
                <div class="w-full font-semibold">: {{ $diterima->pompa_usulan->desa->name }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Kelompok Tani</label>
                <div class="w-full font-semibold">: {{ $diterima->pompa_usulan->poktan->name }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Luas Lahan</label>
                <div class="w-full font-semibold">: {{ $diterima->pompa_usulan->luas_lahan }} Ha</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Total Luas Tanam</label>
                <div class="w-full font-semibold">: 0 Ha</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Total Unit Diusulkan</label>
                <div class="w-full font-semibold">: {{ $diterima->pompa_usulan->total_unit }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Total Unit Diterima</label>
                <div class="w-full font-semibold">: {{ $diterima->total_unit }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-52">Total Unit Dimanfaatkan</label>
                <div class="w-full font-semibold">: 0</div>
            </div>
        </div>
    </div>
    <div class="mt-2 mb-2 flex justify-end">
        <div class="flex justify-end"><button onclick="add_dimanfaatkan_modal.showModal()" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">+ Tambah Data</button></div>
    </div>
    
    <div>
        <table class="w-full">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Luas Tanam (Ha)</th>
                    <th>Total Dimanfaatkan</th>
                    <th>Bukti</th>
                </tr>
            </thead> 
            <tbody class="text-center">
                    <tr>
                        <td>1</td>
                        <td>yyyy-mm-dd hh:mm:ss</td>
                        <td>3.24</td>
                        <td>5</td>
                        <td class="flex justify-center"><a href="" class="btn btn-sm bg-[#0ae] hover:bg-[#08c] text-white rounded-sm">Detail</a></td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>

<dialog id="add_dimanfaatkan_modal" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Edit </h3>
        <form action="" method="POST" id="add_dimanfaatkan" class="py-4">
            @csrf
            @method('PUT')
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Desa</label>
                    <input type="text" id="add_dimanfaatkan_desa" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Kelompok Tani</label>
                    <input type="text" id="add_dimanfaatkan_poktan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Luas Lahan (Ha)</label>
                    <input type="text" id="add_dimanfaatkan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Total Unit Diusulkan</label>
                    <input type="text" id="add_dimanfaatkan_usulan_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Total Unit Diterima</label>
                    <input type="text" id="add_dimanfaatkan_terima_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
            </div>
            <div class="flex flex-col py-1">
                <label for="total_unit" class="text-semibold">Total Unit Dimanfaatkan </label>
                <input type="number" id="add_dimanfaatkan_total_unit" min="0" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
            </div>
        </form>
        <div class="modal-action"><button class="btn bg-[#070] hover:bg-[#060] text-white rounded-sm" onclick="add_dimanfaatkan.submit()">Kirim</button><form method="dialog"><button class="btn" onclick="closeAddModal()">Tutup</button></form></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>


@endsection