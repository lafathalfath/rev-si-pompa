@extends('layouts.authenticated')
@section('title')| Tambah Pompa Usulan @endsection
@section('content')
<style>
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .step-point {
        width: 25px;
        height: 25px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ddd;
    }
    .step-point-active {
        color: white;
        background-color: #070;
    }
</style>

<div id="alert-container" class="flex flex-col gap-1 w-full"></div>

<div>
    <div class="text-xl font-bold mb-10">Tambah Data Pompa Diusulkan</div>
    {{-- <div class="w-1/2 h-2 bg-[#ddd] absolute left-1/4 top-1/6"><div class="{{ request()->poktan ? 'w-full' : 'w-1/2' }} h-2 bg-[#070]"></div></div> --}}
    <div class="relative flex flex-column justify-center">
        <div class="w-1/2 h-2 bg-[#ddd] absolute left-1/4 top-1/6"><div class="{{ request()->poktan ? 'w-full' : 'w-1/2' }} h-2 bg-[#070]"></div></div>
        <div class="w-full flex items-center justify-around z-10">
            <div class="step-item">
                <div class="step-point step-point-active">1</div>
                <div class="text-xs">Pilih Kelompok Tani</div>
            </div>
            <div class="step-item">
                <div class="step-point {{ request()->poktan ? 'step-point-active' : '' }}">2</div>
                <div class="text-xs">Isi Data Usulan</div>
            </div>
        </div>
    </div>
    <div>
        {{-- {{ dd($selected_poktan) }} --}}
        <div class="mt-2">
            <div class="w-full" id="select_poktan" style="{{ $selected_poktan ? 'display:none;' : '' }}">
                <div class="text-lg font-semibold" id="title_poktan">Pilih Kelompok Tani</div>
                <div class="flex flex-col py-1" id="search_poktan">
                    <label for="search_poktan_input" class="text-semibold">Cari Kelompok Tani</label>
                    <div class="flex items-center justify-between gap-2">
                        <input type="search" name="" value="{{ $selected_poktan ? $selected_poktan->name : '' }}" id="search_poktan_input" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="searchPoktan(this)">
                        <button type="button" class="btn btn-sm rounded-sm bg-[#070] hover:bg-[#060] text-white" id="add_poktan" onclick="addPoktan(this)">+ Tambah Kelompok Tani</button>
                    </div>
                    <table class="mt-2">
                        <thead><tr>
                            <th class="w-1">No</th>
                            <th>Nama</th>
                            <th>Desa</th>
                            <th class="w-1"></th>
                        </tr></thead>
                        <tbody id="list_poktan">
                            @foreach ($poktan as $pok)
                                <tr>
                                    <td class="w-1">{{ $loop->iteration }}</td>
                                    <td>{{ $pok->name }}</td>
                                    <td>{{ $pok->desa->name }}</td>
                                    <td class="w-1"><div class="tooltip" data-tip="Pilih">
                                        <a href="{{ route('kecamatan.usulan.create', ['poktan' => Crypt::encryptString($pok->id)]) }}" class="btn btn-sm bg-[#0a0] hover:bg-[#080] text-white rounded-sm">&#10140;</a>
                                    </div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm rounded-sm bg-red-700 hover:bg-red-800 text-white" id="cancel_add_poktan" style="display: none;"  onclick="cancelAddPoktan(this)">Batal</button>
            </div>
            <div id="create_poktan" style="display: none;">
                <form action="{{ route('kecamatan.poktan.store') }}" method="POST" id="create_poktan_form" enctype="multipart/form-data">
                    @csrf
                    <div class="mt-5">
                        <label>Data Diri</label>
                        <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                            <div class="flex flex-col py-1">
                                <label for="name" class="text-semibold">Nama Kelompok Tani<span class="text-red-600">*</span></label>
                                <input type="text" name="name" id="poktan_name" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            </div>
                            <div class="flex flex-col py-1">
                                <label for="phone_number" class="text-semibold">No. HP Kelompok Tani<span class="text-red-600">*</span></label>
                                <input type="tel" name="phone_number" id="poktan_phone_number" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            </div>
                            <div class="flex flex-col py-1">
                                <label for="ktp" class="text-semibold">KTP<span class="text-red-600">*</span></label>
                                <input type="file" accept="application/pdf" name="ktp" id="poktan_ktp" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <label for="">Alamat</label>
                        <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                            <div class="flex flex-col py-1">
                                <label class="text-semibold">Provinsi<span class="text-red-600">*</span></label>
                                <input type="text" id="poktan_provinsi" value="{{ $kecamatan->kabupaten->provinsi->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                            </div>
                            <div class="flex flex-col py-1">
                                <label class="text-semibold">Kabupaten<span class="text-red-600">*</span></label>
                                <input type="text" id="poktan_kabupaten" value="{{ $kecamatan->kabupaten->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                            </div>
                            <div class="flex flex-col py-1">
                                <label class="text-semibold">Kecamatan<span class="text-red-600">*</span></label>
                                <input type="text" id="poktan_kecamatan" value="{{ $kecamatan->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                            </div>
                            <div class="flex flex-col py-1">
                                <label for="poktan_desa_id" class="text-semibold">Desa<span class="text-red-600">*</span></label>
                                <select name="desa_id" id="poktan_desa_id" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                                    <option value="" disabled selected>-- pilih desa --</option>
                                    @foreach ($desa as $des)
                                        <option value="{{ $des->id }}">{{ $des->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col py-1">
                                <label for="address" class="text-semibold">Alamat Lengkap<span class="text-red-600">*</span></label>
                                {{-- <input type="text" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly> --}}
                                <textarea name="address" id="address" cols="30" rows="5" required class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" placeholder="RT/RW, Jalan, Nomor, ..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <label>Kepemilikan Lahan</label>
                        <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                            <div class="flex flex-col py-1">
                                <label for="poktan_luas_lahan" class="text-semibold">Luas Lahan (Ha)<span class="text-red-600">*</span></label>
                                <input type="number" step="0.0001" min="0" name="luas_lahan" id="poktan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            </div>
                            <div class="flex flex-col py-1">
                                <label for="bukti_kepemilikan" class="text-semibold">Bukti Kepemilikan<span class="text-red-600">*</span></label>
                                <input type="file" accept="application/pdf" multiple name="bukti_kepemilikan[]" id="bukti_kepemilikan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-red-600 text-sm">*) Wajib diisi</div>
                </form>
                <div class="flex justify-end">
                    <button type="submit" class="btn bg-[#070] hover:bg-[#060] text-white" onclick="confirmSendPoktan()">Buat</button>
                </div>
            </div>

            <div id="usulan_form" style="{{ $selected_poktan ? 'display:block;' : 'display:none;' }}">
                <div class="text-lg font-semibold">Isi Data Usulan</div>
                <form action="{{ route('kecamatan.usulan.store') }}" method="POST" id="add_usulan_form" class="w-full">
                    @csrf
                    <input type="number" name="poktan_id" id="poktan_id" value="{{ $selected_poktan ? $selected_poktan->id : '' }}" style="display: none;">
                    <div class="flex gap-2 flex-wrap w-full">
                        <div class="flex flex-col py-1">
                            <label class="text-semibold">Kelompok Tani<span class="text-red-600">*</span></label>
                            <div class="w-98 flex items-center gap-1">
                                <input type="text" value="{{ $selected_poktan ? $selected_poktan->name : '' }}" id="poktan_selected" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" readonly>
                                <button type="button" class="btn btn-sm text-black rounded-sm bg-[#0bf] hover:bg-[#0ae]" onclick="detailPoktan('{{ session('api_token') }}')">Detail</button>
                                {{-- <button type="button" class="btn btn-sm text-black rounded-sm bg-[#ffc800] hover:bg-[#eeb700]" onclick="changePoktan()">Ganti</button> --}}
                                <a href="{{ route('kecamatan.usulan.create') }}" class="btn btn-sm text-black rounded-sm bg-[#ffc800] hover:bg-[#eeb700]">Ganti</a>
                            </div>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="usulan_desa_id" class="text-semibold">Desa<span class="text-red-600">*</span></label>
                            <select name="desa_id" id="usulan_desa_id" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                                <option value="" disabled selected>-- pilih desa --</option>
                                @foreach ($desa as $des)
                                    <option value="{{ $des->id }}">{{ $des->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="usulan_luas_lahan" class="text-semibold">Luas Lahan (Ha)<span class="text-red-600">**</span></label>
                            <input type="number" step="0.0001" min="0" name="luas_lahan" id="usulan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="usulan_diusulkan_unit" class="text-semibold">Jumlah Pompa Diusulkan<span class="text-red-600">*</span></label>
                            <input type="number" min="1" name="diusulkan_unit" id="usulan_diusulkan_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                    </div>
                    <div class="text-red-600 text-sm mt-2">
                        *) Wajib diisi <br>
                        **) Wajjib diisi dan tidak boleh lebih dari luas lahan dimiliki kelompok tani
                    </div>
                </form>
                <div class="flex justify-end">
                    <button type="submit" onclick="confirmSendUsulan({{ $selected_poktan }})" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">Kirim</button>
                </div>
            </div>
        </div>
    </div>

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

    <dialog id="confirm_add_poktan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            Pastikan data yang Anda isi benar! <br>
            Apakah Anda yakin mengirim data ini?
            <div class="modal-action">
                <button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="create_poktan_form.submit()">Ya</button>
                <form method="dialog"><button class="btn">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <dialog id="confirm_add_usulan_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            Pastikan data yang Anda isi benar! <br>
            Apakah Anda yakin mengirim data ini?
            <div class="modal-action">
                <button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="add_usulan_form.submit()">Ya</button>
                <form method="dialog"><button class="btn">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

</div>

<script>
    // const searchPoktan = async (e, BASE_URL, token) => {
    //     const query = e.value
    //     const list = document.getElementById('list_poktan')
    //     const btnAdd = document.getElementById('add_poktan')
    //     list.style.display = query != '' ? 'flex' : 'none'
    //     try {
    //         const response = await fetch(BASE_URL + `/api/poktan?search=${query}`, {headers: {
    //             "Authorization": `Bearer ${token}`
    //         }})
    //         const data = await response.json()
    //         btnAdd.style.display = (!data || data.length == 0) && query ? 'block' : 'none'
    //         if (data) {
    //             let poktans = ''
    //             data.forEach(pok => {
    //                 poktans += `<button type="button" class="w-full hover:bg-gray-200 text-start p-2" onclick="selectPoktan('${pok.id}', '${pok.name}')">${pok.name} | Desa: ${pok.desa}</button>`
    //             });
    //             list.innerHTML = `
    //                 <button type="button" class="bg-gray-200 w-full p-2" disabled>-- pilih poktan --</button>${poktans}
    //             `
    //         }
    //     } catch (err) {
    //         console.error(err.message)
    //     }
    // }
    const searchPoktan = (e) => {
        const value = e.value.toLowerCase()
        const rows = document.getElementById('list_poktan').children
        for (let i = 0; i < rows.length; i++)
            rows[i].style.display = rows[i].textContent.toLowerCase().includes(value) ? '' : 'none'
    }
    const addPoktan = (e) => {
        e.style.display = 'none'
        document.getElementById('cancel_add_poktan').style.display = 'block'
        document.getElementById('create_poktan').style.display = 'block'
        document.getElementById('search_poktan').style.display = 'none'
        document.getElementById('poktan_name').value = document.getElementById('search_poktan_input').value
        document.getElementById('title_poktan').innerHTML = 'Tambah Kelompok Tani'
    }
    const cancelAddPoktan = (e) => {
        e.style.display = 'none'
        document.getElementById('search_poktan').style.display = 'flex'
        document.getElementById('create_poktan').style.display = 'none'
        document.getElementById('search_poktan_input').value = ''
        document.getElementById('add_poktan').style.display = ''
        document.getElementById('title_poktan').innerHTML = 'Pilih Kelompok Tani'
    }
    const selectPoktan = (id, name) => {
        document.getElementById('poktan_id').value = id
        document.getElementById('search_poktan_input').value = name
        document.getElementById('list_poktan').style.display = 'none'
        document.getElementById('select_poktan').style.display = 'none'
        document.getElementById('poktan_selected').value = name
        document.getElementById('usulan_form').style.display = 'block'
    }
    const changePoktan = () => {
        document.getElementById('usulan_form').style.display = 'none'
        document.getElementById('select_poktan').style.display = 'block'
    }
    const detailPoktan = async (token) => {
        document.getElementById('detail_poktan_modal').showModal()
        const poktanName = document.getElementById('poktan_selected').value
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
    const confirmSendPoktan = () => {
        const name = document.getElementById('poktan_name').value
        const phone = document.getElementById('poktan_phone_number').value
        const ktp = document.getElementById('poktan_ktp').files.length
        const desaId = document.getElementById('poktan_desa_id').value
        const address = document.getElementById('address').value
        const luasLahan = document.getElementById('poktan_luas_lahan').value
        const bukti = document.getElementById('bukti_kepemilikan').files.length
        let anyErrors = false
        let errors = []
        if (name == '' || name == null) {
            anyErrors = true
            errors.push('Nama Kelompok Tani tidak boleh kosong')
        }
        if (phone == '' || phone == null) {
            anyErrors = true
            errors.push('Nomor Telepon Kelompok Tani tidak boleh kosong')
        }
        if (ktp == 0 || ktp == null) {
            anyErrors = true
            errors.push('KTP Kelompok Tani tidak boleh kosong')
        }
        if (desaId == '' || desaId == null) {
            anyErrors = true
            errors.push('Desa tidak boleh kosong')
        }
        if (address == '' || address == null) {
            anyErrors = true
            errors.push('Alamat lengkap tidak boleh kosong')
        }
        if (luasLahan == 0 || luasLahan == '' || luasLahan == null) {
            anyErrors = true
            errors.push('Alamat lengkap tidak boleh kosong')
        }
        if (bukti == 0 || bukti == null) {
            anyErrors = true
            errors.push('Bukti kepemilikan lahan tidak boleh kosong')
        }
        if (anyErrors) {
            errValidation(errors)
            return
        }
        document.getElementById('confirm_add_poktan_modal').showModal()
    }
    const confirmSendUsulan = (poktan) => {
        const desaId = document.getElementById('usulan_desa_id').value
        const luasLahan = document.getElementById('usulan_luas_lahan').value
        const unit = document.getElementById('usulan_diusulkan_unit').value
        let anyErrors = false
        let errors = []
        if (poktan == null) {
            anyErrors = true
            errors.push('Kelompok Tani tidak ditemukan')
        }
        if (desaId == '' || desaId == null) {
            anyErrors = true
            errors.push('Desa tidak boleh kosong')
        }
        if (luasLahan == 0 || luasLahan == '' || luasLahan == null) {
            anyErrors = true
            errors.push('Luas lahan diusulkan tidak boleh kosong')
        } else if (luasLahan > poktan.luas_lahan) {
            anyErrors = true
            errors.push('Luas lahan diusulkan tidak boleh lebih dari luas lahan dimiliki kelompok tani')
        }
        if (unit == 0 || unit == '' || unit == null) {
            anyErrors = true
            errors.push('Jumlah pompa diusulkan tidak boleh kosong')
        }
        if (anyErrors) {
            errValidation(errors)
            return
        }
        document.getElementById('confirm_add_usulan_modal').showModal()
    }
    const errValidation = (messages) => {
        const container = document.getElementById('alert-container');
        let messageList = ''
        messages.forEach(msg => {messageList += `<li>${msg}</li>`})
        const alert = `<div role="alert" class="alert alert-error"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><div><lu>${messageList}</lu></div></div>`
        container.innerHTML += alert
    }
</script>
@endsection