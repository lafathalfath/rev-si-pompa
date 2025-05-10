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
    <div class="flex flex-col items-center">
        <div class="p-5 text-2xl text-center font-semibold">SI-Pompa</div>
    </div>
    <form action="{{ route('auth.login') }}" method="POST">
        @csrf
        <div class="flex flex-col py-1">
            <label for="name_or_email_or_nip" class="text-semibold">Username atau Email atau NIP</label>
            <input type="text" name="name_or_email_or_nip" id="name_or_email_or_nip" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
        </div>
        <div class="flex flex-col py-1">
            <label for="password" class="text-semibold">Password</label>
            <input type="password" name="password" id="password" class="py-1 px-2 w-full rounded-sm border-1 border-gray-400" required>
            <div><button type="button" id="show_password" class="btn-lihat-password" onclick="toggleShowPassword()">Lihat Password</button></div>
        </div>
        <div class="mt-5 flex justify-center">
            <button type="submit" class="btn text-white bg-[#0a0] hover:bg-[#090]">Login</button>
        </div>
    </form>

</div>

<script>
    const toggleShowPassword = () => {
        const passwordInput = document.getElementById('password')
        const toggle = document.getElementById('show_password')
        passwordInput.type = passwordInput.type == 'password' ? 'text' : 'password'
        toggle.innerHTML = toggle.innerHTML == 'Lihat Password' ? 'Sembunyikan Password' : 'Lihat Password'
    }
</script>
@endsection