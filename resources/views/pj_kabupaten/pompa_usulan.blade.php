@extends('layouts.authenticated')
@section('title')| Pompa Usulan @endsection
@section('content')
    
<div>
    <div class="text-xl font-bold">Data Pompa Diusulkan</div>
    <div class="mt-5">
        <div class="flex flex-col py-1">
            <label for="search" class="text-semibold">Cari Data Pompa Diusulkan</label>
            <input type="search" id="search" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="handleSearchUsulan(this)">
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        <div class="">
            <label class="text-semibold">Tanggal: </label>
            <input type="date" id="filter_date_start" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
            s/d
            <input type="date" id="filter_date_end" class="py-1 rounded-sm border-1 border-gray-400" oninput="filterDate()">
        </div>
        <div>
            <label for="kecamatan">Kecamatan:</label>
            <select name="" id="kecamatan" class="py-1 px-2 rounded-sm border-1 border-gray-400" 
                oninput="handleFilterKecamatan(this)"
            >
                <option value="semua" selected>semua</option>
                @foreach ($kecamatan as $kec)
                    <option value="{{ $kec['name'] }}">{{ $kec['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div id="filter_desa" style="display: none;">
            <label for="desa">Desa:</label>
            <select name="" id="desa" class="py-1 px-2 rounded-sm border-1 border-gray-400" 
                oninput="handleFilterDesa(this)"
            >
                <option value="semua" selected>semua</option>
            </select>
        </div>
        <div>
            <a href="{{ route('kabupaten.usulan') }}" class="btn btn-sm rounded-sm text-white bg-gray-500 hover:bg-gray-600">Bersihkan Filter</a>
        </div>
    </div>
    <table class="w-full" id="usulan_table">
        <thead>
            <tr>
                <th>No</th>
                <th>Terakhir<br>Diubah Oleh</th>
                <th>Tanggal</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Kelompok Tani</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Unit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pompa as $pom)
                <tr>
                    {{-- <td>{{ $loop->iteration }}</td> --}}
                    <td id="number_row"></td>
                    <td>{{ $pom->updated_by ? str_replace('_', ' ', $pom->update_by->name.' ('.$pom->update_by->role->name.')') : '-' }}</td>
                    <td>{{ $pom->created_at }}</td>
                    <td id="kecamatan_entry">{{ $pom->desa->kecamatan->name }}</td>
                    <td id="desa_entry">{{ $pom->desa->name }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $pom->poktan->name }}</div>
                        <button type="button"
                            onclick="detailPoktan('{{ $pom->poktan->name }}', '{{ $api_token }}')"
                        class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm">Detail</button>
                    </td>
                    <td>{{ $pom->luas_lahan }}</td>
                    <td>{{ $pom->diusulkan_unit }}</td>
                    <td>
                        @if ($pom->status_id == 1)
                            <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                            onclick="editUsulan('{{ route('kabupaten.usulan.update', Crypt::encryptString($pom->id)) }}', {{ $pom }}, '{{ $pom->desa->kecamatan->name }}')"
                            >Ubah</button>
                            @if ($pom->status != 'ditolak')
                                <button class="btn btn-sm bg-[#0a0] hover:bg-[#080] text-white rounded-sm" 
                                onclick="approveUsulan('{{ route('kabupaten.usulan.approve', Crypt::encryptString($pom->id)) }}', {{ $pom->diusulkan_unit }})"
                                >Setujui</button>
                                <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm"
                                onclick="denyUsulan('{{ route('kabupaten.usulan.deny', Crypt::encryptString($pom->id)) }}')"
                                >Tolak</button>
                            @endif
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
                    <label for="edit_kecamatan" class="text-semibold">Kecamatan</label>
                    <input type="text" name="edit_kecamatan" id="edit_usulan_kecamatan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_desa" class="text-semibold">Desa</label>
                    <input type="text" name="edit_desa" id="edit_usulan_desa" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_usulan_luas_lahan" class="text-semibold">Luas Lahan (Ha) <span class="text-red-600">**</span></label>
                    <input type="number" step="0.0001" min="0.0001" name="luas_lahan" id="edit_usulan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_usulan_diusulkan_unit" class="text-semibold">Jumlah Pompa Diusulkan <span class="text-red-600">*</span></label>
                    <input type="number" min="1" name="diusulkan_unit" id="edit_usulan_diusulkan_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
                <div class="text-red-600 text-xs mt-2">
                    *) Tidak boleh kosong <br>
                    **) Tidak boleh kosong dan tidak boleh lebih dari luas lahan dimiliki kelompok tani
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="confirmUpdate()">Perbarui</button><form method="dialog"><button class="btn" onclick="closeEdit()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <dialog id="approve_usulan_modal" class="modal">
        <div class="modal-box">
            <div id="alert-container-approve" class="flex flex-col gap-1 w-full"></div>
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            <form action="" method="POST" id="approve_usulan" class="py-4">
                @csrf
                @method('PUT')
                Apakah Anda yakin ingin memverifikasi data pompa diusulkan ini?
                <div>
                    <label for="diterima_unit">Masukkan jumlah pompa diterima: <span class="text-red-600">*</span></label>
                    <input type="number" min="1" name="diterima_unit" id="diterima_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                    <div class="text-red-600 text-xs">*) Tidak boleh kosong dan tidak boleh lebih dari jumlah diusulkan</div>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="confirmApprove()">Kirim</button><form method="dialog"><button class="btn" onclick="diterima_unit.value=''">Batal</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    <dialog id="deny_usulan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            <form action="" method="POST" id="deny_usulan" class="py-4">
                @csrf
                @method('PUT')
                Apakah Anda yakin ingin menolak verifikasi data Pompa Diusulkan ini?
            </form>
            <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="deny_usulan.submit()">Tolak</button><form method="dialog"><button class="btn">Batal</button></form></div>
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

    <dialog id="confirm_update_modal" class="modal">
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
    const handleSearchUsulan = (e) => {
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
    const editUsulan = (route, data, kecamatan) => {
        document.getElementById('edit_usulan_modal').showModal()
        document.getElementById('edit_usulan').action = route
        document.getElementById('edit_usulan_kecamatan').value = kecamatan
        document.getElementById('edit_usulan_desa').value = data.desa.name
        document.getElementById('edit_usulan_luas_lahan').max = data.poktan.luas_lahan
        document.getElementById('edit_usulan_luas_lahan').value = data.luas_lahan
        document.getElementById('edit_usulan_diusulkan_unit').value = data.diusulkan_unit
        document.getElementById('edit_usulan_poktan').value = data.poktan.name
    }
    const closeEdit = () => {
        document.getElementById('edit_usulan').action = ''
        document.getElementById('edit_usulan_desa').value = ''
        document.getElementById('edit_usulan_luas_lahan').value = ''
        document.getElementById('edit_usulan_diusulkan_unit').value = ''
    }
    const approveUsulan = (route, usulan) => {
        document.getElementById('approve_usulan').action = route
        document.getElementById('approve_usulan_modal').showModal()
        document.getElementById('diterima_unit').max = usulan
    }
    const denyUsulan = (route) => {
        document.getElementById('deny_usulan').action = route
        document.getElementById('deny_usulan_modal').showModal()
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
    const handleFilterKecamatan = async (e) => {
        const {value} = e
        if (!value || value == '') return
        const rows = document.querySelectorAll('#usulan_table tbody tr')
        const kecamatans = document.querySelectorAll('#kecamatan_entry')
        const filterDesa = document.getElementById('filter_desa')
        rows.forEach(row => {
            const kecamatanCell = row.children[2]
            row.style.display = kecamatanCell.textContent.includes(value) || value == 'semua' ? '' : 'none'
        });
        if (value != 'semua') try {
            const response = await fetch(`/api/desa/${value}`)
            const data = await response.json()
            filterDesa.style.display = data ? '' : 'none'
            if (data) {
                const desaSelect = document.querySelector('#filter_desa select')
                desaSelect.innerHTML = '<option value="semua" selected>semua</option>'
                data.forEach(desa => {
                    const opt = document.createElement('option')
                    opt.value = desa.name
                    opt.textContent = desa.name
                    desaSelect.appendChild(opt)
                });
                numbering()
            }
        } catch (err) {
            console.error(err.message)
        }
        else filterDesa.style.display = 'none'
    }
    const handleFilterDesa = (e) => {
        const {value} = e
        if (!value || value == '') return
        const rows = document.querySelectorAll('#usulan_table tbody tr')
        rows.forEach(row => {
            const desaName = row.children[3].textContent
            row.style.display = desaName.includes(value) ? '' : 'none'
        })
        numbering()
    }
    const detailPoktan = async (name, token) => {
        document.getElementById('detail_poktan_modal').showModal()
        if (!name) return
        try {
            const response = await fetch(`/api/poktan/${name}`, {headers: {"Authorization": `Bearer ${token}`}})
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
    const errValidation = (messages, containerId) => {
        const container = document.getElementById(containerId);
        let messageList = ''
        messages.forEach(msg => {messageList += `<li>${msg}</li>`})
        const alert = `<div role="alert" class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div><lu>${messageList}</lu></div></div>`
        container.innerHTML += alert
    }
    const confirmUpdate = () => {
        const luasLahan = document.getElementById('edit_usulan_luas_lahan')
        const unit = document.getElementById('edit_usulan_diusulkan_unit').value
        let anyErrors = false
        let errors = []
        if (unit == 0 || unit == '' || unit == null) {
            anyErrors = true
            errors.push('Jumlah pemanfaatan pompa tidak boleh kosong')
        }
        if (luasLahan.value == 0 || luasLahan.value == '' || luasLahan.value == null) {
            anyErrors = true
            errors.push('Luas lahan diusulkan tidak boleh kosong')
        } else if (luasLahan.value > luasLahan.max) {
            anyErrors = true
            errors.push('Luas lahan diusulkan tidak boleh lebih dari luash lahan dimiliki kelompok tani')
        }
        if (anyErrors) {
            errValidation(errors, 'alert-container-edit')
            return
        }
        document.getElementById('confirm_update_modal').showModal()
    }
    const confirmApprove = () => {
        const unit = document.getElementById('diterima_unit')
        let anyErrors = false
        let errors = []
        if (unit.value == 0 || unit.value == '' || unit.value == null) {
            anyErrors = true
            errors.push('Jumlah pompa diterima tidak boleh kosong')
        } else if (unit.value > unit.max) {
            anyErrors = true
            errors.push('Jumlah pompa diterima tidak boleh lebih dari pompa usulan')
        }
        if (anyErrors) {
            errValidation(errors, 'alert-container-approve')
            return
        }
        document.getElementById('approve_usulan').submit()
    }
    
    numbering()
</script>

@endsection