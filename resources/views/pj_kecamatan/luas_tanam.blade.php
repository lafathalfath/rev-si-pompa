@extends('layouts.authenticated')
@section('title')| Luas Tanam Harian @endsection
@section('content')
    
<div>
    <div class="text-xl font-bold">Data Luas Tanam Harian</div>
    
    <div class="mt-5 mb-1 flex justify-between items-center">
        <div class="">
            <div>
                <label for="search_luas_tanam">Cari data pompa diterima</label><br>
                <input type="search" id="search_luas_tanam" oninput="searchData(this)" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400">
            </div>
            <div class="mt-1 flex items-center gap-2">
                <div>
                    <label for="filter_status">Status: </label>
                    <select id="filter_status" oninput="filterStatus(this)" class="py-1 px-2 rounded-sm border-1 border-gray-400">
                        <option value="" selected>Semua</option>
                        <option value="Belum Diverifikasi">Belum Diverifikasi</option>
                        <option value="Terverifikasi">Terverifikasi</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div>
                    <label for="filter_desa">Desa: </label>
                    <select id="filter_desa" oninput="filterDesa(this)" class="py-1 px-2 rounded-sm border-1 border-gray-400">
                        <option value="" selected>Semua</option>
                        @foreach ($desa as $ds)
                            <option value="{{ $ds->name }}">{{ $ds->name }}</option>
                        @endforeach
                    </select>
                </div>
                <a href="" class="btn btn-sm text-white bg-gray-500 hover:bg-gray-600">Bersihkan</a>
            </div>
        </div>
        <div class="flex justify-end"><a href="{{ route('kecamatan.diterima.create') }}" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">+ Tambah Data</a></div>
    </div>
    <table class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kelompok Tani</th>
                <th>Desa</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Pompa Diterima</th>
                <th>Luas Tanam (Ha)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($luas_tanam as $lt)
                <tr>
                    <td id="number_row"></td>
                    <td class="flex items-center justify-between">
                        <div>{{ $lt->pompa_diterima->pompa_usulan->poktan->name }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" 
                            onclick="detailPoktan('{{ $api_token }}', '{{ $lt->pompa_diterima->pompa_usulan->poktan->name }}')"
                        >Detail</button>
                    </td>
                    <td>{{ $lt->pompa_diterima->pompa_usulan->desa->name }}</td>
                    <td>{{ $lt->pompa_diterima->pompa_usulan->luas_lahan }}</td>
                    <td>{{ $lt->pompa_diterima->pompa_usulan->total_unit }}</td>
                    <td>{{ $lt->total_unit }}</td>
                    <td>
                        @if ($lt->status == 'diverifikasi')
                            <div class="badge bg-[#090] text-white font-semibold rounded-sm">Terverifikasi</div>
                        @elseif($lt->status == 'ditolak')
                            <div class="badge text-white bg-red-600 font-semibold rounded-sm">Ditolak</div>
                        @else
                            <div class="badge text-black bg-[#ffc800] font-semibold rounded-sm">Belum Diverifikasi</div>
                        @endif
                    </td>
                    <td>
                        @if ($lt->status != 'diverifikasi')
                            <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                                onclick="editDiterima({{ $lt }}, '{{ route('kecamatan.diterima.update', Crypt::encryptString($lt->id)) }}')"
                            >Edit</button>
                            <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" 
                                onclick="deleteDiterima('{{ route('kecamatan.diterima.destroy', Crypt::encryptString($lt->id)) }}')"
                            >Hapus</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Data Kosong</td></tr>
            @endforelse
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
    <dialog id="edit_luas_tanam_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Edit </h3>
            <form action="" method="POST" id="edit_luas_tanam" class="py-4">
                @csrf
                @method('PUT')
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Desa</label>
                        <input type="text" id="edit_luas_tanam_desa" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Kelompok Tani</label>
                        <input type="text" id="edit_luas_tanam_poktan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Luas Lahan (Ha)</label>
                        <input type="text" id="edit_luas_tanam_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diusulkan</label>
                        <input type="text" id="edit_luas_tanam_diterima_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-col py-1">
                    <label for="total_unit" class="text-semibold">Total Unit Diterima </label>
                    <input type="number" id="edit_luas_tanam_luas" min="0" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="edit_luas_tanam.submit()">Perbarui</button><form method="dialog"><button class="btn" onclick="closeEdit()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <dialog id="delete_luas_tanam_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            <form action="" method="POST" id="delete_luas_tanam" class="py-4">
                @csrf
                @method('DELETE')
                Apakah Anda yakin ingin menghapus data Luas Tanam Harian ini?
            </form>
            <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="delete_luas_tanam.submit()">Hapus</button><form method="dialog"><button class="btn">Batal</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

</div>

<script>
    const numbering = () => {
        const rows = document.querySelectorAll('table tbody tr')
        let index = 1
        rows.forEach(row => {
            if (row.style.display != 'none' && row.children[0].id == 'number_row') {row.children[0].textContent = `${index}`
            index++}
        });
    }
    const searchData = (e) => {
        const {value} = e
        const rows = document.querySelectorAll('table tbody tr')
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value.toLowerCase()) ? '' : 'none'
        });
        numbering()
    }
    const filterStatus = (e) => {
        const {value} = e
        const rows = document.querySelectorAll('table tbody tr')
        rows.forEach(row => {
            const statusCell = row.children[6]
            row.style.display = statusCell.textContent.includes(value) ? '' : 'none'
        });
        numbering()
    }
    const filterDesa = (e) => {
        const {value} = e
        const rows = document.querySelectorAll('table tbody tr')
        rows.forEach(row => {
            const desaCell = row.children[2]
            row.style.display = desaCell.textContent.includes(value) ? '' : 'none'
        });
        numbering()
    }
    const detailPoktan = async (token, poktanName) => {
        if (!poktanName) return
        try {
            const response = await fetch(`/api/poktan/${poktanName}`, {headers: {"Authorization": `Bearer ${token}`}})
            const data = await response.json()
            if (!data) return
            const buktiContainer = document.getElementById('detail_poktan_bukti')
            document.getElementById('detail_poktan_name').value = data.name
            document.getElementById('detail_poktan_phone').value = data.phone_number
            document.getElementById('detail_poktan_address').value = data.full_address
            document.getElementById('detail_poktan_ktp').src = data.ktp
            document.getElementById('detail_poktan_luas_lahan').value = data.luas_lahan
            if (data.kepemilikan_tanah.length) data.kepemilikan_tanah.forEach(kep => {
                var iframe = document.createElement('iframe')
                iframe.src = kep.url
                iframe.class = 'w-full border-1 border-black'
                buktiContainer.appendChild(iframe)
                document.getElementById('detail_poktan_modal').showModal()
            });
        } catch (err) {
            console.error(err.message)
        }
    }
    const showDetailKtp = (e) => {
        const iframe = document.getElementById('detail_poktan_ktp')
        iframe.style.display = iframe.style.display == 'block' ? 'none' : 'block'
        e.innerHTML = e.innerHTML == 'Lihat KTP' ? 'Tutup KTP' : 'Lihat KTP'
    }
    const showBukti = (e) => {
        const container = document.getElementById('detail_poktan_bukti')
        container.style.display = container.style.display == 'flex' ? 'none' : 'flex'
        e.innerHTML = e.innerHTML == 'Lihat Bukti Kepemilikan Lahan' ? 'Tutup Bukti Kepemilikan Lahan' : 'Lihat Bukti Kepemilikan Lahan'
    }
    const editDiterima = (data, route) => {
        document.getElementById('edit_luas_tanam_desa').value = data?.pompa_diterima?.pompa_usulan?.desa?.name
        document.getElementById('edit_luas_tanam_poktan').value = data?.pompa_diterima?.pompa_usulan?.poktan?.name
        document.getElementById('edit_luas_tanam_luas_lahan').value = data?.pompa_diterima?.pompa_usulan?.luas_lahan
        document.getElementById('edit_luas_tanam_diterima_total_unit').value = data?.pompa_diterima?.total_unit
        document.getElementById('edit_luas_tanam_luas').max = data?.pompa_diterima?.pompa_usulan?.luas_lahan
        document.getElementById('edit_luas_tanam_luas').value = data?.total_unit
        document.getElementById('edit_luas_tanam').action = route
        document.getElementById('edit_luas_tanam_modal').showModal()
    }
    const deleteDiterima = (route) => {
        document.getElementById('delete_luas_tanam').action = route
        document.getElementById('delete_luas_tanam_modal').showModal()
    }


    numbering()
</script>

@endsection