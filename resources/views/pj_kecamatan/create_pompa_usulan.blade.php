@extends('layouts.authenticated')
@section('content')
<style>
</style>    

<div>
    <div class="text-xl font-bold mb-10">Tambah Data Pompa Diusulkan</div>
    <div>
        <div class="mt-2">
            <div class="w-full" id="select_poktan" style="{{ $poktan ? 'display:none;' : '' }}">
                <div class="text-lg font-semibold">Data Kelompok Tani</div>
                <div class="flex flex-col py-1" id="search_poktan">
                    <label for="search_poktan_input" class="text-semibold">Cari Kelompok Tani</label>
                    <div class="flex items-center gap-2">
                        <input type="search" name="" value="{{ $poktan ? $poktan->name : '' }}" id="search_poktan_input" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="searchPoktan(this, '{{ env('BASE_URL') }}', '{{ $api_token }}')">
                        <button type="button" class="btn btn-sm rounded-sm bg-[#070] hover:bg-[#060] text-white" id="add_poktan" onclick="addPoktan(this)" style="display: none;">+ Tambah Kelompok Tani</button>
                    </div>
                    <div id="list_poktan" class="border-1 border-gray-400 rounded-sm h-50 max-w-full overflow-y-scroll" style="display: none;flex-direction:column;">
                        <button type="button" class="bg-gray-200 w-full p-2" disabled>-- pilih poktan --</button>
                    </div>
                </div>
                <button type="button" class="btn btn-sm rounded-sm bg-red-700 hover:bg-red-800 text-white" id="cancel_add_poktan" style="display: none;"  onclick="cancelAddPoktan(this)">Batal</button>
            </div>
            <form action="{{ route('kecamatan.poktan.store') }}" method="POST" id="create_poktan_form" style="display: none;" enctype="multipart/form-data">
                @csrf
                <div class="mt-5">
                    <label>Data Diri</label>
                    <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                        <div class="flex flex-col py-1">
                            <label for="name" class="text-semibold">Nama Kelompok Tani</label>
                            <input type="text" name="name" id="name" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="phone_number" class="text-semibold">No. HP Kelompok Tani</label>
                            <input type="tel" name="phone_number" id="phone_number" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="ktp" class="text-semibold">KTP</label>
                            <input type="file" accept="application/pdf" name="ktp" id="ktp" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label for="">Alamat</label>
                    <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                        <div class="flex flex-col py-1">
                            <label class="text-semibold">Provinsi</label>
                            <input type="text" value="{{ $kecamatan->kabupaten->provinsi->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                        </div>
                        <div class="flex flex-col py-1">
                            <label class="text-semibold">Kabupaten</label>
                            <input type="text" value="{{ $kecamatan->kabupaten->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                        </div>
                        <div class="flex flex-col py-1">
                            <label class="text-semibold">Kecamatan</label>
                            <input type="text" value="{{ $kecamatan->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="poktan_desa_id" class="text-semibold">Desa</label>
                            <select name="desa_id" id="poktan_desa_id" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                                <option value="" disabled selected>-- pilih desa --</option>
                                @foreach ($desa as $des)
                                    <option value="{{ $des->id }}">{{ $des->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="address" class="text-semibold">Alamat Lengkap</label>
                            {{-- <input type="text" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" disabled readonly> --}}
                            <textarea name="address" id="address" cols="30" rows="5" required class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" placeholder="RT/RW, Jalan, Nomor, ..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label>Kepemilikan Lahan</label>
                    <div class="flex items-start justify-start gap-2 flex-wrap w-full">
                        <div class="flex flex-col py-1">
                            <label for="poktan_luas_lahan" class="text-semibold">Luas Lahan (Ha)</label>
                            <input type="number" step="0.0001" min="0" name="luas_lahan" id="poktan_luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                        <div class="flex flex-col py-1">
                            <label for="bukti_kepemilikan" class="text-semibold">Bukti Kepemilikan</label>
                            <input type="file" accept="application/pdf" multiple name="bukti_kepemilikan[]" id="bukti_kepemilikan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn bg-[#070] hover:bg-[#060] text-white">Buat</button>
                </div>
            </form>
            <form action="{{ route('kecamatan.usulan.store') }}" method="POST" id="usulan_form" class="w-full" style="{{ $poktan ? 'display:block;' : 'display:none;' }}">
                @csrf
                <input type="number" name="poktan_id" id="poktan_id" value="{{ $poktan ? $poktan->id : '' }}" style="display: none;">
                <div class="flex gap-2 flex-wrap w-full">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Kelompok Tani</label>
                        <div class="w-98 flex items-center gap-1">
                            <input type="text" value="{{ $poktan ? $poktan->name : '' }}" id="poktan_selected" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" readonly>
                            <button type="button" class="btn btn-sm text-black rounded-sm bg-[#0bf] hover:bg-[#0ae]" onclick="detailPoktan('{{ $api_token }}')">Detail</button>
                            <button type="button" class="btn btn-sm text-black rounded-sm bg-[#ffc800] hover:bg-[#eeb700]" onclick="changePoktan()">Ganti</button>
                        </div>
                    </div>
                    <div class="flex flex-col py-1">
                        <label for="desa_id" class="text-semibold">Desa</label>
                        <select name="desa_id" id="desa_id" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                            <option disabled selected>-- pilih desa --</option>
                            @foreach ($desa as $des)
                                <option value="{{ $des->id }}">{{ $des->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col py-1">
                        <label for="luas_lahan" class="text-semibold">Luas Lahan (Ha)</label>
                        <input type="number" step="0.0001" min="0" name="luas_lahan" id="luas_lahan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                    </div>
                    <div class="flex flex-col py-1">
                        <label for="total_unit" class="text-semibold">Jumlah Pompa Diusulkan</label>
                        <input type="number" min="1" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">Kirim</button>
                </div>
            </form>
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

</div>

<script>
    const searchPoktan = async (e, BASE_URL, token) => {
        const query = e.value
        const list = document.getElementById('list_poktan')
        const btnAdd = document.getElementById('add_poktan')
        list.style.display = query != '' ? 'flex' : 'none'
        try {
            const response = await fetch(BASE_URL + `/api/poktan?search=${query}`, {headers: {
                "Authorization": `Bearer ${token}`
            }})
            const data = await response.json()
            btnAdd.style.display = (!data || data.length == 0) && query ? 'block' : 'none'
            if (data) {
                let poktans = ''
                data.forEach(pok => {
                    poktans += `<button type="button" class="w-full hover:bg-gray-200 text-start p-2" onclick="selectPoktan('${pok.id}', '${pok.name}')">${pok.name} | Desa: ${pok.desa}</button>`
                });
                list.innerHTML = `
                    <button type="button" class="bg-gray-200 w-full p-2" disabled>-- pilih poktan --</button>${poktans}
                `
            }
        } catch (err) {
            console.error(err.message)
        }
    }
    const addPoktan = (e) => {
        e.style.display = 'none'
        document.getElementById('cancel_add_poktan').style.display = 'block'
        document.getElementById('create_poktan_form').style.display = 'block'
        document.getElementById('search_poktan').style.display = 'none'
    }
    const cancelAddPoktan = (e) => {
        e.style.display = 'none'
        document.getElementById('search_poktan').style.display = 'block'
        document.getElementById('create_poktan_form').style.display = 'none'
        document.getElementById('search_poktan_input').value = ''
        document.getElementById('list_poktan').style.display = 'none'
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
</script>
@endsection