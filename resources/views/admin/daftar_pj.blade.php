@extends('layouts.authenticated')
@section('title')| Daftar Penanggung Jawab @endsection
@section('content')
<style>
    #region_container > button, #edit_region_container > button {
        padding: 1px 3px;
        width: 100%;
        text-align: start;
    }
    #region_container > button:hover, #edit_region_container > button:hover {
        background-color: #eee;
    }
</style>
    
<div class="w-full">
    <div class="text-xl font-bold">Daftar Penanggung Jawab</div>
    <div class="flex justify-end">
        <button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="create_user_modal.showModal()">+ Tambah Penanggung Jawab Baru</button>
    </div>
    <table class="w-full mt-5">
        <thead class="w-full">
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Username</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Role</th>
                <th>Wilayah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="w-full">
            @forelse ($users as $usr)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $usr->nip }}</td>
                    <td>{{ $usr->name }}</td>
                    <td>{{ $usr->email ?? '-' }}</td>
                    <td>{{ $usr->phone_number ?? '-' }}</td>
                    <td class="capitalize">{{ str_replace('_', ' ', $usr->role->name) }}</td>
                    <td>{{ $usr->region ? $usr->region->name : '-' }}</td>
                    <td>
                        @if (!$usr->email || !$usr->phone_number || !$usr->password_changed || $usr->nip == $usr->name)
                            <div class="badge badge-warning text-black rounded-sm bg-[#ffc800] border-none">Belum Aktif</div>
                        @else
                            <div class="badge badge-success text-white rounded-sm bg-[#090] border-none">Aktif</div>
                        @endif
                    </td>
                    <td>
                        <button type="button" onclick="editUserModal('{{ route('admin.edit_pj', Crypt::encryptString($usr->id)) }}', {{ $usr }})" class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black">Edit</button>
                        <button type="button" onclick="deleteUser('{{ route('admin.hapus_pj', Crypt::encryptString($usr->id)) }}')" class="btn btn-sm bg-red-600 hover:bg-red-700 text-white">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Data Kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <dialog id="create_user_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Buat Akun Penanggung Jawab</h3>
            <form action="{{ route('admin.tambah_pj') }}" method="POST" id="create_user" class="py-4">
                @csrf
                <div class="flex flex-col py-1">
                    <label for="nip" class="text-semibold">NIP</label>
                    <input type="number" name="nip" id="nip" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required oninput="followNip(this)" minlength="16" maxlength="20">
                </div>
                <div class="flex flex-col py-1">
                    <label for="name" class="text-semibold">Username</label>
                    <input type="text" name="name" id="name" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
                </div>
                <div class="flex flex-col py-1">
                    <label for="role" class="text-semibold">Role</label>
                    <select name="role_id" id="role" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required onchange="showSearchRegion(this)">
                        <option value="" disabled selected>-- pilih role --</option>
                        @foreach ($role_list as $rl)
                            <option value="{{ $rl->id }}" class="capitalize">{{ str_replace('_', ' ', $rl->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col py-1" id="search_region" style="display: none" onblur="hideRegions()">
                    <label for="role" class="text-semibold" id="region_label"></label>
                    <input type="number" id="region_input_id" name="region_id" style="display: none;" disabled>
                    <input type="search" id="search_region_name" oninput="searchRegion('{{ env('BASE_URL') }}', this)" onfocus="searchRegion('{{ env('BASE_URL') }}', '')" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400">
                    <div class="hidden bg-white h-20 overflow-y-scroll rounded-sm border-1 border-[#ddd]" id="region_container">
                        <button type="button" disabled>-- pilih wilayah --</button><br>
                    </div>
                </div>
                <div class="text-red-600 text-sm">Password awal akun penanggung jawab adalah sama dengan NIP. <br>Penanggung jawab harus merubah password untuk mengaktifkan akun.</div>
            </form>
            <div class="modal-action"><button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="createUser()">Buat</button><form method="dialog"><button class="btn" onclick="clearInput()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button onclick="clearInput()">close</button></form>
    </dialog>

    <dialog id="edit_user_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Buat Akun Penanggung Jawab</h3>
            <form action="" method="POST" id="edit_user" class="py-4">
                @csrf
                @method('PUT')
                <div class="flex flex-col py-1">
                    <label class="text-semibold">NIP</label>
                    <input type="text" id="edit_nip" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400 bg-gray-200" disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Username</label>
                    <input type="text" id="edit_name" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400 bg-gray-200" disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">Email</label>
                    <input type="text" id="edit_email" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400 bg-gray-200" disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label class="text-semibold">No. HP</label>
                    <input type="text" id="edit_phone_number" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400 bg-gray-200" disabled>
                </div>
                <div class="flex flex-col py-1">
                    <label for="edit_role" class="text-semibold">Role</label>
                    <select name="role_id" id="edit_role" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required onchange="showEditSearchRegion(this)">
                        <option value="" disabled selected>-- pilih role --</option>
                        @foreach ($role_list as $rl)
                            <option value="{{ $rl->id }}" class="capitalize">{{ str_replace('_', ' ', $rl->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col py-1" id="edit_search_region" style="display: none" onblur="editHideRegions()">
                    <label for="role" class="text-semibold" id="edit_region_label"></label>
                    <input type="number" id="edit_region_input_id" name="region_id" style="display: none;" disabled>
                    <input type="search" id="edit_search_region_name" oninput="editSearchRegion('{{ env('BASE_URL') }}', this)" onfocus="editSearchRegion('{{ env('BASE_URL') }}', '')" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400">
                    <div class="hidden bg-white h-20 overflow-y-scroll rounded-sm border-1 border-[#ddd]" id="edit_region_container">
                        <button type="button" disabled>-- pilih wilayah --</button><br>
                    </div>
                </div>
            </form>
            <div class="modal-action"><button class="btn bg-[#070] hover:bg-[#060] text-white" onclick="editUser()">Simpan</button><form method="dialog"><button class="btn" onclick="clearEditInput()">Tutup</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button onclick="clearEditInput()">close</button></form>
    </dialog>

    <dialog id="delete_user_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Konfirmasi</h3>
            <form action="" method="POST" id="delete_user" class="py-4">
                @csrf
                @method('DELETE')
                Apakah Anda yakin ingin menghapus Penanggung Jawab ini?
            </form>
            <div class="modal-action"><button class="btn bg-red-600 hover:bg-red-700 text-white" onclick="delete_user.submit()">Hapus</button><form method="dialog"><button class="btn">Batal</button></form></div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
</div>

<script>
    const followNip = (e) => {
        const usernameInput = document.getElementById('name')
        // if (usernameInput.value != '') return
        usernameInput.value = e.value
    }
    const createUser = () => {
        const form = document.getElementById('create_user')
        form.submit()
    }
    const showSearchRegion = (e) => {
        const input = document.getElementById('search_region')
        const role_id = e.value
        if (role_id == 1 || role_id == 2) input.style.display = 'none'
        else {
            const label = document.getElementById('region_label')
            const regionInput = document.getElementById('region_input_id')
            input.style.display = 'block'
            label.innerHTML = e.children[role_id].innerHTML.charAt(3).toUpperCase() + e.children[role_id].innerHTML.substring(4)
            regionInput.disabled = false
            regionInput.required = true
        }
    }
    const searchRegion = async (host, e) => {
        const roleId = document.getElementById('role')
        if (roleId.value == 1 || roleId.value == 2) return
        const regionLevel = roleId.children[roleId.value].innerHTML.substring(3).toLowerCase()
        const container = document.getElementById('region_container')
        container.style.display = 'block'
        try {
            const response = await fetch(`${host}/api/${regionLevel}?search=${e.value}`)
            if (!response.ok) throw new Error('terjadi kesalahan')
            const data = await response.json()
            if (data) {
                container.innerHTML = '<button type="button" disabled>-- pilih wilayah --</button><br>'
                // const doc = document.createElement('div')
                data.forEach((reg) => {
                    const button = document.createElement('button')
                    const br = document.createElement('br')
                    button.type = 'button'
                    button.innerHTML = `${reg.name}${reg.kabupaten ? ' | '+reg.kabupaten : ''}${reg.provinsi ? ' | '+reg.provinsi : ''}`
                    button.addEventListener('click', () => setRegion(reg))
                    container.appendChild(button)
                    container.appendChild(br)
                })
                
            }
        } catch (err) {
            console.error(err.message)
        }
    }
    const setRegion = (data) => {
        const input = document.getElementById('search_region_name')
        const inputId = document.getElementById('region_input_id')
        input.value = data.name
        inputId.value = data.id
        hideRegions()
    }
    const hideRegions = () => {
        const container = document.getElementById('region_container')
        container.style.display = 'none'
    }
    const clearInput = () => {
        const name = document.getElementById('name')
        const nip = document.getElementById('nip')
        const region = document.getElementById('region_input_id')
        const role = document.getElementById('role')
        const search = document.getElementById('search_region_name')
        const searchRegion = document.getElementById('search_region')
        name.value = ''
        nip.value = ''
        region.value = ''
        role.value = ''
        search.value = ''
        searchRegion.style.display = 'none'
    }
    const editUserModal = (route, data) => {
        const modal = document.getElementById('edit_user_modal')
        const form = document.getElementById('edit_user')
        const nip = document.getElementById('edit_nip')
        const name = document.getElementById('edit_name')
        const email = document.getElementById('edit_email')
        const phone_number = document.getElementById('edit_phone_number')
        const role = document.getElementById('edit_role')
        const edit_region_input_id = document.getElementById('edit_region_input_id')
        const edit_search_region_name = document.getElementById('edit_search_region_name')
        modal.showModal()
        form.action = route
        nip.value = data.nip
        name.value = data.name
        email.value = data.email
        phone_number.value = data.phone_number
        role.value = data.role.id
        showEditSearchRegion(role)
        edit_search_region_name.value = data.region.name
        edit_region_input_id.value = data.region.id
    }
    const editUser = () => {
        document.getElementById('edit_user').submit()
    }
    const showEditSearchRegion = (e) => {
        const input = document.getElementById('edit_search_region')
        const role_id = e.value
        if (role_id == 1 || role_id == 2) input.style.display = 'none'
        else {
            const label = document.getElementById('edit_region_label')
            const regionInput = document.getElementById('edit_region_input_id')
            input.style.display = 'block'
            label.innerHTML = e.children[role_id].innerHTML.charAt(3).toUpperCase() + e.children[role_id].innerHTML.substring(4)
            regionInput.disabled = false
            regionInput.required = true
        }
    }
    const editSearchRegion = async (host, e) => {
        const roleId = document.getElementById('edit_role')
        if (roleId.value == 1 || roleId.value == 2) return
        const regionLevel = roleId.children[roleId.value].innerHTML.substring(3).toLowerCase()
        const container = document.getElementById('edit_region_container')
        container.style.display = 'block'
        try {
            const response = await fetch(`${host}/api/${regionLevel}?search=${e.value}`)
            if (!response.ok) throw new Error('terjadi kesalahan')
            const data = await response.json()
            if (data) {
                container.innerHTML = '<button type="button" disabled>-- pilih wilayah --</button><br>'
                // const doc = document.createElement('div')
                data.forEach((reg) => {
                    const button = document.createElement('button')
                    const br = document.createElement('br')
                    button.type = 'button'
                    button.innerHTML = `${reg.name}${reg.kabupaten ? ' | '+reg.kabupaten : ''}${reg.provinsi ? ' | '+reg.provinsi : ''}`
                    button.addEventListener('click', () => editSetRegion(reg))
                    container.appendChild(button)
                    container.appendChild(br)
                })
                
            }
        } catch (err) {
            console.error(err.message)
        }
    }
    const editSetRegion = (data) => {
        const input = document.getElementById('edit_search_region_name')
        const inputId = document.getElementById('edit_region_input_id')
        input.value = data.name
        inputId.value = data.id
        editHideRegions()
    }
    const editHideRegions = () => {
        const container = document.getElementById('edit_region_container')
        container.style.display = 'none'
    }
    const clearEditInput = () => {
        const name = document.getElementById('edit_name')
        const nip = document.getElementById('edit_nip')
        const email = document.getElementById('edit_email')
        const phone_number = document.getElementById('edit_phone_number')
        const region = document.getElementById('edit_region_input_id')
        const role = document.getElementById('edit_role')
        const search = document.getElementById('edit_search_region_name')
        const searchRegion = document.getElementById('edit_search_region')
        name.value = ''
        nip.value = ''
        email.value = ''
        phone_number.value = ''
        region.value = ''
        role.value = ''
        search.value = ''
        searchRegion.style.display = 'none'
    }
    const deleteUser = (route) => {
        document.getElementById('delete_user_modal').showModal()
        document.getElementById('delete_user').action = route
    }
</script>
@endsection