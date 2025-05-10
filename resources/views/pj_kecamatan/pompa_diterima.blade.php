@extends('layouts.authenticated')
@section('content')
    
<div>
    <div class="text-xl font-bold">Data Pompa Diterima</div>
    
    <div class="flex justify-end mb-5"><button class="btn rounded-sm text-white bg-[#070] hover:bg-[#060]"
        onclick=""
    >+ Tambah Data</button></div>
    <table class="w-full">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kelompok Tani</th>
                <th>Desa</th>
                <th>Luas Lahan (Ha)</th>
                <th>Total Usulan</th>
                <th>Total Diterima</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($diterima as $dt)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="flex items-center justify-between">
                        <div>{{ $dt->pompa_usulan->poktan->name }}</div>
                        <button type="button" class="btn btn-sm bg-[#0bf] hover:bg-[#0ae] text-black rounded-sm" 
                            onclick=""
                        >Detail</button>
                    </td>
                    <td>{{ $dt->pompa_usulan->desa->name }}</td>
                    <td>{{ $dt->pompa_usulan->luas_lahan }}</td>
                    <td>{{ $dt->pompa_usulan->total_unit }}</td>
                    <td>{{ $dt->total_unit }}</td>
                    <td>
                        @if ($dt->status == 'diverifikasi')
                            <div class="badge bg-[#090] text-white font-semibold rounded-sm">Terverifikasi</div>
                        @elseif($dt->status == 'ditolak')
                            <div class="badge text-white bg-red-600 font-semibold rounded-sm">Ditolak</div>
                        @else
                            <div class="badge text-black bg-[#ffc800] font-semibold rounded-sm">Belum Diverifikasi</div>
                        @endif
                    </td>
                    <td>
                        @if ($usul->status != 'diverifikasi')
                            <button class="btn btn-sm bg-[#ffc800] hover:bg-[#eeb700] text-black rounded-sm" 
                                onclick=""
                            >Edit</button>
                            <button class="btn btn-sm bg-red-600 hover:bg-red-700 text-white rounded-sm" 
                                onclick=""
                            >Hapus</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Data Kosong</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection