@extends('layouts.authenticated')
@section('content')

<div>
    <div class="text-xl font-bold">Data Pompa Diusulkan</div>
    {{-- <div class="flex justify-end mb-5"><button class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]" onclick="create_usulan_modal.showModal()">+ Tambah Data</button></div> --}}
    <div class="flex justify-end mb-5"><a href="{{ route('kecamatan.usulan.create') }}" class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]">+ Tambah Data</a></div>
    <table class="w-full">
        <thead>
            <tr>
                <th>No</th>
                <th>Desa</th>
                <th>Kelompok Tani</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Unit</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- {{ dd($api_token) }} --}}
            @forelse ($usulan as $usul)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $usul->desa }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $usul->poktan }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" onclick="detailPoktan('{{ $api_token }}', '{{ $usul->poktan }}')">Detail</button>
                    </td>
                    <td>{{ $usul->luas_lahan }}</td>
                    <td>{{ $usul->total_unit }}</td>
                    <td>
                        @if ($usul->status == 'diverifikasi')
                            <div class="badge bg-[#090] text-white font-semibold rounded-sm">Terverifikasi</div>
                        @elseif($usul->status == 'ditolak')
                            <div class="badge text-white bg-red-600 font-semibold rounded-sm">Ditolak</div>
                        @else
                            <div class="badge text-black bg-[#ffc800] font-semibold rounded-sm">Belum Diverifikasi</div>
                        @endif
                    </td>
                    <td>
                        @if ($usul->status != 'diverifikasi')
                            <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                                onclick="editUsulan('{{ route('kecamatan.usulan.update', Crypt::encryptString($usul->id)) }}', {desa_id: {{ $usul->desa_id }}, luas_lahan: {{ $usul->luas_lahan }}, total_unit: {{ $usul->total_unit }}, poktan: '{{ $usul->poktan }}' })"
                            >Edit</button>
                            <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" onclick="deleteUsulan('{{ route('kecamatan.usulan.destroy', Crypt::encryptString($usul->id)) }}')">Hapus</button>
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
            <h3 class="text-lg font-bold">Edit </h3>
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
                    <label for="edit_usulan_total_unit" class="text-semibold">Jumlah Pompa Diusulkan</label>
                    <input type="number" min="1" name="total_unit" id="edit_usulan_total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" onclick="edit_usulan.submit()">Perbarui</button><form method="dialog"><button class="btn" onclick="closeEdit()">Tutup</button></form></div>
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

</div>

<script>
    const editUsulan = (route, data) => {
        document.getElementById('edit_usulan_modal').showModal()
        document.getElementById('edit_usulan').action = route
        document.getElementById('edit_usulan_desa').value = data.desa_id
        document.getElementById('edit_usulan_luas_lahan').value = data.luas_lahan
        document.getElementById('edit_usulan_total_unit').value = data.total_unit
        document.getElementById('edit_usulan_poktan').value = data.poktan
    }
    const closeEdit = () => {
        document.getElementById('edit_usulan').action = ''
        document.getElementById('edit_usulan_desa').value = ''
        document.getElementById('edit_usulan_luas_lahan').value = ''
        document.getElementById('edit_usulan_total_unit').value = ''
    }
    const deleteUsulan = (route) => {
        document.getElementById('delete_usulan').action = route
        document.getElementById('delete_usulan_modal').showModal()
    }
    const detailPoktan = async (token, poktanName) => {
        document.getElementById('detail_poktan_modal').showModal()
        // const poktanName = document.getElementById('poktan_selected').value
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