@extends('layouts.authenticated')
@section('title')| Dashboard @endsection
@section('content')
<div>
    <h1 class="text-2xl font-bold text-center mb-5">DASHBOARD</h1>

    <div class="my-2 w-full bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-lg font-semibold">
        Total Pompa Diusulkan: <span>{{ $total_pompa }}</span>
    </div>

    <div class="my-2 w-full bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-lg font-semibold flex items-center justify-evenly">
        <a href="{{ route('kecamatan.history.verified') }}" class="flex flex-col items-center">
            <div id="progress_percentage" class="text-green-600 radial-progress outline-[0.5rem] outline-gray-300 -outline-offset-[0.5rem]" style="--value:{{ $total_pompa ? $diverifikasi*100/$total_pompa : 0 }};--thickness:0.5rem;" aria-valuenow="{{ $total_pompa ? $diverifikasi*100/$total_pompa : 0 }}" role="progressbar">{{ floor($total_pompa ? $diverifikasi*100/$total_pompa : 0) }}%</div>
            <div class="text-center">Diverifikasi</div>
        </a>
        <a href="{{ route('kecamatan.history.denied') }}" class="flex flex-col items-center">
            <div id="progress_percentage" class="text-red-600 radial-progress outline-[0.5rem] outline-gray-300 -outline-offset-[0.5rem]" style="--value:{{ $total_pompa ? $usulan_ditolak*100/$total_pompa : 0 }};--thickness:0.5rem;" aria-valuenow="{{ $total_pompa ? $usulan_ditolak*100/$total_pompa : 0 }}" role="progressbar">{{ floor($total_pompa ? $usulan_ditolak*100/$total_pompa : 0) }}%</div>
            <div>Ditolak</div>
        </a>
    </div>

    <div class="my-2 w-full flex flex-col gap-2">
        <div class="w-full flex items-center gap-2">
            <a href="{{ route('kecamatan.usulan') }}" class="w-1/2 bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-center text-lg font-semibold">Mengunggu Persetujuan:<br>{{ $usulan_pending }}</a>
            <a href="{{ route('kecamatan.dimanfaatkan', ['s' => 'ongoing']) }}" class="w-1/2 bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-center text-lg font-semibold">Dalam Pemanfaatan:<br>{{ $pemanfaatan_ongoing }}</a>
        </div>
        <div class="w-full flex items-center gap-2">
            <a href="{{ route('kecamatan.dimanfaatkan', ['s' => 'pending']) }}" class="w-1/2 bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-center text-lg font-semibold">Belum Dimanfaatkan:<br>{{ $pemanfaatan_pending }}</a>
            <a href="{{ route('kecamatan.dimanfaatkan', ['s' => 'completed']) }}" class="w-1/2 bg-white p-3 rounded border-gray-200 border-1 shadow-lg text-center text-lg font-semibold">Menunggu Verifikasi:<br>{{ $pemanfaatan_completed }}</a>
        </div>
    </div>

    <div class="my-2 w-fit bg-white p-3 rounded border-gray-200 border-1 shadow-lg flex flex-col items-center">
        <div class="text-lg font-semibold my-2">Jumlah Usulan Per Desa</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Desa</th>
                    <th>Jumlah Usulan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usulan_per_desa as $us)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $us->name }}</td>
                        <td>{{ $us->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection