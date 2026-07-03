<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinerja — Sistem Penilaian Magang yang Mudah Dipahami</title>
    <meta name="description" content="Kinerja membantu menentukan kriteria, bobot, dan rekomendasi predikat kinerja peserta magang — tanpa perlu paham matematika di baliknya.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.14.0/dist/tabler-icons.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Fraunces', serif; }
        [x-cloak] { display: none !important; }

        .grain {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.04) 1px, transparent 0);
            background-size: 18px 18px;
        }

        .beam-glow {
            background: radial-gradient(60% 60% at 50% 30%, rgba(251,228,216,0.16) 0%, rgba(251,228,216,0) 70%);
        }

        @keyframes tilt-left {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-6deg); }
        }
        @keyframes tilt-right {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(6deg); }
        }

        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#FAF7F9] text-[#241726] antialiased">

<div x-data="landingPage()" x-cloak>

    {{-- ============ NAVBAR ============ --}}
    <header class="sticky top-0 z-50 bg-[#FAF7F9]/85 backdrop-blur-md border-b border-[#E8D8F0]">
        <div class="max-w-6xl mx-auto px-5 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#190019] flex items-center justify-center">
                    <i class="ti ti-scale text-[#FBE4D8] text-base"></i>
                </div>
                <div>
                    <p class="text-[14px] font-bold leading-none tracking-tight">Kinerja</p>
                    <p class="text-[9.5px] text-[#522B5B]/70 uppercase tracking-[0.08em] leading-none mt-0.5">Predikat Magang</p>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-7 text-[13px] font-medium text-[#522B5B]">
                <a href="#cara-kerja" class="hover:text-[#190019] transition-colors">Cara Kerja</a>
                <a href="#coba-bobot" class="hover:text-[#190019] transition-colors">Coba Pembobotan</a>
                <a href="#fitur" class="hover:text-[#190019] transition-colors">Fitur</a>
            </nav>

            <a href="{{ route('login') }}"
               class="bg-[#2B124C] text-[#FBE4D8] text-[13px] font-semibold px-4 py-2 rounded-xl hover:bg-[#522B5B] transition-colors shrink-0">
                Masuk ke Aplikasi
            </a>
        </div>
    </header>

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-[#190019] grain">
        <div class="absolute inset-0 beam-glow pointer-events-none"></div>
        <div class="max-w-6xl mx-auto px-5 lg:px-8 pt-16 pb-20 lg:pt-24 lg:pb-28 relative">
            <div class="grid lg:grid-cols-[1.1fr_0.9fr] gap-12 items-center">

                {{-- Copy --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[0.06] border border-white/10 text-[#DFB6B2] text-[11.5px] font-medium mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FBE4D8]"></span>
                        Untuk pembimbing & HR yang bukan orang IT
                    </div>

                    <h1 class="font-display text-[#FBE4D8] text-[34px] sm:text-[44px] lg:text-[52px] leading-[1.08] font-medium mb-5">
                        Menilai kinerja peserta magang,<br class="hidden sm:block">
                        <span class="italic">tanpa pusing rumus.</span>
                    </h1>

                    <p class="text-[#DFB6B2]/85 text-[15.5px] lg:text-[17px] leading-relaxed max-w-lg mb-8">
                        Kinerja menuntun Anda dari menyusun kriteria, menentukan mana yang lebih penting, sampai
                        keluar rekomendasi predikat — lewat pertanyaan dalam bahasa sehari-hari. Perhitungan
                        <span class="text-[#FBE4D8] font-semibold">Fuzzy AHP</span> di baliknya berjalan sendiri.
                    </p>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="#cara-kerja"
                           class="bg-[#FBE4D8] text-[#190019] text-[14px] font-semibold px-5 py-3 rounded-xl hover:bg-white transition-colors inline-flex items-center gap-2">
                            Lihat cara kerjanya
                            <i class="ti ti-arrow-down text-base"></i>
                        </a>
                        <a href="#coba-bobot"
                           class="border border-white/20 text-[#FBE4D8] text-[14px] font-semibold px-5 py-3 rounded-xl hover:bg-white/[0.06] transition-colors inline-flex items-center gap-2">
                            <i class="ti ti-player-play text-base"></i>
                            Coba demo pembobotan
                        </a>
                    </div>
                </div>

                {{-- Signature visual: timbangan kriteria --}}
                <div class="relative mx-auto w-full max-w-sm">
                    <div class="bg-white/[0.04] border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
                        <p class="text-[#DFB6B2]/60 text-[10.5px] uppercase tracking-[0.1em] font-medium mb-5">Contoh pembobotan</p>

                        <div class="flex items-end justify-center gap-3 h-32 mb-5">
                            <div class="flex flex-col items-center gap-2" style="animation: tilt-left 5s ease-in-out infinite;">
                                <div class="bg-[#FBE4D8] text-[#190019] text-[11px] font-semibold px-2.5 py-1 rounded-lg">62%</div>
                                <div class="w-1 bg-white/20 h-12"></div>
                                <div class="w-20 h-14 rounded-lg bg-[#522B5B]/40 border border-white/10 flex items-center justify-center text-center px-1.5">
                                    <span class="text-[#FBE4D8] text-[10px] font-medium leading-tight">Kedisiplinan</span>
                                </div>
                            </div>
                            <div class="w-px h-24 bg-white/15 self-end mb-0"></div>
                            <div class="flex flex-col items-center gap-2" style="animation: tilt-right 5s ease-in-out infinite;">
                                <div class="bg-white/10 text-[#DFB6B2] text-[11px] font-semibold px-2.5 py-1 rounded-lg">38%</div>
                                <div class="w-1 bg-white/20 h-8"></div>
                                <div class="w-20 h-14 rounded-lg bg-white/[0.05] border border-white/10 flex items-center justify-center text-center px-1.5">
                                    <span class="text-[#DFB6B2] text-[10px] font-medium leading-tight">Komunikasi</span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[#DFB6B2]/70 text-[12px] text-center leading-relaxed">
                            "Kedisiplinan" dianggap lebih penting, jadi bobotnya otomatis lebih besar.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ CARA KERJA (4 langkah nyata) ============ --}}
    <section id="cara-kerja" class="max-w-6xl mx-auto px-5 lg:px-8 py-20 lg:py-28">
        <div class="max-w-xl mb-14">
            <p class="text-[#522B5B] text-[12px] font-semibold uppercase tracking-[0.1em] mb-3">Alur kerja</p>
            <h2 class="font-display text-[28px] lg:text-[36px] font-medium leading-tight text-[#190019]">
                Empat tahap, dari nol sampai rekomendasi predikat
            </h2>
            <p class="text-[#522B5B]/80 text-[15px] mt-3 leading-relaxed">
                Urutannya memang harus begini — kriteria perlu ada dulu sebelum bisa dibobotkan, dan bobot perlu ada
                sebelum nilai peserta bisa dihitung jadi predikat.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">

            <div class="bg-white rounded-2xl border border-[#E8D8F0] p-6 relative">
                <p class="text-[42px] font-display italic text-[#EDE8F5] leading-none mb-4">01</p>
                <div class="w-10 h-10 rounded-xl bg-[#EDE8F5] flex items-center justify-center mb-4">
                    <i class="ti ti-list-check text-[#2B124C] text-lg"></i>
                </div>
                <h3 class="font-semibold text-[15px] text-[#190019] mb-2">Susun kriteria</h3>
                <p class="text-[13px] text-[#522B5B]/75 leading-relaxed">
                    Tentukan apa yang mau dinilai — misalnya Kedisiplinan, Kualitas Kerja, atau Inisiatif. Bisa juga
                    dipecah jadi sub-kriteria yang lebih detail.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-[#E8D8F0] p-6 relative">
                <p class="text-[42px] font-display italic text-[#EDE8F5] leading-none mb-4">02</p>
                <div class="w-10 h-10 rounded-xl bg-[#EDE8F5] flex items-center justify-center mb-4">
                    <i class="ti ti-brain text-[#2B124C] text-lg"></i>
                </div>
                <h3 class="font-semibold text-[15px] text-[#190019] mb-2">Bobotkan kriteria</h3>
                <p class="text-[13px] text-[#522B5B]/75 leading-relaxed">
                    Bandingkan dua kriteria sekaligus lewat pertanyaan biasa: "mana yang lebih penting, dan
                    seberapa jauh?" Sistem yang menghitung skornya.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-[#E8D8F0] p-6 relative">
                <p class="text-[42px] font-display italic text-[#EDE8F5] leading-none mb-4">03</p>
                <div class="w-10 h-10 rounded-xl bg-[#EDE8F5] flex items-center justify-center mb-4">
                    <i class="ti ti-pencil text-[#2B124C] text-lg"></i>
                </div>
                <h3 class="font-semibold text-[15px] text-[#190019] mb-2">Nilai peserta</h3>
                <p class="text-[13px] text-[#522B5B]/75 leading-relaxed">
                    Isi nilai tiap peserta magang per kriteria — bisa angka, persentase, atau pilihan kata seperti
                    "Baik" dan "Cukup".
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-[#E8D8F0] p-6 relative">
                <p class="text-[42px] font-display italic text-[#EDE8F5] leading-none mb-4">04</p>
                <div class="w-10 h-10 rounded-xl bg-[#EDE8F5] flex items-center justify-center mb-4">
                    <i class="ti ti-certificate text-[#2B124C] text-lg"></i>
                </div>
                <h3 class="font-semibold text-[15px] text-[#190019] mb-2">Lihat rekomendasi</h3>
                <p class="text-[13px] text-[#522B5B]/75 leading-relaxed">
                    Sistem menggabungkan nilai dan bobot, lalu keluar predikat akhir tiap peserta — siap dipakai
                    untuk laporan.
                </p>
            </div>

        </div>
    </section>

    {{-- ============ DEMO INTERAKTIF: PEMBOBOTAN SAATY ============ --}}
    <section id="coba-bobot" class="bg-[#190019] grain relative overflow-hidden">
        <div class="absolute inset-0 beam-glow pointer-events-none"></div>
        <div class="max-w-6xl mx-auto px-5 lg:px-8 py-20 lg:py-28 relative">

            <div class="max-w-xl mb-12">
                <p class="text-[#DFB6B2] text-[12px] font-semibold uppercase tracking-[0.1em] mb-3">Coba sendiri · data contoh</p>
                <h2 class="font-display text-[#FBE4D8] text-[28px] lg:text-[36px] font-medium leading-tight">
                    Begini rasanya membobotkan kriteria
                </h2>
                <p class="text-[#DFB6B2]/80 text-[15px] mt-3 leading-relaxed">
                    Geser slidernya. Anda tidak akan melihat angka matriks atau istilah "skala Saaty" sama sekali —
                    cukup pilih mana yang lebih penting, dan seberapa jauh.
                </p>
            </div>

            <div class="grid lg:grid-cols-[1fr_0.85fr] gap-6">

                {{-- Kartu pertanyaan & slider --}}
                <div class="bg-white rounded-2xl p-6 lg:p-8">

                    <div class="flex items-center justify-between mb-6">
                        <p class="text-[11.5px] font-medium text-[#522B5B]/70 uppercase tracking-[0.08em]">
                            Pertanyaan <span x-text="current + 1"></span> dari <span x-text="pairs.length"></span>
                        </p>
                        <div class="flex gap-1">
                            <template x-for="(p, i) in pairs" :key="i">
                                <div class="h-1.5 w-6 rounded-full" :class="i === current ? 'bg-[#2B124C]' : (i < current ? 'bg-[#DFB6B2]' : 'bg-[#EDE8F5]')"></div>
                            </template>
                        </div>
                    </div>

                    <template x-for="(p, i) in pairs" :key="i">
                        <div x-show="current === i">
                            <p class="text-[16px] lg:text-[18px] font-semibold text-[#190019] mb-1">
                                Mana yang lebih penting untuk menilai peserta magang?
                            </p>
                            <p class="text-[13px] text-[#522B5B]/70 mb-6">Geser ke arah yang menurut Anda lebih penting.</p>

                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[14px] font-semibold px-3 py-1.5 rounded-lg" :class="sliderValue(i) < 0 ? 'bg-[#2B124C] text-[#FBE4D8]' : 'bg-[#F7F8FA] text-[#522B5B]'" x-text="p.a"></span>
                                <span class="text-[14px] font-semibold px-3 py-1.5 rounded-lg" :class="sliderValue(i) > 0 ? 'bg-[#2B124C] text-[#FBE4D8]' : 'bg-[#F7F8FA] text-[#522B5B]'" x-text="p.b"></span>
                            </div>

                            <input type="range" min="-4" max="4" step="1"
                                   x-model.number="answers[i]"
                                   class="w-full h-2 rounded-full bg-[#EDE8F5] appearance-none cursor-pointer accent-[#2B124C]">

                            <p class="text-center mt-5 text-[14px] text-[#190019] bg-[#F7F8FA] border border-[#E8D8F0] rounded-xl py-3 px-4 leading-relaxed" x-text="describeAnswer(i)"></p>
                        </div>
                    </template>

                    <div class="flex items-center justify-between mt-7">
                        <button @click="current = Math.max(0, current - 1)"
                                x-show="current > 0"
                                class="text-[13px] font-medium text-[#522B5B] hover:text-[#190019] px-3 py-2 transition-colors">
                            ← Sebelumnya
                        </button>
                        <span x-show="current === 0"></span>

                        <button @click="current = Math.min(pairs.length - 1, current + 1)"
                                x-show="current < pairs.length - 1"
                                class="bg-[#2B124C] text-[#FBE4D8] text-[13px] font-semibold px-4 py-2.5 rounded-xl hover:bg-[#522B5B] transition-colors">
                            Pertanyaan berikutnya →
                        </button>
                        <button @click="current = 0" x-show="current === pairs.length - 1"
                                class="bg-[#EDE8F5] text-[#2B124C] text-[13px] font-semibold px-4 py-2.5 rounded-xl hover:bg-[#E8D8F0] transition-colors">
                            ↺ Ulangi dari awal
                        </button>
                    </div>
                </div>

                {{-- Hasil bobot live --}}
                <div class="bg-white/[0.05] border border-white/10 rounded-2xl p-6 lg:p-8">
                    <p class="text-[11.5px] font-medium text-[#DFB6B2]/70 uppercase tracking-[0.08em] mb-5">Hasil bobot saat ini</p>

                    <div class="space-y-4">
                        <template x-for="crit in criteria" :key="crit">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[13.5px] font-medium text-[#FBE4D8]" x-text="crit"></span>
                                    <span class="text-[13px] font-semibold text-[#DFB6B2]" x-text="weights[crit] + '%'"></span>
                                </div>
                                <div class="h-2.5 rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[#DFB6B2] to-[#FBE4D8] transition-all duration-300" :style="`width: ${weights[crit]}%`"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 pt-5 border-t border-white/10">
                        <p class="text-[12.5px] text-[#DFB6B2]/70 leading-relaxed">
                            <i class="ti ti-info-circle text-[13px] -mt-0.5 inline-block"></i>
                            Di aplikasi asli, jawaban ini diolah dengan <span class="text-[#FBE4D8] font-medium">Fuzzy AHP</span>
                            supaya hasilnya tetap konsisten meski penilai ragu-ragu di antara dua pilihan.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============ FITUR ============ --}}
    <section id="fitur" class="max-w-6xl mx-auto px-5 lg:px-8 py-20 lg:py-28">
        <div class="max-w-xl mb-14">
            <p class="text-[#522B5B] text-[12px] font-semibold uppercase tracking-[0.1em] mb-3">Yang sudah bisa dipakai</p>
            <h2 class="font-display text-[28px] lg:text-[36px] font-medium leading-tight text-[#190019]">
                Dibangun bertahap, ditandai dengan jelas
            </h2>
            <p class="text-[#522B5B]/80 text-[15px] mt-3 leading-relaxed">
                Sebagian fitur sudah berjalan, sebagian masih disiapkan. Supaya tidak menebak-nebak.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

            @php
            $features = [
                ['icon' => 'ti-users', 'title' => 'Data Peserta', 'desc' => 'Kelola data peserta magang: institusi, periode, dan fungsi/divisi tempat mereka ditempatkan.', 'status' => 'ready'],
                ['icon' => 'ti-list-check', 'title' => 'Kriteria Penilaian', 'desc' => 'Susun kriteria dan sub-kriteria, lalu tentukan cara penilaiannya: angka, persentase, atau pilihan kata.', 'status' => 'ready'],
                ['icon' => 'ti-brain', 'title' => 'Bobot Penilaian', 'desc' => 'Bandingkan kriteria berpasangan lewat pertanyaan sederhana — perhitungan Fuzzy AHP berjalan di belakang layar.', 'status' => 'soon'],
                ['icon' => 'ti-pencil', 'title' => 'Penilaian Peserta', 'desc' => 'Input nilai tiap peserta per kriteria, termasuk sinkronisasi otomatis dari catatan logbook.', 'status' => 'ready'],
                ['icon' => 'ti-certificate', 'title' => 'Hasil & Rekomendasi', 'desc' => 'Lihat skor akhir dan predikat tiap peserta, terurut dari yang tertinggi.', 'status' => 'soon'],
                ['icon' => 'ti-shield-lock', 'title' => 'Peran Pengguna', 'desc' => 'Admin mengatur kriteria dan bobot; mentor cukup mengisi nilai peserta yang dibimbingnya.', 'status' => 'ready'],
            ];
            @endphp

            @foreach($features as $f)
            <div class="bg-white rounded-2xl border border-[#E8D8F0] p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#EDE8F5] flex items-center justify-center">
                        <i class="ti {{ $f['icon'] }} text-[#2B124C] text-lg"></i>
                    </div>
                    @if($f['status'] === 'ready')
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-[#E8F5ED] text-[#2D6A4A]">Siap dipakai</span>
                    @else
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">Segera hadir</span>
                    @endif
                </div>
                <h3 class="font-semibold text-[15px] text-[#190019] mb-1.5">{{ $f['title'] }}</h3>
                <p class="text-[13px] text-[#522B5B]/75 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach

        </div>
    </section>

    {{-- ============ CTA PENUTUP ============ --}}
    <section class="max-w-6xl mx-auto px-5 lg:px-8 pb-20 lg:pb-28">
        <div class="bg-[#2B124C] rounded-3xl px-6 lg:px-14 py-12 lg:py-16 text-center relative overflow-hidden grain">
            <div class="absolute inset-0 beam-glow pointer-events-none"></div>
            <div class="relative">
                <h2 class="font-display text-[#FBE4D8] text-[26px] lg:text-[34px] font-medium leading-tight mb-4">
                    Siap mulai menilai dengan lebih objektif?
                </h2>
                <p class="text-[#DFB6B2]/85 text-[15px] max-w-md mx-auto mb-7">
                    Masuk dengan akun Anda untuk mulai menyusun kriteria, atau menilai peserta yang Anda bimbing.
                </p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-[#FBE4D8] text-[#190019] text-[14px] font-semibold px-6 py-3.5 rounded-xl hover:bg-white transition-colors">
                    Masuk ke Aplikasi
                    <i class="ti ti-arrow-right text-base"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="border-t border-[#E8D8F0] py-7">
        <div class="max-w-6xl mx-auto px-5 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-[12.5px] text-[#522B5B]/60">© {{ date('Y') }} Kinerja — Sistem Rekomendasi Predikat Kinerja Peserta Magang.</p>
            <p class="text-[12.5px] text-[#522B5B]/60">Dibangun dengan metode Fuzzy AHP & Skala Saaty.</p>
        </div>
    </footer>

</div>

<script>
    function landingPage() {
        return {
            criteria: ['Kedisiplinan', 'Kualitas Kerja', 'Komunikasi'],
            pairs: [
                { a: 'Kedisiplinan', b: 'Kualitas Kerja' },
                { a: 'Kedisiplinan', b: 'Komunikasi' },
                { a: 'Kualitas Kerja', b: 'Komunikasi' },
            ],
            current: 0,
            answers: [0, 0, 0],

            sliderValue(i) {
                return this.answers[i];
            },

            describeAnswer(i) {
                const v = this.answers[i];
                const p = this.pairs[i];
                const labels = {
                    0: 'Menurut Anda, keduanya sama penting.',
                    1: `"${p.a}" sedikit lebih penting dari "${p.b}".`,
                    2: `"${p.a}" cukup lebih penting dari "${p.b}".`,
                    3: `"${p.a}" jauh lebih penting dari "${p.b}".`,
                    4: `"${p.a}" mutlak lebih penting dibanding "${p.b}".`,
                    [-1]: `"${p.b}" sedikit lebih penting dari "${p.a}".`,
                    [-2]: `"${p.b}" cukup lebih penting dari "${p.a}".`,
                    [-3]: `"${p.b}" jauh lebih penting dari "${p.a}".`,
                    [-4]: `"${p.b}" mutlak lebih penting dibanding "${p.a}".`,
                };
                return labels[v];
            },

            get weights() {
                // Bobot diturunkan dari jawaban slider lewat pendekatan rata-rata baris
                // sederhana (bukan eigenvector penuh) — cukup untuk demo ilustratif.
                const toSaaty = (v) => {
                    if (v === 0) return 1;
                    const scale = [1, 1, 2, 4, 6][Math.abs(v)] || (1 + Math.abs(v) * 2);
                    return v > 0 ? scale : 1 / scale;
                };

                const n = this.criteria.length;
                const m = Array.from({ length: n }, () => Array(n).fill(1));

                this.pairs.forEach((p, i) => {
                    const ai = this.criteria.indexOf(p.a);
                    const bi = this.criteria.indexOf(p.b);
                    const val = toSaaty(this.answers[i]);
                    m[ai][bi] = val;
                    m[bi][ai] = 1 / val;
                });

                const rowProducts = m.map(row => row.reduce((acc, x) => acc * x, 1));
                const rowGeoMean = rowProducts.map(p => Math.pow(p, 1 / n));
                const total = rowGeoMean.reduce((a, b) => a + b, 0);
                const norm = rowGeoMean.map(x => (x / total) * 100);

                const result = {};
                this.criteria.forEach((c, i) => result[c] = Math.round(norm[i]));
                return result;
            },
        };
    }
</script>

</body>
</html>