<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('logobbpsip.png') }}">
    <title>SI-Pompa</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <style>
        .nav-link {
            width: 100%;
            text-decoration: none;
            font-weight: 600;
            border-radius: 5px;
            padding: 10px 15px;
            color: #fff;
        }
        .nav-link:hover {
            background-color: #060;
        }
        .active {background-color: #050;}
    </style>
</head>
@php
    $user = auth()->user();
    $role = $user->role->name;
    // dd(str_starts_with(request()->url(), route('kecamatan.usulan')));
@endphp
<body class="w-full h-[100vh] overflow-hidden bg-gray-100">

    <div class="w-full h-full flex">
        <aside class="p-1">
            <nav id="sidebar" class="text-white bg-[#070] p-[10px] w-[250px] h-full rounded-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-end">
                        <button type="button" class="btn text-lg text-white border-none shadow-none bg-transparent hover:bg-[#060]" onclick="toggleMinimizeSidebar()">&#9776;</button>
                    </div>
                    <div class="w-full flex flex-col items-center py-5 mb-10 border-b-1 border-white">
                        <img src="{{ asset('logobbpsip.png') }}" alt="">
                        <div class="text-xl font-semibold px-4 pt-2" id="app_name">SI-Pompa</div>
                    </div>
                    <div class="flex flex-col">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->url() == route('dashboard') ? 'active' : '' }}">Dashboard</a>
                        @if ($role == 'admin')
                            <a href="{{ route('admin.daftar_pj') }}" class="nav-link {{ request()->url() == route('admin.daftar_pj') ? 'active' : '' }}">Daftar Penanggung Jawab</a>
                        @endif
                        @if ($role == 'pj_kabupaten')
                            <a href="{{ route('kabupaten.usulan') }}" class="nav-link {{ str_starts_with(request()->url(), route('kabupaten.usulan')) ? 'active' : '' }}">Pompa Usulan</a>
                            <a href="{{ route('kabupaten.diterima') }}" class="nav-link {{ request()->url() == route('kabupaten.diterima') ? 'active' : '' }}">Pompa Diterima</a>
                            <a href="{{ route('kabupaten.dimanfaatkan') }}" class="nav-link {{ request()->url() == route('kabupaten.dimanfaatkan') ? 'active' : '' }}">Pompa Dimanfaatkan</a>
                            <a href="{{ route('kabupaten.luas_tanam') }}" class="nav-link {{ request()->url() == route('kabupaten.luas_tanam') ? 'active' : '' }}">Luas Tanam Harian</a>
                        @endif
                        @if ($role == 'pj_kecamatan')
                            <a href="{{ route('kecamatan.usulan') }}" class="nav-link {{ request()->url() == route('kecamatan.usulan') ? 'active' : '' }}">Pompa Usulan</a>
                            <a href="{{ route('kecamatan.diterima') }}" class="nav-link {{ request()->url() == route('kecamatan.diterima') ? 'active' : '' }}">Pompa Diterima</a>
                            <a href="{{ route('kecamatan.dimanfaatkan') }}" class="nav-link {{ request()->url() == route('kecamatan.dimanfaatkan') ? 'active' : '' }}">Pompa Dimanfaatkan</a>
                            <a href="{{ route('kecamatan.luas_tanam') }}" class="nav-link {{ request()->url() == route('kecamatan.luas_tanam') ? 'active' : '' }}">Luas Tanam Harian</a>
                        @endif
                    </div>
                </div>
                <div class="w-full">
                    {{-- <a href="" class="w-full py-1.5 px-4 border-2 rounded-sm border-red-600 font-semibold text-red-600 hover:text-white hover:bg-red-600">Logout</a> --}}
                </div>
            </nav>
        </aside>
        
        <div class="w-full h-full">
            <div class="p-1 h-fit">
                <div class="w-full p-3 bg-orange-400 text-white rounded-sm flex items-center justify-between">
                    <div class="capitalize"><span class="text-black bg-white py-1 px-3 rounded-sm font-semibold">{{ $user->name }}</span>&ensp;{{ str_replace('_', ' ', $role) }}{{ $user->region ? " | ".$user->region->name : '' }}</div>
                    <div>
                        <a href="{{ route('auth.logout') }}" class="btn text-white bg-red-500 border-none shadow-none hover:bg-red-600">Logout</a>
                    </div>
                </div>
            </div>
            <div class="p-5 h-9/10 w-full p-1 overflow-y-scroll overflow-x-hidden">
                <div class="w-full p-1 max-h-full">
                    <div class="alert-container">
                        @if(session('success'))
                            <div class="alert bg-success">{{ session('success') }}</div>
                        @endif
                        @if(session('errors'))
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-error" style="color: #fff;">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div>
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggleMinimizeSidebar = () => {
            const sidebar = document.getElementById('sidebar')
            const navLink = document.getElementsByClassName('nav-link')
            const appName = document.getElementById('app_name')
            sidebar.style.width = sidebar.style.width=='65px'?'250px':'65px'
            appName.style.display = appName.style.display == 'none' ? 'block' : 'none'
            navLink.forEach(e => {
                e.style.width = e.style.width
            });
        }
    </script>
</body>
</html>