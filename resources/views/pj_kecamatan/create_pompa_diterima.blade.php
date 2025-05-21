@extends('layouts.authenticated')
@section('title')| Tambah Pompa Diterima @endsection
@section('content')

<div>
    <div class="text-xl font-bold">Tambah Data Pompa Diterima</div>
    
    @if (!$selected_usulan)
        <div class="mt-5 mb-2">
            <a href="{{ route('kecamatan.diterima') }}" class="btn btn-sm rounded-sm text-white bg-red-600 hover:bg-red-700">Kembali</a>
        </div>
        <div class="mt-2">
            <div class="flex flex-col py-1">
                <label for="usulan" class="text-semibold">Cari Data Pompa Usulan</label>
                <input type="search" name="usulan" id="usulan" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" oninput="handleSearchUsulan(this)">
            </div>
        </div>

        <div class="w-full">
            <table class="w-full" id="usulan_table" style="display: none">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Desa</th>
                        <th>Kelompok Tani</th>
                        <th>Luas Lahan (Ha)</th>
                        <th>Total Unit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usulan as $us)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $us->desa->name }}</td>
                            <td>{{ $us->poktan->name }}</td>
                            <td>{{ $us->luas_lahan }}</td>
                            <td>{{ $us->total_unit }}</td>
                            <td>
                                <a href="{{ route('kecamatan.diterima.create', ['usulan' => Crypt::encryptString($us->id)]) }}" class="btn btn-sm rounded-sm text-white bg-[#070] hover:bg-[#060]">Pilih</a>
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
            <a href="{{ route('kecamatan.diterima.create') }}" class="btn btn-sm rounded-sm text-white bg-red-600 hover:bg-red-700">Kembali</a>
        </div>
        <form action="{{ route('kecamatan.diterima.store') }}" method="POST" class="w-full">
            @csrf
            <input type="text" name="pompa_usulan_id" value="{{ request()->usulan }}" style="display: none;">
            <div class="flex flex-wrap gap-2">
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Desa</label>
                        <input type="text" value="{{ $selected_usulan->desa->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Kelompok Tani</label>
                        <input type="text" value="{{ $selected_usulan->poktan->name }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Luas Lahan (Ha)</label>
                        <input type="text" value="{{ $selected_usulan->luas_lahan }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex flex-col py-1">
                        <label class="text-semibold">Total Unit Diusulkan</label>
                        <input type="text" value="{{ $selected_usulan->total_unit }}" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" readonly disabled>
                    </div>
                </div>
            </div>
            <div class="flex flex-col py-1">
                <label for="total_unit" class="text-semibold">Total Unit Diterima <span class="text-red-500">*</span></label>
                <input type="number" min="0" max="{{ $selected_usulan->total_unit }}" name="total_unit" id="total_unit" class="py-1 px-2 w-98 rounded-sm border-1 border-gray-400" required>
            </div>
            <div class="mt-2 flex justify-end">
                <button class="btn text-white rounded-sm bg-[#070] hover:bg-[#060]">Kirim</button>
            </div>
        </form>
    @endif
    
</div>

<script>
    
    const handleSearchUsulan = (e) => {
        const {value} = e
        const table = document.getElementById('usulan_table')
        if (value != '') table.style.display = 'block'
        else table.style.display = 'none'
        const rows = document.querySelectorAll('#usulan_table tbody tr')
        rows.forEach(row => {
            const text = row.textContent.toLowerCase()
            row.style.display = text.includes(value.toLowerCase()) ? "" : "none"
        });
    }

</script>

@endsection