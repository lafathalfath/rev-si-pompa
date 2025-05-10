<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="logobbpsip.png">
    <title>SI-Pompa</title>
    {{-- <link rel="stylesheet" href="/assets/css/app.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <style>
    </style>
</head>
<body class="w-full h-[100vh] overflow-hidden flex items-center justify-center bg-gray-200">

    {{-- <div>
        {{ dd(session('success')) }}
        @if (session('success'))
            okeh
        @endif
    </div> --}}
    <div class="w-[400px] max-w-full p-1 max-h-full">
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
        
        <div class="auth-container card">
            @yield('content')
        </div>
    </div>

</body>
</html>