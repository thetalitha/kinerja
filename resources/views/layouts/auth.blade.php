<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPK-Fuzzy — @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-[#F7F8FA] flex items-center justify-center">

    <div class="w-full max-w-md px-4 py-8">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-[#2B124C] tracking-[0.04em]">Sistem Pendukung Keputusan Kinerja Peserta Magang</h1>
            <p class="text-[#854F6C] text-[11px] mt-1 uppercase tracking-[0.07em]">Rekomendasi Predikat Kinerja</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-[#E8F5ED] border border-[#2D6A4A]/20 text-[#2D6A4A] rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-[#F5E8EF] border border-[#7A2D4A]/20 text-[#7A2D4A] rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Content --}}
        @yield('content')

    </div>

</body>
</html>