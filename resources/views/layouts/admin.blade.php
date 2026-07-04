<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kinerja — @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.14.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#F1EDF4] text-gray-800"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<div class="flex min-h-screen">

    {{-- ── OVERLAY mobile ── --}}
    <div x-show="sidebarOpen"
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-[#190019]/50 backdrop-blur-[2px] z-40 lg:hidden">
    </div>

    {{-- ── SIDEBAR (floating) ── --}}
    <aside x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:-translate-x-[calc(100%+16px)]'"
           class="fixed z-50 flex flex-col transition-transform duration-300 ease-out
                  inset-y-0 left-0 w-[260px] rounded-r-[28px]
                  lg:inset-y-4 lg:left-4 lg:w-[224px] lg:rounded-[26px]
                  bg-gradient-to-b from-[#190019] via-[#241033] to-[#2B124C]
                  shadow-[0_24px_60px_-16px_rgba(25,0,25,0.55)]
                  ring-1 ring-white/[0.06]">

        {{-- Logo --}}
        <div class="px-5 pt-6 pb-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#FBE4D8] to-[#DFB6B2] shrink-0 flex items-center justify-center shadow-sm">
                    <i class="ti ti-sparkles text-[15px] text-[#2B124C]"></i>
                </span>
                <div>
                    <h1 class="text-[#FBE4D8] font-semibold text-[14px] tracking-[0.02em] leading-none">Kinerja</h1>
                    <p class="text-[#DFB6B2]/50 text-[9px] mt-1 tracking-[0.08em] uppercase leading-none">Rekomendasi Predikat</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="text-[#DFB6B2] hover:text-[#FBE4D8] lg:hidden">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <div class="mx-5 h-px bg-white/[0.07]"></div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3.5 py-4 space-y-0.5 overflow-y-auto">

            <p class="text-[10px] font-semibold text-[#DFB6B2]/35 uppercase tracking-[0.1em] px-3 pb-2">Menu Utama</p>

            <a href="#"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.dashboard') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-layout-dashboard text-[17px] shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.peserta.index') }}"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.peserta.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-users text-[17px] shrink-0"></i>
                <span>Data Peserta</span>
            </a>

            <a href="{{ route('admin.kriteria.index') }}"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.kriteria.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-list-check text-[17px] shrink-0"></i>
                <span>Kriteria Penilaian</span>
            </a>

            <a href="#"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.spk.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-brain text-[17px] shrink-0"></i>
                <span>Bobot Penilaian</span>
            </a>

            <a href="{{ route('admin.penilaian.index') }}"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.penilaian.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-pencil text-[17px] shrink-0"></i>
                <span>Penilaian Peserta</span>
            </a>

            <a href="#"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.hasil.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-certificate text-[17px] shrink-0"></i>
                <span>Hasil &amp; Rekomendasi</span>
            </a>

            <p class="text-[10px] font-semibold text-[#DFB6B2]/35 uppercase tracking-[0.1em] px-3 pt-5 pb-2">Pengaturan</p>

            <a href="#"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.pengguna.*') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-user-cog text-[17px] shrink-0"></i>
                <span>Kelola Pengguna</span>
            </a>

            <a href="#"
               class="group flex items-center gap-2.5 px-3 py-2.5 rounded-[14px] text-[13px] font-medium transition-all duration-150
               {{ request()->routeIs('admin.settings') ? 'bg-[#FBE4D8] text-[#2B124C] shadow-[0_4px_14px_-4px_rgba(251,228,216,0.4)]' : 'text-[#DFB6B2]/70 hover:bg-white/[0.07] hover:text-[#FBE4D8]' }}">
                <i class="ti ti-settings text-[17px] shrink-0"></i>
                <span>Pengaturan</span>
            </a>

        </nav>

        {{-- User & Logout --}}
        <div class="px-3.5 pb-4 pt-2">
            <div class="rounded-[16px] bg-white/[0.05] ring-1 ring-white/[0.07] p-1.5">
                <div class="flex items-center gap-2.5 px-2 py-2 mb-0.5">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#522B5B] to-[#2B124C] flex items-center justify-center text-[#FBE4D8] text-xs font-semibold shrink-0 ring-1 ring-white/10">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[#FBE4D8] text-[13px] font-medium truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[#DFB6B2]/55 text-[11px]">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2.5 w-full px-2 py-2 rounded-[12px] text-[13px] font-medium
                                   text-[#DFB6B2]/70 hover:bg-white/[0.08] hover:text-[#FBE4D8] transition-all duration-150">
                        <i class="ti ti-logout text-base shrink-0"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
         x-bind:class="sidebarOpen ? 'lg:ml-[256px]' : 'ml-0'">

        {{-- ── TOPBAR (floating) ── --}}
        <header class="h-14 mx-4 mt-4 lg:mt-4 bg-white/90 backdrop-blur-md border border-[#E8D8F0] rounded-[18px]
                        shadow-[0_8px_24px_-12px_rgba(43,18,76,0.12)] flex items-center justify-between px-5 sticky top-4 z-40">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-[#8A7A93] hover:text-[#2B124C] transition-colors">
                    <i class="ti ti-menu-2 text-lg"></i>
                </button>
                <h2 class="text-[15px] font-semibold text-[#2B124C]">@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="flex items-center gap-3">
                @yield('topbar-actions')
                <span class="text-xs text-gray-400 hidden sm:block">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </header>

        {{-- ── PAGE CONTENT ── --}}
        <main class="flex-1 px-4 sm:px-6 pt-5 pb-6">

            {{-- @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-[#E8F5ED] border border-[#2D6A4A]/20 text-[#2D6A4A] rounded-2xl text-sm flex items-center gap-2">
                    <i class="ti ti-circle-check text-base shrink-0"></i>
                    {{ session('success') }}
                </div>
            @endif --}}

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-[#F5E8EF] border border-[#7A2D4A]/20 text-[#7A2D4A] rounded-2xl text-sm flex items-center gap-2">
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