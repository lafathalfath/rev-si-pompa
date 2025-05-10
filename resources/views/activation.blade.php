@extends('layouts.auth')
@section('content')
<style>
    .btn-lihat-password {
        background: none;
        border: none;
        font-size: 14px;
        font-weight: 400;
        padding: 0;
        cursor: pointer;
    }
    .btn-lihat-password:hover {color: #060;}
</style>
    
<div class="w-full bg-white p-5 rounded-sm shadow-lg">
    <div>
        <a href="{{ route('auth.logout') }}" class="text-red-600 hover:text-red-700">Logout</a>
    </div>
    <div class="flex flex-col items-center">
        <div class="p-5 text-2xl text-center font-semibold">Aktifasi Akun</div>
    </div>
    <form action="{{ route('activate') }}" method="POST">
        @csrf
        <div class="flex flex-col py-1">
            <label for="name" class="text-semibold"><span class="text-red-600">*</span>Username</label>
            <input type="text" name="name" id="name" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
        </div>
        <div class="flex flex-col py-1">
            <label for="email" class="text-semibold"><span class="text-red-600">*</span>Email</label>
            <input type="email" name="email" id="email" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
        </div>
        <div class="flex flex-col py-1">
            <label for="phone_number" class="text-semibold"><span class="text-red-600">*</span>No. HP</label>
            <input type="text" name="phone_number" id="phone_number" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
        </div>
        <div class="flex flex-col py-1">
            <label for="password" class="text-semibold"><span class="text-red-600">*</span>Password</label>
            <input type="password" name="password" id="password" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
        </div>
        <div class="flex flex-col py-1">
            <label for="password_confirmation" class="text-semibold"><span class="text-red-600">*</span>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
            <div><button type="button" id="show_password" class="btn-lihat-password" onclick="toggleShowPassword()">Lihat Password</button></div>
        </div>
        <div class="text-red-600 text-sm">* Wajib diisi</div>
        <div class="mt-10 flex justify-center">
            <button type="submit" class="btn text-white bg-[#0a0] hover:bg-[#090]">Aktifasi</button>
        </div>
    </form>
</div>

<script>
    const toggleShowPassword = () => {
        const passwordInput = document.getElementById('password')
        const confirmPasswordInput = document.getElementById('confirm_password')
        const toggle = document.getElementById('show_password')
        passwordInput.type = passwordInput.type == 'password' ? 'text' : 'password'
        confirmPasswordInput.type = confirmPasswordInput.type == 'password' ? 'text' : 'password'
        toggle.innerHTML = toggle.innerHTML == 'Lihat Password' ? 'Sembunyikan Password' : 'Lihat Password'
    }
</script>
@endsection