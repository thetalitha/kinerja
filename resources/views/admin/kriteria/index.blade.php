@extends('layouts.admin')

@section('title', 'Kriteria Penilaian')
@section('page-title', 'Kriteria Penilaian')

@section('content')
<div x-data="kriteriaPage()"
     x-init="@if(session('open_sub_modal')) $nextTick(() => openTambahSub({{ session('open_sub_modal') }}, '{{ addslashes(session('open_sub_nama')) }}')) @endif">

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2000)"
        x-show="show"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-4 p-4 bg-[#E8F5ED] border border-[#2D6A4A]/20 text-[#2D6A4A] rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-gray-500">
            Total <span class="font-medium text-gray-700">{{ $kriteria->count() }}</span> kriteria utama
        </p>
        <button @click="openTambah()"
                class="bg-[#2B124C] text-[#FBE4D8] text-sm font-medium px-4 py-2 rounded-xl hover:bg-[#522B5B] transition-colors">
            + Tambah Kriteria
        </button>
    </div>

    {{-- List Kriteria --}}
    <div class="space-y-3">
        @forelse($kriteria as $k)
        <div class="bg-white rounded-xl border border-[#E8D8F0] overflow-hidden" x-data="{ open: true }">

            {{-- Kriteria Utama --}}
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="w-8 h-8 rounded-lg bg-[#EDE8F5] flex items-center justify-center text-[#2B124C] font-semibold text-sm shrink-0">
                    {{ $loop->iteration }}
                </div>

                @if($k->has_sub_kriteria && $k->sub_kriteria->count())
                <button @click="open = !open" class="text-gray-400 hover:text-[#522B5B] transition-colors shrink-0">
                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800">{{ $k->nama }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        @if($k->has_sub_kriteria)
                            {{ $k->sub_kriteria->count() }} sub kriteria
                        @elseif($k->tipe_input === 'numerik')
                            Angka · {{ $k->input_min ?? '0' }} – {{ $k->input_max ?? '100' }}
                        @elseif($k->tipe_input === 'persentase')
                            Persentase · 0% – 100%
                        @elseif($k->tipe_input === 'linguistik')
                            Pilihan kata · {{ $k->skala->count() }} tingkatan
                        @else
                            Belum dikonfigurasi
                        @endif
                    </p>
                </div>

                {{-- Badge tipe --}}
                <div class="hidden md:flex items-center gap-1.5">
                    @if($k->has_sub_kriteria)
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-[#EDE8F5] text-[#522B5B]">Punya Sub Kriteria</span>
                    @elseif($k->tipe_input === 'numerik')
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-600">Angka</span>
                    @elseif($k->tipe_input === 'persentase')
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-purple-50 text-purple-600">Persentase</span>
                    @elseif($k->tipe_input === 'linguistik')
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-amber-50 text-amber-600">Pilihan Kata</span>
                    @endif

                    @if(in_array($k->tipe_input, ['numerik', 'persentase']) && $k->tipe_nilai)
                        @if($k->tipe_nilai === 'benefit')
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-green-50 text-green-600">📈 Tinggi = Baik</span>
                        @else
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-red-50 text-red-500">📉 Rendah = Baik</span>
                        @endif
                    @endif
                </div>

                {{-- Skala linguistik --}}
                @if(!$k->has_sub_kriteria && $k->tipe_input === 'linguistik' && $k->skala->count())
                <div class="hidden lg:flex items-center gap-1">
                    @foreach($k->skala as $s)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F7F8FA] border border-[#E8D8F0] text-gray-600">
                            {{ $s->urutan }}. {{ $s->label }}
                        </span>
                    @endforeach
                </div>
                @endif

                {{-- Aksi --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="openEdit({
                                id: {{ $k->id }},
                                nama: '{{ addslashes($k->nama) }}',
                                has_sub: {{ $k->has_sub_kriteria ? 'true' : 'false' }},
                                tipe_input: '{{ $k->tipe_input ?? '' }}',
                                tipe_nilai: '{{ $k->tipe_nilai ?? 'benefit' }}',
                                input_min: {{ $k->input_min ?? 0 }},
                                input_max: {{ $k->input_max ?? 100 }},
                                skala: {{ json_encode($k->skala->map(function($s) { return ['label' => $s->label]; })->values()) }}
                            })"
                            class="text-xs px-3 py-1.5 rounded-lg border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        Edit
                    </button>

                    @if($k->has_sub_kriteria)
                    <button @click="openTambahSub({{ $k->id }}, '{{ addslashes($k->nama) }}')"
                            class="text-xs px-3 py-1.5 rounded-lg border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        + Sub
                    </button>
                    @endif

                    <form action="{{ route('admin.kriteria.destroy', $k->id) }}" method="POST"
                          onsubmit="return confirm('Hapus kriteria {{ addslashes($k->nama) }}? Sub kriteria di dalamnya juga akan terhapus.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-500 hover:bg-red-50 transition-colors">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Sub Kriteria --}}
            @if($k->has_sub_kriteria && $k->sub_kriteria->count())
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="border-t border-[#F0E8F5] divide-y divide-[#F0E8F5]">
                @foreach($k->sub_kriteria as $sub)
                <div class="flex items-center gap-4 px-5 py-3 bg-[#FDFBFF]">
                    <div class="w-1 h-6 rounded-full bg-[#DFB6B2] shrink-0 ml-8"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700">{{ $sub->nama }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if($sub->tipe_input === 'numerik')
                                Angka · {{ $sub->input_min ?? '0' }} – {{ $sub->input_max ?? '100' }}
                            @elseif($sub->tipe_input === 'persentase')
                                Persentase · 0% – 100%
                            @elseif($sub->tipe_input === 'linguistik')
                                Pilihan kata · {{ $sub->skala->count() }} tingkatan
                            @else
                                Belum dikonfigurasi
                            @endif
                        </p>
                    </div>

                    {{-- Badge sub --}}
                    <div class="hidden md:flex items-center gap-1.5">
                        @if($sub->tipe_input === 'numerik')
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-blue-50 text-blue-600">Angka</span>
                        @elseif($sub->tipe_input === 'persentase')
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-purple-50 text-purple-600">Persentase</span>
                        @elseif($sub->tipe_input === 'linguistik')
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-amber-50 text-amber-600">Pilihan Kata</span>
                        @endif

                        @if(in_array($sub->tipe_input, ['numerik', 'persentase']) && $sub->tipe_nilai)
                            @if($sub->tipe_nilai === 'benefit')
                                <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-green-50 text-green-600">📈 Tinggi = Baik</span>
                            @else
                                <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-red-50 text-red-500">📉 Rendah = Baik</span>
                            @endif
                        @endif
                    </div>

                    @if($sub->tipe_input === 'linguistik' && $sub->skala->count())
                    <div class="hidden lg:flex items-center gap-1">
                        @foreach($sub->skala as $s)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F7F8FA] border border-[#E8D8F0] text-gray-600">
                                {{ $s->urutan }}. {{ $s->label }}
                            </span>
                        @endforeach
                    </div>
                    @endif

                    <div class="flex items-center gap-2 shrink-0">
                        <button @click="openEditSub({
                                    id: {{ $sub->id }},
                                    nama: '{{ addslashes($sub->nama) }}',
                                    tipe_input: '{{ $sub->tipe_input ?? '' }}',
                                    tipe_nilai: '{{ $sub->tipe_nilai ?? 'benefit' }}',
                                    input_min: {{ $sub->input_min ?? 0 }},
                                    input_max: {{ $sub->input_max ?? 100 }},
                                    skala: {{ json_encode($sub->skala->map(function($s) { return ['label' => $s->label]; })->values()) }}
                                })"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                            Edit
                        </button>

                        <form action="{{ route('admin.kriteria.sub.destroy', $sub->id) }}" method="POST"
                              onsubmit="return confirm('Hapus sub kriteria {{ addslashes($sub->nama) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-500 hover:bg-red-50 transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
        @empty
        <div class="bg-white rounded-xl border border-[#E8D8F0] px-5 py-16 text-center text-gray-400 text-sm">
            Belum ada kriteria. Klik "Tambah Kriteria" untuk mulai.
        </div>
        @endforelse
    </div>

    {{-- ── MODAL TAMBAH KRITERIA ── --}}
    <div x-show="modalTambah" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="modalTambah = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5]">
                <h3 class="font-semibold text-[#2B124C]">Tambah Kriteria</h3>
                <button @click="modalTambah = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.kriteria.store') }}" method="POST" class="px-6 py-5 space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Kriteria</label>
                    <input type="text" name="nama" placeholder="contoh: Kedisiplinan"
                           class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-2">Apakah kriteria ini punya sub kriteria?</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="hasSub === '1' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="has_sub_kriteria" value="1" x-model="hasSub" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Ya</p>
                                <p class="text-xs text-gray-400">Sub kriteria ditambah setelah ini</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="hasSub === '0' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="has_sub_kriteria" value="0" x-model="hasSub" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Tidak</p>
                                <p class="text-xs text-gray-400">Langsung atur cara penilaian</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="hasSub === '0'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana cara menilai kriteria ini?</label>
                        <div class="flex gap-2">
                            <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                   :class="tipeInput === 'numerik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                <input type="radio" name="tipe_input" value="numerik" x-model="tipeInput" class="sr-only">
                                <span class="text-lg">📊</span>
                                <p class="text-xs font-medium text-gray-700">Angka</p>
                                <p class="text-[10px] text-gray-400">Contoh: 85, 90</p>
                            </label>
                            <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                   :class="tipeInput === 'persentase' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                <input type="radio" name="tipe_input" value="persentase" x-model="tipeInput" class="sr-only">
                                <span class="text-lg">📐</span>
                                <p class="text-xs font-medium text-gray-700">Persentase</p>
                                <p class="text-[10px] text-gray-400">0% – 100%</p>
                            </label>
                            <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                   :class="tipeInput === 'linguistik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                <input type="radio" name="tipe_input" value="linguistik" x-model="tipeInput" class="sr-only">
                                <span class="text-lg">📝</span>
                                <p class="text-xs font-medium text-gray-700">Pilihan Kata</p>
                                <p class="text-[10px] text-gray-400">Baik, Sangat Baik</p>
                            </label>
                        </div>
                    </div>

                    <div x-show="tipeInput === 'numerik'" x-cloak class="flex gap-3">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Minimum</label>
                            <input type="number" name="input_min" value="0"
                                   class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Maksimum</label>
                            <input type="number" name="input_max" value="100"
                                   class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                        </div>
                    </div>

                    <div x-show="tipeInput === 'numerik' || tipeInput === 'persentase'" x-cloak>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana pengaruh nilai ini terhadap kinerja?</label>
                        <div class="flex gap-3">
                            <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                                   :class="tipeNilai === 'benefit' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                <input type="radio" name="tipe_nilai" value="benefit" x-model="tipeNilai" class="accent-[#522B5B]">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">📈 Semakin tinggi semakin baik</p>
                                    <p class="text-[11px] text-gray-400">contoh: kehadiran, nilai tugas</p>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                                   :class="tipeNilai === 'cost' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                <input type="radio" name="tipe_nilai" value="cost" x-model="tipeNilai" class="accent-[#522B5B]">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">📉 Semakin rendah semakin baik</p>
                                    <p class="text-[11px] text-gray-400">contoh: ketidakhadiran, pelanggaran</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="tipeInput === 'linguistik'" x-cloak>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-medium text-gray-500">Tingkatan (dari terendah ke tertinggi)</label>
                            <button type="button" @click="tambahSkala()" class="text-xs text-[#522B5B] hover:underline">+ Tambah</button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(skala, i) in skalaList" :key="i">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400 w-5 text-right shrink-0" x-text="i + 1 + '.'"></span>
                                    <input type="text" :name="'skala[' + i + '][label]'"
                                           x-model="skalaList[i].label" placeholder="contoh: Sangat Disiplin"
                                           class="flex-1 px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                    <button type="button" @click="hapusSkala(i)" x-show="skalaList.length > 2"
                                            class="text-red-400 hover:text-red-600 shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2">Sistem akan otomatis menentukan nilai fuzzy berdasarkan urutan tingkatan.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-[#F0E8F5]">
                    <button type="button" @click="modalTambah = false"
                            class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="text-sm px-4 py-2 rounded-xl bg-[#2B124C] text-[#FBE4D8] hover:bg-[#522B5B] transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL TAMBAH SUB KRITERIA ── --}}
    <div x-show="modalSub" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="modalSub = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5]">
                <div>
                    <h3 class="font-semibold text-[#2B124C]">Tambah Sub Kriteria</h3>
                    <p class="text-xs text-gray-400 mt-0.5">untuk: <span x-text="subParentNama" class="font-medium text-gray-600"></span></p>
                </div>
                <button @click="modalSub = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'/admin/kriteria/' + subParentId + '/sub'" method="POST" class="px-6 py-5 space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Sub Kriteria</label>
                    <input type="text" name="nama" placeholder="contoh: Ketepatan Waktu"
                           class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana cara menilai sub kriteria ini?</label>
                    <div class="flex gap-2">
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="subTipeInput === 'numerik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="numerik" x-model="subTipeInput" class="sr-only">
                            <span class="text-lg">📊</span>
                            <p class="text-xs font-medium text-gray-700">Angka</p>
                            <p class="text-[10px] text-gray-400">Contoh: 85, 90</p>
                        </label>
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="subTipeInput === 'persentase' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="persentase" x-model="subTipeInput" class="sr-only">
                            <span class="text-lg">📐</span>
                            <p class="text-xs font-medium text-gray-700">Persentase</p>
                            <p class="text-[10px] text-gray-400">0% – 100%</p>
                        </label>
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="subTipeInput === 'linguistik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="linguistik" x-model="subTipeInput" class="sr-only">
                            <span class="text-lg">📝</span>
                            <p class="text-xs font-medium text-gray-700">Pilihan Kata</p>
                            <p class="text-[10px] text-gray-400">Baik, Sangat Baik</p>
                        </label>
                    </div>
                </div>

                <div x-show="subTipeInput === 'numerik'" x-cloak class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Minimum</label>
                        <input type="number" name="input_min" value="0"
                               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Maksimum</label>
                        <input type="number" name="input_max" value="100"
                               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                    </div>
                </div>

                <div x-show="subTipeInput === 'numerik' || subTipeInput === 'persentase'" x-cloak>
                    <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana pengaruh nilai ini terhadap kinerja?</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="subTipeNilai === 'benefit' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_nilai" value="benefit" x-model="subTipeNilai" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">📈 Semakin tinggi semakin baik</p>
                                <p class="text-[11px] text-gray-400">contoh: kehadiran, nilai tugas</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="subTipeNilai === 'cost' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_nilai" value="cost" x-model="subTipeNilai" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">📉 Semakin rendah semakin baik</p>
                                <p class="text-[11px] text-gray-400">contoh: ketidakhadiran, pelanggaran</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="subTipeInput === 'linguistik'" x-cloak>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-gray-500">Tingkatan (dari terendah ke tertinggi)</label>
                        <button type="button" @click="tambahSubSkala()" class="text-xs text-[#522B5B] hover:underline">+ Tambah</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(skala, i) in subSkalaList" :key="i">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400 w-5 text-right shrink-0" x-text="i + 1 + '.'"></span>
                                <input type="text" :name="'skala[' + i + '][label]'"
                                       x-model="subSkalaList[i].label" placeholder="contoh: Sangat Baik"
                                       class="flex-1 px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                <button type="button" @click="hapusSubSkala(i)" x-show="subSkalaList.length > 2"
                                        class="text-red-400 hover:text-red-600 shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2">Sistem akan otomatis menentukan nilai fuzzy berdasarkan urutan tingkatan.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-[#F0E8F5]">
                    <button type="button" @click="modalSub = false"
                            class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        Selesai
                    </button>
                    <button type="submit"
                            class="text-sm px-4 py-2 rounded-xl bg-[#2B124C] text-[#FBE4D8] hover:bg-[#522B5B] transition-colors">
                        Simpan Sub
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL EDIT KRITERIA ── --}}
    <div x-show="modalEdit" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="modalEdit = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5]">
                <h3 class="font-semibold text-[#2B124C]">Edit Kriteria</h3>
                <button @click="modalEdit = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'/admin/kriteria/' + editData.id" method="POST" class="px-6 py-5 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Kriteria</label>
                    <input type="text" name="nama" x-model="editData.nama"
                           class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                </div>

                <template x-if="!editData.has_sub">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Cara penilaian</label>
                            <div class="flex gap-2">
                                <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                       :class="editData.tipe_input === 'numerik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                    <input type="radio" name="tipe_input" value="numerik" x-model="editData.tipe_input" class="sr-only">
                                    <span class="text-lg">📊</span>
                                    <p class="text-xs font-medium text-gray-700">Angka</p>
                                </label>
                                <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                       :class="editData.tipe_input === 'persentase' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                    <input type="radio" name="tipe_input" value="persentase" x-model="editData.tipe_input" class="sr-only">
                                    <span class="text-lg">📐</span>
                                    <p class="text-xs font-medium text-gray-700">Persentase</p>
                                </label>
                                <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                                       :class="editData.tipe_input === 'linguistik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                    <input type="radio" name="tipe_input" value="linguistik" x-model="editData.tipe_input" class="sr-only">
                                    <span class="text-lg">📝</span>
                                    <p class="text-xs font-medium text-gray-700">Pilihan Kata</p>
                                </label>
                            </div>
                        </div>

                        <div x-show="editData.tipe_input === 'numerik'" class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Minimum</label>
                                <input type="number" name="input_min" x-model="editData.input_min"
                                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Maksimum</label>
                                <input type="number" name="input_max" x-model="editData.input_max"
                                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                            </div>
                        </div>

                        <div x-show="editData.tipe_input === 'numerik' || editData.tipe_input === 'persentase'">
                            <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana pengaruh nilai ini terhadap kinerja?</label>
                            <div class="flex gap-3">
                                <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                                       :class="editData.tipe_nilai === 'benefit' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                    <input type="radio" name="tipe_nilai" value="benefit" x-model="editData.tipe_nilai" class="accent-[#522B5B]">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">📈 Semakin tinggi semakin baik</p>
                                        <p class="text-[11px] text-gray-400">contoh: kehadiran, nilai tugas</p>
                                    </div>
                                </label>
                                <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                                       :class="editData.tipe_nilai === 'cost' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                                    <input type="radio" name="tipe_nilai" value="cost" x-model="editData.tipe_nilai" class="accent-[#522B5B]">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">📉 Semakin rendah semakin baik</p>
                                        <p class="text-[11px] text-gray-400">contoh: ketidakhadiran, pelanggaran</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div x-show="editData.tipe_input === 'linguistik'">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-medium text-gray-500">Tingkatan</label>
                                <button type="button" @click="editData.skala.push({ label: '' })"
                                        class="text-xs text-[#522B5B] hover:underline">+ Tambah</button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(s, i) in editData.skala" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400 w-5 text-right shrink-0" x-text="i + 1 + '.'"></span>
                                        <input type="text" :name="'skala[' + i + '][label]'"
                                               x-model="editData.skala[i].label"
                                               class="flex-1 px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                        <button type="button" @click="editData.skala.splice(i, 1)"
                                                x-show="editData.skala.length > 2" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2">Sistem akan otomatis menentukan nilai fuzzy berdasarkan urutan tingkatan.</p>
                        </div>
                    </div>
                </template>

                <div class="flex justify-end gap-3 pt-2 border-t border-[#F0E8F5]">
                    <button type="button" @click="modalEdit = false"
                            class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="text-sm px-4 py-2 rounded-xl bg-[#2B124C] text-[#FBE4D8] hover:bg-[#522B5B] transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL EDIT SUB KRITERIA ── --}}
    <div x-show="modalEditSub" x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click.outside="modalEditSub = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5]">
                <h3 class="font-semibold text-[#2B124C]">Edit Sub Kriteria</h3>
                <button @click="modalEditSub = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'/admin/kriteria/sub/' + editSubData.id" method="POST" class="px-6 py-5 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Sub Kriteria</label>
                    <input type="text" name="nama" x-model="editSubData.nama"
                           class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-2">Cara penilaian</label>
                    <div class="flex gap-2">
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="editSubData.tipe_input === 'numerik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="numerik" x-model="editSubData.tipe_input" class="sr-only">
                            <span class="text-lg">📊</span>
                            <p class="text-xs font-medium text-gray-700">Angka</p>
                        </label>
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="editSubData.tipe_input === 'persentase' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="persentase" x-model="editSubData.tipe_input" class="sr-only">
                            <span class="text-lg">📐</span>
                            <p class="text-xs font-medium text-gray-700">Persentase</p>
                        </label>
                        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
                               :class="editSubData.tipe_input === 'linguistik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_input" value="linguistik" x-model="editSubData.tipe_input" class="sr-only">
                            <span class="text-lg">📝</span>
                            <p class="text-xs font-medium text-gray-700">Pilihan Kata</p>
                        </label>
                    </div>
                </div>

                <div x-show="editSubData.tipe_input === 'numerik'" class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Minimum</label>
                        <input type="number" name="input_min" x-model="editSubData.input_min"
                               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Maksimum</label>
                        <input type="number" name="input_max" x-model="editSubData.input_max"
                               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                    </div>
                </div>

                <div x-show="editSubData.tipe_input === 'numerik' || editSubData.tipe_input === 'persentase'">
                    <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana pengaruh nilai ini terhadap kinerja?</label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="editSubData.tipe_nilai === 'benefit' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_nilai" value="benefit" x-model="editSubData.tipe_nilai" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">📈 Semakin tinggi semakin baik</p>
                                <p class="text-[11px] text-gray-400">contoh: kehadiran, nilai tugas</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
                               :class="editSubData.tipe_nilai === 'cost' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
                            <input type="radio" name="tipe_nilai" value="cost" x-model="editSubData.tipe_nilai" class="accent-[#522B5B]">
                            <div>
                                <p class="text-sm font-medium text-gray-700">📉 Semakin rendah semakin baik</p>
                                <p class="text-[11px] text-gray-400">contoh: ketidakhadiran, pelanggaran</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="editSubData.tipe_input === 'linguistik'">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-gray-500">Tingkatan</label>
                        <button type="button" @click="editSubData.skala.push({ label: '' })"
                                class="text-xs text-[#522B5B] hover:underline">+ Tambah</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(s, i) in editSubData.skala" :key="i">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400 w-5 text-right shrink-0" x-text="i + 1 + '.'"></span>
                                <input type="text" :name="'skala[' + i + '][label]'"
                                       x-model="editSubData.skala[i].label"
                                       class="flex-1 px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                <button type="button" @click="editSubData.skala.splice(i, 1)"
                                        x-show="editSubData.skala.length > 2" class="text-red-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2">Sistem akan otomatis menentukan nilai fuzzy berdasarkan urutan tingkatan.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-[#F0E8F5]">
                    <button type="button" @click="modalEditSub = false"
                            class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="text-sm px-4 py-2 rounded-xl bg-[#2B124C] text-[#FBE4D8] hover:bg-[#522B5B] transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function kriteriaPage() {
    return {
        modalTambah: false,
        modalSub: false,
        modalEdit: false,
        modalEditSub: false,
        hasSub: null,
        tipeInput: null,
        tipeNilai: 'benefit',
        skalaList: [{ label: '' }, { label: '' }],
        subParentId: null,
        subParentNama: '',
        subTipeInput: null,
        subTipeNilai: 'benefit',
        subSkalaList: [{ label: '' }, { label: '' }],
        editData: { id: null, nama: '', has_sub: false, tipe_input: '', tipe_nilai: 'benefit', input_min: 0, input_max: 100, skala: [] },
        editSubData: { id: null, nama: '', tipe_input: '', tipe_nilai: 'benefit', input_min: 0, input_max: 100, skala: [] },

        openTambah() {
            this.hasSub = null;
            this.tipeInput = null;
            this.tipeNilai = 'benefit';
            this.skalaList = [{ label: '' }, { label: '' }];
            this.modalTambah = true;
        },

        openTambahSub(id, nama) {
            this.subParentId = id;
            this.subParentNama = nama;
            this.subTipeInput = null;
            this.subTipeNilai = 'benefit';
            this.subSkalaList = [{ label: '' }, { label: '' }];
            this.modalSub = true;
        },

        openEdit(data) {
            this.editData = {
                ...data,
                tipe_nilai: data.tipe_nilai || 'benefit',
                skala: data.skala && data.skala.length ? data.skala : [{ label: '' }, { label: '' }]
            };
            this.modalEdit = true;
        },

        openEditSub(data) {
            this.editSubData = {
                ...data,
                tipe_nilai: data.tipe_nilai || 'benefit',
                skala: data.skala && data.skala.length ? data.skala : [{ label: '' }, { label: '' }]
            };
            this.modalEditSub = true;
        },

        tambahSkala() { this.skalaList.push({ label: '' }); },
        hapusSkala(i) { this.skalaList.splice(i, 1); },
        tambahSubSkala() { this.subSkalaList.push({ label: '' }); },
        hapusSubSkala(i) { this.subSkalaList.splice(i, 1); },
    }
}
</script>
@endpush