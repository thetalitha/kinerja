<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kinerja — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.14.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F7F8FA] text-gray-800"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<div class="flex min-h-screen">

    {{-- ── OVERLAY mobile ── --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    {{-- ── SIDEBAR ── --}}
    <aside x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="w-[220px] min-h-screen bg-[#190019] fixed top-0 left-0 z-50 flex flex-col transition-transform duration-300">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-white/[0.08] flex items-center justify-between">
            <div>
                <h1 class="text-[#FBE4D8] font-semibold text-m tracking-[0.04em]">Kinerja</h1>
                <p class="text-[#DFB6B2]/60 text-[10px] mt-0.5 tracking-[0.07em] uppercase">Rekomendasi Predikat Kinerja</p>
            </div>
            <button @click="sidebarOpen = false" class="text-[#DFB6B2] hover:text-[#FBE4D8] lg:hidden">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        {{-- Role Badge
        <div class="mx-4 mt-3 mb-1 px-2.5 py-1.5 rounded-lg flex items-center gap-2
                    bg-[#FBE4D8]/10 border border-[#FBE4D8]/20">
            <span class="w-[7px] h-[7px] rounded-full bg-[#FBE4D8] shrink-0"></span>
            <span class="text-[#FBE4D8] text-[10px] font-medium tracking-[0.06em] uppercase">Admin</span>
        </div> --}}

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">

            <p class="text-[10px] font-medium text-[#DFB6B2]/40 uppercase tracking-[0.08em] px-3 pt-3 pb-1.5">Menu Utama</p>

            <a href="#"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.dashboard') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-layout-dashboard text-base shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.peserta.index') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.peserta.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-users text-base shrink-0"></i>
                <span>Data Peserta</span>
            </a>

            <a href="{{ route('admin.kriteria.index') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.kriteria.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-list-check text-base shrink-0"></i>
                <span>Kriteria Penilaian</span>
            </a>

            <a href="#"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.spk.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-brain text-base shrink-0"></i>
                <span>Bobot Penilaian</span>
            </a>

            <a href="{{ route('admin.penilaian.index') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.penilaian.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-pencil text-base shrink-0"></i>
                <span>Penilaian peserta</span>
            </a>

            <a href="#"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.hasil.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-certificate text-base shrink-0"></i>
                <span>Hasil &amp; Rekomendasi</span>
            </a>

            <p class="text-[10px] font-medium text-[#DFB6B2]/40 uppercase tracking-[0.08em] px-3 pt-4 pb-1.5">Pengaturan</p>

            <a href="#"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.pengguna.*') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-user-cog text-base shrink-0"></i>
                <span>Kelola Pengguna</span>
            </a>

            <a href="#"
               class="flex items-center gap-2.5 px-3 py-2 rounded-[7px] text-[13px] transition-all duration-150
               {{ request()->routeIs('admin.settings') ? 'bg-[#522B5B] text-[#FBE4D8]' : 'text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2]' }}">
                <i class="ti ti-settings text-base shrink-0"></i>
                <span>Pengaturan</span>
            </a>

        </nav>

        {{-- User & Logout --}}
        <div class="px-3 py-3 border-t border-white/[0.08]">
            <div class="flex items-center gap-2.5 px-3 py-2 mb-1">
                <div class="w-8 h-8 rounded-full bg-[#2B124C] flex items-center justify-center text-[#FBE4D8] text-xs font-semibold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-[#FBE4D8] text-[13px] font-medium truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[#DFB6B2]/60 text-[11px]">Administrator</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2.5 w-full px-3 py-2 rounded-[7px] text-[13px]
                               text-[#DFB6B2]/75 hover:bg-white/[0.06] hover:text-[#DFB6B2] transition-all duration-150">
                    <i class="ti ti-logout text-base shrink-0"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>

    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
         x-bind:class="sidebarOpen ? 'lg:ml-[220px]' : 'ml-0'">

        {{-- ── TOPBAR ── --}}
        <header class="h-14 bg-white border-b border-[#E8D8F0] flex items-center justify-between px-6 sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                        x-show="!sidebarOpen || window.innerWidth < 1024"
                        class="text-gray-400 hover:text-gray-700 transition-colors">
                    <i class="ti ti-menu-2 text-lg"></i>
                </button>
                <h2 class="text-[15px] font-medium text-[#2B124C]">@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center gap-3">
                @yield('topbar-actions')
                <span class="text-xs text-gray-400 hidden sm:block">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        {{-- ── PAGE CONTENT ── --}}
        <main class="flex-1 p-6">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-[#E8F5ED] border border-[#2D6A4A]/20 text-[#2D6A4A] rounded-xl text-sm flex items-center gap-2">
                    <i class="ti ti-circle-check text-base shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-[#F5E8EF] border border-[#7A2D4A]/20 text-[#7A2D4A] rounded-xl text-sm flex items-center gap-2">
                    <i class="ti ti-alert-circle text-base shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')
</body>
</html>