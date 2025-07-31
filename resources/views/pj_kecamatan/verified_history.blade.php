@extends('layouts.authenticated')
@section('title')| History Pompa Diverifikasi @endsection

@section('content')
    
<div>
    <div class="text-xl font-bold">History Verifikasi</div>
    
    <div class="mt-5 mb-1 flex justify-between items-center">
        <div class="">
            <div>
                <label for="search_diterima">Cari data history verifikasi</label><br>
                <input type="search" id="search_diterima" oninput="searchData(this)" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400">
            </div>
            <div class="mt-1 flex items-center gap-2">
                <div class="">
                    <label class="text-semibold">Tanggal: </label>
                    <input type="date" id="filter_date_start" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
                    s/d
                    <input type="date" id="filter_date_end" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
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
    </div>
    <table class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Kelompok Tani</th>
                <th>Desa</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Usulan</th>
                <th>Total Diterima</th>
                <th>Total Dimanfaatkan</th>
                <th>Luas Tanam (Ha)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pompa as $pom)
                <tr>
                    <td id="number_row"></td>
                    <td>{{ $pom->created_at }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $pom->poktan->name }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" 
                            onclick="detailPoktan('{{ session('api_token') }}', '{{ $pom->poktan->name }}')"
                        >Detail</button>
                    </td>
                    <td>{{ $pom->desa->name }}</td>
                    <td>{{ $pom->luas_lahan }}</td>
                    <td>{{ $pom->diusulkan_unit }}</td>
                    <td>{{ $pom->diterima_unit }}</td>
                    <td>{{ $pom->dimanfaatkan_unit }}</td>
                    <td>{{ $pom->total_tanam }}</td>
                    <td>
                        <div class="tooltip" data-tip="Pemanfaatan Pompa">
                            <a href="{{ route('kecamatan.history.verified.detail', Crypt::encryptString($pom->id)) }}" class="btn btn-sm bg-[#0a0] hover:bg-[#080] text-white rounded-sm">&#10140;</a>
                        </div>
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
    const filterDate = () => {
        const startEl = document.getElementById('filter_date_start')
        const endEl = document.getElementById('filter_date_end')
        const start = new Date(startEl.value).getTime()
        const end = new Date(endEl.value).getTime()
        if (!start && !end) return
        if (end < start) endEl.value = startEl.value
        const rows = document.querySelectorAll('table tbody tr')
        rows.forEach(row => {
            const date = new Date(row.children[1].textContent.slice(0, -9))
            const dateCellVal = date.getTime()
            let condition = false
            if (start && !end && dateCellVal >= start) condition = true
            else if (!start && end && dateCellVal <= end) condition = true
            else if (start && end && dateCellVal >= start && dateCellVal <= end) condition = true
            else condition = false
            row.style.display = condition ? '' : 'none'
        });
        numbering()
    }
    const filterDesa = (e) => {
        const {value} = e
        const rows = document.querySelectorAll('table tbody tr')
        rows.forEach(row => {
            const desaCell = row.children[3]
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


    numbering()
</script>

@endsection