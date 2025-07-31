@extends('layouts.authenticated')
@section('title')| Pengisian Data Pompa Dimanfaatkan @endsection
@section('content')
    
@php
    $pompa_progress = round($pompa->dimanfaatkan_unit * 100 / $pompa->diterima_unit);
    $luas_tanam_progress = round($pompa->total_tanam * 100 / $pompa->luas_lahan);
@endphp

<div>
    <div class="text-xl font-bold"> Pengisian Data Pompa Dimanfaatkan </div>
    
    
    @if ($pompa->dimanfaatkan_unit == $pompa->diterima_unit)
        @if ($pompa->status_id == 4)
            <div class="mt-2 mb-2 flex items-center justify-end gap-1 text-[#0a0] text-lg font-semibold">
                <div class="w-6 h-6 border-3 border-[#0a0] rounded-full flex items-center justify-center text-center">&#10003;</div>Terverifikasi
            </div>
        @else
            <div class="mt-2 mb-2 flex justify-end">
                <div class="flex justify-end"><button onclick="verify_dimanfaatkan_modal.showModal()" class="btn rounded-sm text-white bg-[#0a0] hover:bg-[#090]">Verifikasi</button></div>
            </div>
        @endif
    @endif

    <div class="w-full flex justify-center mt-2 mb-5 border-1 border-gray-300 rounded-lg shadow-xl">
        <div class="w-2/3 px-5 py-2 flex flex-col gap-2">
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Desa</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->desa->name }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Kelompok Tani</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->poktan->name }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Luas Lahan</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->luas_lahan }} Ha</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Total Luas Tanam</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->total_tanam }} Ha</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Total Unit Diusulkan</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->diusulkan_unit }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Total Unit Diterima</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->diterima_unit }}</div>
            </div>
            <div class="flex items-center w-full">
                <label for="" class="w-1/2">Total Unit Dimanfaatkan</label>
                <div class="w-1/2 font-semibold">: {{ $pompa->dimanfaatkan_unit }}</div>
            </div>
        </div>
        <div class="w-1/3 flex items-center justify-start">
            <div class="flex items-start gap-5">
                <div class="flex flex-col items-center">
                    <div id="progress_percentage" class="radial-progress outline-[0.5rem] outline-gray-300 -outline-offset-[0.5rem]" style="--value:{{ $pompa_progress }};--thickness:0.5rem;" aria-valuenow="{{ $pompa_progress }}" role="progressbar">{{ $pompa_progress }}%</div>
                    <div class="text-center">Pemanfaatan<br>Pompa</div>
                </div>
                <div class="flex flex-col items-center">
                    <div id="progress_percentage" class="radial-progress outline-[0.5rem] outline-gray-300 -outline-offset-[0.5rem]" style="--value:{{ $luas_tanam_progress }};--thickness:0.5rem;" aria-valuenow="{{ $luas_tanam_progress }}" role="progressbar">{{ $luas_tanam_progress }}%</div>
                    <div>Luas Tanam</div>
                </div>
            </div>
        </div>
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
                    {{-- @if ($pompa->status_id == 3) <th>Aksi</th> @endif --}}
                </tr>
            </thead> 
            <tbody class="text-center">
                @foreach ($pompa->pemanfaatan as $pm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pm->created_at }}</td>
                        <td>{{ $pm->luas_tanam }}</td>
                        <td>{{ $pm->total_unit }}</td>
                        <td>
                            <div class="flex justify-center"><a href="{{ $pm->bukti->url }}" target="_blank" class="btn btn-sm bg-[#0ae] hover:bg-[#08c] text-white rounded-sm">Lihat</a></div>
                        </td>
                        {{-- @if ($pompa->status_id == 3) 
                            <td><div class="flex justify-center gap-1">
                                <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="editModal('{{ route('kecamatan.dimanfaatkan.update', Crypt::encryptString($pm->id)) }}', {{ $pm }})">Ubah</button>
                                <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" onclick="deleteModal('{{ route('kecamatan.dimanfaatkan.destroy', Crypt::encryptString($pm->id)) }}')">Hapus</button>
                            </div></td> 
                        @endif --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<dialog id="verify_dimanfaatkan_modal" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Konfirmasi</h3>
        <form action="{{ route('kabupaten.dimanfaatkan.verify', request()->id) }}" method="POST" id="verify_dimanfaatkan" class="py-4">
            @csrf
            @method('PUT')
            Apakan Anda yakin ingin memverifikasi data pompa dimanfaatkan ini?
        </form>
        <div class="modal-action"><button class="btn bg-[#070] hover:bg-[#060] text-white rounded-sm" onclick="verify_dimanfaatkan.submit()">Verifikasi</button><form method="dialog"><button class="btn">Tutup</button></form></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<dialog id="edit_dimanfaatkan_modal" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Ubah Data</h3>
        <form method="POST" id="edit_dimanfaatkan" class="py-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="flex flex-col py-1">
                <label for="total_unit" class="text-semibold">Total Unit Dimanfaatkan<span class="text-red-600">*</span></label>
                <input type="number" id="edit_dimanfaatkan_total_unit" min="1" max="{{ $pompa->diterima_unit - $pompa->dimanfaatkan_unit }}" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-col py-1">
                    <label class="text-semibold" for="edit_dimanfaatkan_luas_tanam">Luas Tanam (Ha)<span class="text-red-600">*</span></label>
                    <input type="number" step="0.0001" min="0.0001" max="{{ $pompa->luas_lahan - $pompa->total_tanam }}" id="edit_dimanfaatkan_luas_tanam" name="luas_tanam" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
            </div>
            <div class="flex flex-col py-1">
                <label for="edit_dimanfaatkan_bukti" class="text-semibold">Bukti Pemanfaatan</label>
                <input type="file" id="edit_dimanfaatkan_bukti" accept="application/pdf" name="bukti" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400">
            </div>
            <span class="text-red-600 text-sm">*) wajib diisi</span>
        </form>
        <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="edit_dimanfaatkan.submit()">Simpan</button><form method="dialog"><button class="btn">Tutup</button></form></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<dialog id="delete_dimanfaatkan_modal" class="modal">
    <div class="modal-box">
        <h3 class="text-lg font-bold">Konfirmasi</h3>
        <form action="" method="POST" id="delete_dimanfaatkan" class="py-4">
            @csrf
            @method('DELETE')
            Apakah Anda yakin ingin menghapus data Pompa Dimanfaatkan ini?
        </form>
        <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="delete_dimanfaatkan.submit()">Hapus</button><form method="dialog"><button class="btn">Batal</button></form></div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
    const progressColor = (percentage) => {
        const value = percentage
        const red_max = 255
        const green_max = 200
        let red = 255
        let green = 0
        if (value <= 50) green += Math.round(green_max * (value*2/100))
        else {
            green = green_max
            red -= Math.round(red_max * ((value-50) * 2/100))
        }
        return `rgb(${red}, ${green}, 0)`
    }
    const showPercentage = () => {
        const element = document.querySelectorAll('#progress_percentage')
        element.forEach(e => {
            const value = parseInt(e.textContent.replace('%', ''))
            e.style.color = `${progressColor(value)}`
        })
    }
    const editModal = (route, data) => {
        document.getElementById('edit_dimanfaatkan').action = route
        document.getElementById('edit_dimanfaatkan_total_unit').value = data.total_unit
        document.getElementById('edit_dimanfaatkan_total_unit').max -= data.total_unit
        document.getElementById('edit_dimanfaatkan_luas_tanam').value = data.luas_tanam
        document.getElementById('edit_dimanfaatkan_luas_tanam').max -= data.luas_tanam
        document.getElementById('edit_dimanfaatkan_modal').showModal()
    }
    const deleteModal = (route) => {
        document.getElementById('delete_dimanfaatkan').action = route
        document.getElementById('delete_dimanfaatkan_modal').showModal()
    }

    showPercentage()
</script>

@endsection