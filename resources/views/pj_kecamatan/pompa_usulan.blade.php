@extends('layouts.authenticated')
@section('title')| Pompa Usulan @endsection
@section('content')

<div id="alert-container" class="flex flex-col gap-1 w-full"></div>

<div>
    <div class="text-xl font-bold">Data Pompa Diusulkan</div>
    <div class="flex items-end justify-between mt-5">
        <div class="flex flex-col py-1">
            <label for="search" class="text-semibold">Cari Data Pompa Diusulkan</label>
            <input type="search" id="search" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="search(this)">
        </div>
        <a href="{{ route('kecamatan.usulan.create') }}" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">+ Tambah Data</a>
    </div>
    <div class="mt-2 flex flex-wrap gap-2">
        <div class="flex gap-2 items-center">
            <label class="text-semibold">Tanggal: </label>
            <input type="date" id="filter_date_start" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
            s/d
            <input type="date" id="filter_date_end" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
        </div>
        <div class="flex gap-2 items-center">
            <label class="text-semibold">Desa: </label>
            <select class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDesa(this)">
                <option value="semua" selected>semua</option>
                @foreach ($desa as $d)
                    <option value="{{ $d->name }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <a href="" class="btn btn-sm text-white bg-gray-500 hover:bg-gray-600">Bersihkan</a>
    </div>
    <table class="w-full" id="usulan_table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Desa</th>
                <th>Kelompok Tani</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Unit</th>
                {{-- <th>Status</th> --}}
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pompa as $pom)
                <tr>
                    <td id="number_row"></td>
                    <td>{{ $pom->created_at }}</td>
                    <td>{{ $pom->desa }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $pom->poktan }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" onclick="detailPoktan('{{ $api_token }}', '{{ $pom->poktan }}')">Detail</button>
                    </td>
                    <td>{{ $pom->luas_lahan }}</td>
                    <td>{{ $pom->diusulkan_unit }}</td>
                    <td>
                        @if ($pom->status_id == 1)
                            <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                                onclick="editUsulan('{{ route('kecamatan.usulan.update', Crypt::encryptString($pom->id)) }}', {desa_id: {{ $pom->desa_id }}, luas_lahan: {{ $pom->luas_lahan }}, diusulkan_unit: {{ $pom->diusulkan_unit }}, poktan: '{{ $pom->poktan }}' })"
                            >Ubah</button>
                            <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" onclick="deleteUsulan('{{ route('kecamatan.usulan.destroy', Crypt::encryptString($pom->id)) }}')">Hapus</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Data Kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <dialog id="edit_usulan_modal" class="modal">
        <div class="modal-box">
            <div id="alert-container-edit" class="flex flex-col gap-1 w-full"></div>
            <h3 class="text-lg font-bold">Ubah </h3>
            <form action="" method="POST" id="edit_usulan" class="py-4">
                @csrf
                @method('PUT')
                <div class="flex flex-col py-1">
                    <label for="edit_usulan_poktan" class="text-semibold">Kelompok Tani</label>
                    <input type="text" id="edit_usulan_poktan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label for="desa_id" class="text-semibold">Desa</label>
                    <select name="desa_id" id="edit_usulan_desa" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        <option disabled>-- pilih desa --</option>
                        @foreach ($desa as $des)
                            <option value="{{ $des->id }}">{{ $des->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_usulan_luas_lahan" class="text-semibold">Luas Lahan (Ha)</label>
                    <input type="number" step="0.0001" min="0.0001" name="luas_lahan" id="edit_usulan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_usulan_diusulkan_unit" class="text-semibold">Jumlah Pompa Diusulkan</label>
                    <input type="number" min="1" name="diusulkan_unit" id="edit_usulan_diusulkan_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="confirmUpdateUsulan()">Perbarui</button><form method="dialog"><button class="btn" onclick="closeEdit()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <dialog id="delete_usulan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            <form action="" method="POST" id="delete_usulan" class="py-4">
                @csrf
                @method('DELETE')
                Apakah Anda yakin ingin menghapus data Pompa Diusulkan ini?
            </form>
            <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="delete_usulan.submit()">Hapus</button><form method="dialog"><button class="btn">Batal</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

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

    <dialog id="confirm_update_usulan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            Pastikan data yang Anda isi benar! <br>
            Apakah Anda yakin memperbarui data ini?
            <div class="modal-action">
                <button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="edit_usulan.submit()">Ya</button>
                <form method="dialog"><button class="btn">Batal</button></form>
            </div>
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
    const editUsulan = (route, data) => {
        document.getElementById('edit_usulan_modal').showModal()
        document.getElementById('edit_usulan').action = route
        document.getElementById('edit_usulan_desa').value = data.desa_id
        document.getElementById('edit_usulan_luas_lahan').value = data.luas_lahan
        document.getElementById('edit_usulan_diusulkan_unit').value = data.diusulkan_unit
        document.getElementById('edit_usulan_poktan').value = data.poktan
    }
    const closeEdit = () => {
        document.getElementById('edit_usulan').action = ''
        document.getElementById('edit_usulan_desa').value = ''
        document.getElementById('edit_usulan_luas_lahan').value = ''
        document.getElementById('edit_usulan_diusulkan_unit').value = ''
    }
    const deleteUsulan = (route) => {
        document.getElementById('delete_usulan').action = route
        document.getElementById('delete_usulan_modal').showModal()
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
    const search = (e) => {
        const {value} = e
        const table = document.getElementById('usulan_table')
        const rows = document.querySelectorAll('#usulan_table tbody tr')
        let num = 1
        rows.forEach(row => {
            const text = row.textContent.toLowerCase()
            row.style.display = text.includes(value.toLowerCase()) ? '' : 'none'
            row.children[0].textContent = text.includes(value.toLowerCase()) ? num : ''
            if (text.includes(value.toLowerCase())) num++
        });
        numbering()
    }
    const filterDesa = (e) => {
        const {value} = e
        const rows = document.querySelectorAll('#usulan_table tbody tr')
        rows.forEach(row => {
            const desaCellName = row.children[2].textContent
            row.style.display = desaCellName == value || value == 'semua' ? '' : 'none'
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
            const dateCellVal = new Date(new Date(row.children[1].textContent).toISOString().split('T')[0]).getTime()
            let condition = false
            if (start && !end && dateCellVal >= start) condition = true
            else if (!start && end && dateCellVal <= end) condition = true
            else if (start && end && dateCellVal >= start && dateCellVal <= end) condition = true
            else condition = false
            row.style.display = condition ? '' : 'none'
        });
        numbering()
    }
    const errValidation = (messages, containerId) => {
        const container = document.getElementById(containerId);
        let messageList = ''
        messages.forEach(msg => {messageList += `<li>${msg}</li>`})
        const alert = `<div role="alert" class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div><lu>${messageList}</lu></div></div>`
        container.innerHTML += alert
    }
    const confirmUpdateUsulan = () => {
        const luasLahan = document.getElementById('edit_usulan_luas_lahan').value
        const unit = document.getElementById('edit_usulan_diusulkan_unit').value
        let anyErrors = false
        let errors = []
        if (luasLahan == 0 || luasLahan == '' || luasLahan == null) {
            anyErrors = true
            errors.push('Luas lahan diusulkan tidak boleh kosong')
        }
        if (unit == 0 || unit == '' || unit == null) {
            anyErrors = true
            errors.push('Jumlah pompa diusulkan tidak boleh kosong')
        }
        if (anyErrors) {
            errValidation(errors, 'alert-container-edit')
            return
        }
        document.getElementById('confirm_update_usulan_modal').showModal()
    }


    numbering()
</script>
@endsection