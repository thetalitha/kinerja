@extends('layouts.admin')

@section('title', 'Kriteria Penilaian')
@section('page-title', 'Kriteria Penilaian')

@section('content')
<div x-data="kriteriaPage()"
     x-init="@if(session('open_sub_modal')) $nextTick(() => openTambahSub({{ session('open_sub_modal') }}, '{{ addslashes(session('open_sub_nama')) }}')) @endif">

    @if($errors->any())
    <div x-data="{ show: true }"
        x-show="show"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 relative">
        <button @click="show = false" class="absolute top-3 right-3 text-red-400 hover:text-red-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <ul class="list-disc list-inside space-y-1 pr-6">
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
        <i class="ti ti-circle-check text-base shrink-0"></i>
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
                    @if($k->definisi)
                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $k->definisi }}</p>
                    @endif
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
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F7F8FA] border border-[#E8D8F0] text-gray-600"
                              title="{{ $s->definisi }}">
                            {{ $s->urutan }}. {{ $s->label }}
                        </span>
                    @endforeach
                </div>
                @endif

                {{-- Aksi --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="openDetail({
                                nama: '{{ addslashes($k->nama) }}',
                                definisi: '{{ addslashes($k->definisi ?? '') }}',
                                has_sub: {{ $k->has_sub_kriteria ? 'true' : 'false' }},
                                tipe_input: '{{ $k->tipe_input ?? '' }}',
                                tipe_nilai: '{{ $k->tipe_nilai ?? 'benefit' }}',
                                input_min: {{ $k->input_min ?? 0 }},
                                input_max: {{ $k->input_max ?? 100 }},
                                skala: {{ json_encode($k->skala->map(function($s) { return ['label' => $s->label, 'definisi' => $s->definisi]; })->values()) }},
                                sub_kriteria: {{ json_encode($k->sub_kriteria->map(function($sub) { return [
                                    'nama' => $sub->nama,
                                    'tipe_input' => $sub->tipe_input,
                                    'tipe_nilai' => $sub->tipe_nilai,
                                    'input_min' => $sub->input_min,
                                    'input_max' => $sub->input_max,
                                    'skala' => $sub->skala->map(function($s) { return ['label' => $s->label, 'definisi' => $s->definisi]; })->values(),
                                ]; })->values()) }}
                            })"
                            class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-500 hover:bg-gray-100 hover:border-gray-400 hover:text-gray-700 transition-colors">
                        Detail
                    </button>

                    <button @click="openEdit({
                                id: {{ $k->id }},
                                nama: '{{ addslashes($k->nama) }}',
                                definisi: '{{ addslashes($k->definisi ?? '') }}',
                                has_sub: {{ $k->has_sub_kriteria ? 'true' : 'false' }},
                                tipe_input: '{{ $k->tipe_input ?? '' }}',
                                tipe_nilai: '{{ $k->tipe_nilai ?? 'benefit' }}',
                                input_min: {{ $k->input_min ?? 0 }},
                                input_max: {{ $k->input_max ?? 100 }},
                                skala: {{ json_encode($k->skala->map(function($s) { return ['label' => $s->label, 'definisi' => $s->definisi]; })->values()) }}
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
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F7F8FA] border border-[#E8D8F0] text-gray-600"
                                  title="{{ $s->definisi }}">
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
                                    skala: {{ json_encode($sub->skala->map(function($s) { return ['label' => $s->label, 'definisi' => $s->definisi]; })->values()) }}
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
    <x-modal show="modalTambah" title="Tambah Kriteria">
        <form action="{{ route('admin.kriteria.store') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Kriteria</label>
                <input type="text" name="nama" placeholder="contoh: Kedisiplinan"
                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Deskripsi Kriteria</label>
                <textarea name="definisi" rows="2" placeholder="contoh: Kedisiplinan peserta terhadap waktu masuk/pulang, penggunaan atribut, dan menaati perintah pembimbing"
                          class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B] resize-none"></textarea>
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
                <x-kriteria.tipe-input-section
                    heading="Bagaimana cara menilai kriteria ini?"
                    tipeInputVar="tipeInput"
                    tipeNilaiVar="tipeNilai"
                    inputMinVar="inputMin"
                    inputMaxVar="inputMax"
                    skalaVar="skalaList"
                    addFn="tambahSkala()"
                    removeFn="hapusSkala(i)"
                    removeCond="skalaList.length > 2"
                />
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
    </x-modal>

    {{-- ── MODAL TAMBAH SUB KRITERIA ── --}}
    <x-modal show="modalSub" title="Tambah Sub Kriteria">
        <x-slot:subtitle>untuk: <span x-text="subParentNama" class="font-medium text-gray-600"></span></x-slot:subtitle>

        <form :action="'/admin/kriteria/' + subParentId + '/sub'" method="POST" class="px-6 py-5 space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Sub Kriteria</label>
                <input type="text" name="nama" placeholder="contoh: Ketepatan Waktu"
                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
            </div>

            <x-kriteria.tipe-input-section
                heading="Bagaimana cara menilai sub kriteria ini?"
                tipeInputVar="subTipeInput"
                tipeNilaiVar="subTipeNilai"
                inputMinVar="subInputMin"
                inputMaxVar="subInputMax"
                skalaVar="subSkalaList"
                addFn="tambahSubSkala()"
                removeFn="hapusSubSkala(i)"
                removeCond="subSkalaList.length > 2"
            />

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
    </x-modal>

    {{-- ── MODAL EDIT KRITERIA ── --}}
    <x-modal show="modalEdit" title="Edit Kriteria">
        <form :action="'/admin/kriteria/' + editData.id" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Kriteria</label>
                <input type="text" name="nama" x-model="editData.nama"
                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Definisi Kriteria</label>
                <textarea name="definisi" rows="2" x-model="editData.definisi"
                          class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B] resize-none"></textarea>
            </div>

            <template x-if="!editData.has_sub">
                <div class="space-y-4">
                    <x-kriteria.tipe-input-section
                        heading="Cara penilaian"
                        tipeInputVar="editData.tipe_input"
                        tipeNilaiVar="editData.tipe_nilai"
                        inputMinVar="editData.input_min"
                        inputMaxVar="editData.input_max"
                        skalaVar="editData.skala"
                        addFn="editData.skala.push({ label: '', definisi: '' })"
                        removeFn="editData.skala.splice(i, 1)"
                        removeCond="editData.skala.length > 2"
                    />
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
    </x-modal>

    {{-- ── MODAL EDIT SUB KRITERIA ── --}}
    <x-modal show="modalEditSub" title="Edit Sub Kriteria">
        <form :action="'/admin/kriteria/sub/' + editSubData.id" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Nama Sub Kriteria</label>
                <input type="text" name="nama" x-model="editSubData.nama"
                       class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
            </div>

            <x-kriteria.tipe-input-section
                heading="Cara penilaian"
                tipeInputVar="editSubData.tipe_input"
                tipeNilaiVar="editSubData.tipe_nilai"
                inputMinVar="editSubData.input_min"
                inputMaxVar="editSubData.input_max"
                skalaVar="editSubData.skala"
                addFn="editSubData.skala.push({ label: '', definisi: '' })"
                removeFn="editSubData.skala.splice(i, 1)"
                removeCond="editSubData.skala.length > 2"
            />

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
    </x-modal>

    {{-- ── MODAL DETAIL KRITERIA ── --}}
    <x-modal show="modalDetail" title="Detail Kriteria" maxWidth="max-w-lg">
        <div class="px-6 py-5 space-y-5 text-sm max-h-[65vh] overflow-y-auto">
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Nama Kriteria</p>
                <p class="font-semibold text-gray-800" x-text="detailData.nama"></p>
            </div>

            <div x-show="detailData.definisi">
                <p class="text-xs font-medium text-gray-400 mb-1">Deskripsi</p>
                <p class="text-gray-600 whitespace-pre-line" x-text="detailData.definisi"></p>
            </div>

            {{-- Kriteria tanpa sub --}}
            <template x-if="!detailData.has_sub">
                <div class="space-y-3 border-t border-[#F0E8F5] pt-4">
                    <p class="text-xs font-medium text-gray-400">Cara Penilaian</p>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-[#EDE8F5] text-[#522B5B]"
                              x-text="detailData.tipe_input === 'numerik' ? 'Angka' : detailData.tipe_input === 'persentase' ? 'Persentase' : detailData.tipe_input === 'linguistik' ? 'Pilihan Kata' : 'Belum diatur'"></span>
                        <template x-if="detailData.tipe_input === 'numerik' || detailData.tipe_input === 'persentase'">
                            <span class="text-[10px] font-medium px-2 py-1 rounded-full"
                                  :class="detailData.tipe_nilai === 'benefit' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500'"
                                  x-text="detailData.tipe_nilai === 'benefit' ? '📈 Semakin tinggi semakin baik' : '📉 Semakin rendah semakin baik'"></span>
                        </template>
                    </div>

                    <template x-if="detailData.tipe_input === 'numerik'">
                        <p class="text-xs text-gray-500">Rentang nilai: <span class="font-medium text-gray-700" x-text="detailData.input_min + ' – ' + detailData.input_max"></span></p>
                    </template>

                    <template x-if="detailData.tipe_input === 'linguistik'">
                        <div class="space-y-2">
                            <p class="text-xs text-gray-500">Tingkatan (<span x-text="detailData.skala.length"></span>)</p>
                            <template x-for="(s, i) in detailData.skala" :key="i">
                                <div class="border border-[#E8D8F0] rounded-lg px-3 py-2">
                                    <p class="text-sm font-medium text-gray-700" x-text="(i + 1) + '. ' + s.label"></p>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="s.definisi || '—'"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Kriteria dengan sub --}}
            <template x-if="detailData.has_sub">
                <div class="space-y-4 border-t border-[#F0E8F5] pt-4">
                    <p class="text-xs font-medium text-gray-400">Sub Kriteria (<span x-text="detailData.sub_kriteria.length"></span>)</p>
                    <template x-for="(sub, si) in detailData.sub_kriteria" :key="si">
                        <div class="border border-[#E8D8F0] rounded-xl p-4 space-y-2">
                            <p class="text-sm font-semibold text-gray-800" x-text="(si + 1) + '. ' + sub.nama"></p>

                            <div class="flex flex-wrap gap-1.5">
                                <span class="text-[10px] font-medium px-2 py-1 rounded-full bg-[#EDE8F5] text-[#522B5B]"
                                      x-text="sub.tipe_input === 'numerik' ? 'Angka' : sub.tipe_input === 'persentase' ? 'Persentase' : sub.tipe_input === 'linguistik' ? 'Pilihan Kata' : 'Belum diatur'"></span>
                                <template x-if="sub.tipe_input === 'numerik' || sub.tipe_input === 'persentase'">
                                    <span class="text-[10px] font-medium px-2 py-1 rounded-full"
                                          :class="sub.tipe_nilai === 'benefit' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500'"
                                          x-text="sub.tipe_nilai === 'benefit' ? '📈 Tinggi = Baik' : '📉 Rendah = Baik'"></span>
                                </template>
                            </div>

                            <template x-if="sub.tipe_input === 'numerik'">
                                <p class="text-xs text-gray-500">Rentang nilai: <span class="font-medium text-gray-700" x-text="sub.input_min + ' – ' + sub.input_max"></span></p>
                            </template>

                            <template x-if="sub.tipe_input === 'linguistik'">
                                <div class="space-y-1.5 mt-1">
                                    <template x-for="(s, i) in sub.skala" :key="i">
                                        <div class="bg-[#FDFBFF] border border-[#E8D8F0] rounded-lg px-3 py-1.5">
                                            <p class="text-xs font-medium text-gray-700" x-text="(i + 1) + '. ' + s.label"></p>
                                            <p class="text-[11px] text-gray-400 mt-0.5" x-text="s.definisi || '—'"></p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 border-t border-[#F0E8F5]">
            <button type="button" @click="modalDetail = false"
                    class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                Tutup
            </button>
        </div>
    </x-modal>

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
        modalDetail: false,
        hasSub: null,
        tipeInput: null,
        tipeNilai: 'benefit',
        inputMin: 0,
        inputMax: 100,
        skalaList: [{ label: '', definisi: '' }, { label: '', definisi: '' }],
        subParentId: null,
        subParentNama: '',
        subTipeInput: null,
        subTipeNilai: 'benefit',
        subInputMin: 0,
        subInputMax: 100,
        subSkalaList: [{ label: '', definisi: '' }, { label: '', definisi: '' }],
        editData: { id: null, nama: '', definisi: '', has_sub: false, tipe_input: '', tipe_nilai: 'benefit', input_min: 0, input_max: 100, skala: [] },
        editSubData: { id: null, nama: '', tipe_input: '', tipe_nilai: 'benefit', input_min: 0, input_max: 100, skala: [] },
        detailData: { nama: '', definisi: '', has_sub: false, tipe_input: '', tipe_nilai: 'benefit', input_min: 0, input_max: 100, skala: [], sub_kriteria: [] },

        openTambah() {
            this.hasSub = null;
            this.tipeInput = null;
            this.tipeNilai = 'benefit';
            this.inputMin = 0;
            this.inputMax = 100;
            this.skalaList = [{ label: '', definisi: '' }, { label: '', definisi: '' }];
            this.modalTambah = true;
        },

        openTambahSub(id, nama) {
            this.subParentId = id;
            this.subParentNama = nama;
            this.subTipeInput = null;
            this.subTipeNilai = 'benefit';
            this.subInputMin = 0;
            this.subInputMax = 100;
            this.subSkalaList = [{ label: '', definisi: '' }, { label: '', definisi: '' }];
            this.modalSub = true;
        },

        openEdit(data) {
            this.editData = {
                ...data,
                definisi: data.definisi || '',
                tipe_nilai: data.tipe_nilai || 'benefit',
                skala: data.skala && data.skala.length ? data.skala : [{ label: '', definisi: '' }, { label: '', definisi: '' }]
            };
            this.modalEdit = true;
        },

        openDetail(data) {
            this.detailData = data;
            this.modalDetail = true;
        },

        openEditSub(data) {
            this.editSubData = {
                ...data,
                tipe_nilai: data.tipe_nilai || 'benefit',
                skala: data.skala && data.skala.length ? data.skala : [{ label: '', definisi: '' }, { label: '', definisi: '' }]
            };
            this.modalEditSub = true;
        },

        tambahSkala() { this.skalaList.push({ label: '', definisi: '' }); },
        hapusSkala(i) { this.skalaList.splice(i, 1); },
        tambahSubSkala() { this.subSkalaList.push({ label: '', definisi: '' }); },
        hapusSubSkala(i) { this.subSkalaList.splice(i, 1); },
    }
}
</script>
@endpush