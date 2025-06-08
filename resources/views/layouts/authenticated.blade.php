<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('logobbpsip.png') }}">
    <title>SI-Pompa @yield('title')</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                        <div class="px-5 mt-5">
                            <button onclick="notification_modal.showModal()" class="cursor-pointer relative"><i class="fa fa-bell" style="font-size: 18px;"></i><div id="unreaded_notification_dot" class="absolute top-0 right-0 bg-red-500 w-2 h-2 rounded-full" style="display: none;"></div></button>
                        </div>
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

    <dialog id="notification_modal" class="modal">
        <div class="modal-box">
            <div>
                <h3 class="text-lg font-bold">Notifikasi</h3>
            </div>
            <div class="flex flex-col gap-2 w-full mt-3" id="notification_list">
                <div id="notification_items" class="w-full bg-[#0703] hover:bg-[#0603] py-1 px-3">
                    <div class="m-0 p-0 text-xs flex items-center justify-end gap-1">
                        <button class="text-[#060] hover:text-[#070] cursor-pointer">buat sudah dibaca</button>
                        |
                        <button class="text-red-600 hover:text-red-700 cursor-pointer">hapus</button>
                    </div>
                    <div class="text-md font-bold">[SUB] Subject Placeholder</div>
                    <div class="w-full">Title</div>
                    <div class="m-0 p-0 text-xs text-end text-gray-500">d M 00:00</div>
                </div>
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn">Tutup</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <script>
        const apiToken = @json(session('api_token'))

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

        const getAllNotification = async () => {
            try {
                const response = await fetch('/api/notification', {
                    headers: {"Authorization": `Bearer ${apiToken}`}
                })
                const data = await response.json()
                if (!data) return
                console.log(data)
                document.getElementById('unreaded_notification_dot').style.display = data.has_any_read > 0 ? '' : 'none'
                if (data.notifications) {
                    const {notifications} = data
                    const notificationElementList = document.getElementById('notification_list')
                    let notificationItems = ''
                    notifications.forEach(notif => {
                        notificationItems += `
                            <div id="notification_items" class="w-full ${notif.is_read ? 'hover:bg-gray-200' : 'bg-[#0703] hover:bg-[#0603]'} py-1 px-3">
                                <div class="m-0 p-0 text-xs flex justify-end">
                                    ${notif.is_read ?
                                    '<button class="text-red-600 hover:text-red-700 cursor-pointer">hapus</button>'
                                    : '<button class="text-[#060] hover:text-[#070] cursor-pointer">buat sudah dibaca</button>'}
                                </div>
                                <div class="text-md font-bold">${notif.subject}</div>
                                <div class="w-full">${notif.title}</div>
                                <div class="m-0 p-0 text-xs text-end text-gray-500">${notif.created_at}</div>
                            </div>
                        `
                    });
                    notificationElementList.innerHTML = notificationItems
                }
            } catch (err) {
                console.error(err.message)
            }
        }

        getAllNotification()
    </script>
</body>
</html>