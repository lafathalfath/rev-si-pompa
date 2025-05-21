@extends('layouts.authenticated')
@section('title')| Tambah Pompa Dimanfaatkan @endsection
@section('content')

<div>
    <div class="text-xl font-bold">Tambah Data Pompa Dimanfaatkan</div>
    
    @if (!$selected_diterima)
        <div class="mt-5 mb-2">
            <a href="{{ route('kecamatan.dimanfaatkan') }}" class="btn btn-sm rounded-sm text-white bg-red-600 hover:bg-red-700">Kembali</a>
        </div>
        <div class="mt-2">
            <div class="flex flex-col py-1">
                <label for="diterima" class="text-semibold">Cari Data Pompa Diterima</label>
                <input type="search" name="diterima" id="diterima" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="handleSearchDiterima(this)">
            </div>
        </div>

        <div class="w-full">
            <table class="w-full" id="diterima_table" style="display: none">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Desa</th>
                        <th>Kelompok Tani</th>
                        <th>Luas Lahan (Ha)</th>
                        <th>Total Unit Diusulkan</th>
                        <th>Total Unit Diterima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($diterima as $dt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dt->pompa_usulan->desa->name }}</td>
                            <td>{{ $dt->pompa_usulan->poktan->name }}</td>
                            <td>{{ $dt->pompa_usulan->luas_lahan }}</td>
                            <td>{{ $dt->pompa_usulan->total_unit }}</td>
                            <td>{{ $dt->total_unit }}</td>
                            <td>
                                <a href="{{ route('kecamatan.dimanfaatkan.create', ['diterima' => Crypt::encryptString($dt->id)]) }}" class="btn btn-sm rounded-sm text-white bg-[#070] hover:bg-[#060]">Pilih</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Data Kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="mt-5 mb-2">
            <a href="{{ route('kecamatan.dimanfaatkan.create') }}" class="btn btn-sm rounded-sm text-white bg-red-600 hover:bg-red-700">Kembali</a>
        </div>
        <form action="{{ route('kecamatan.dimanfaatkan.store') }}" method="POST" class="w-full">
            @csrf
            <input type="text" name="pompa_diterima_id" value="{{ request()->diterima }}" style="display: none;">
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Desa</label>
                        <input type="text" value="{{ $selected_diterima->pompa_usulan->desa->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Kelompok Tani</label>
                        <input type="text" value="{{ $selected_diterima->pompa_usulan->poktan->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Luas Lahan (Ha)</label>
                        <input type="text" value="{{ $selected_diterima->pompa_usulan->luas_lahan }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diusulkan</label>
                        <input type="text" value="{{ $selected_diterima->pompa_usulan->total_unit }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diterima</label>
                        <input type="text" value="{{ $selected_diterima->total_unit }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
            </div>
            <div class="flex flex-col py-1">
                <label for="total_unit" class="text-semibold">Total Unit Dimanfaatkan <span class="text-red-500">*</span></label>
                <input type="number" min="0" max="{{ $selected_diterima->total_unit }}" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
            </div>
            <div class="mt-2 flex justify-end">
                <button class="btn text-white rounded-sm bg-[#070] hover:bg-[#060]">Kirim</button>
            </div>
        </form>
    @endif
    
</div>

<script>
    
    const handleSearchDiterima = (e) => {
        const {value} = e
        const table = document.getElementById('diterima_table')
        if (value != '') table.style.display = 'block'
        else table.style.display = 'none'
        const rows = document.querySelectorAll('#diterima_table tbody tr')
        rows.forEach(row => {
            const text = row.textContent.toLowerCase()
            row.style.display = text.includes(value.toLowerCase()) ? "" : "none"
        });
    }

</script>

@endsection