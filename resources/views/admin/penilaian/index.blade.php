@extends('layouts.admin')

@section('title', 'Penilaian Peserta')
@section('page-title', 'Penilaian Peserta')

@section('content')
<div x-data="penilaianPage()">

    @if(session('success'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-4 p-4 bg-[#E8F5ED] border border-[#2D6A4A]/20 text-[#2D6A4A] rounded-xl text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-sm text-gray-500">
                Total <span class="font-medium text-gray-700">{{ $peserta->count() }}</span> peserta ·
                <span class="text-green-600 font-medium">{{ $peserta->where('sudah_dinilai', true)->count() }}</span> sudah dinilai ·
                <span class="text-amber-600 font-medium">{{ $peserta->where('sudah_dinilai', false)->count() }}</span> belum dinilai
            </p>
        </div>

        @if($totalKriteria === 0)
        <span class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
            ⚠ Belum ada kriteria — tambah kriteria dulu
        </span>
        @endif
    </div>

    {{-- Tabel Peserta --}}
    <div class="bg-white rounded-xl border border-[#E8D8F0] overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-[#2B124C] text-[#FBE4D8]">
                    <th class="px-4 py-3 text-left font-medium text-xs">Peserta</th>
                    <th class="px-4 py-3 text-left font-medium text-xs hidden lg:table-cell">Institut</th>
                    <th class="px-4 py-3 text-left font-medium text-xs hidden lg:table-cell">Fungsi</th>
                    <th class="px-4 py-3 text-center font-medium text-xs">Progress Penilaian</th>
                    <th class="px-4 py-3 text-center font-medium text-xs">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F0E8F5]">
                @forelse($peserta as $p)
                <tr class="hover:bg-[#F5F0FF] transition-colors duration-100">

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#2B124C] flex items-center justify-center text-[#FBE4D8] text-xs font-semibold shrink-0">
                                {{ strtoupper(substr($p->nama, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $p->nama }}</p>
                                <p class="text-gray-400 text-xs">{{ $p->nim ?? '-' }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-gray-600 text-xs hidden lg:table-cell">{{ $p->institut ?? '-' }}</td>

                    <td class="px-4 py-3 hidden lg:table-cell">
                        @if($p->fungsi)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#EDE8F5] text-[#522B5B]">{{ $p->fungsi }}</span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-full bg-[#F0E8F5] rounded-full h-1.5 max-w-[120px]">
                                @php $pct = $totalKriteria > 0 ? round(($p->jumlah_dinilai / $totalKriteria) * 100) : 0; @endphp
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $p->sudah_dinilai ? 'bg-green-500' : 'bg-[#2B124C]' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $p->jumlah_dinilai }}/{{ $totalKriteria }}</span>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($p->sudah_dinilai)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-600 border border-green-200">Sudah Dinilai</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-200">Belum Dinilai</span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-right">
                        <button @click="openPenilaian({{ $p->id }}, '{{ addslashes($p->nama) }}')"
                                class="text-xs px-3 py-1.5 rounded-lg border transition-all duration-150
                                {{ $p->sudah_dinilai
                                    ? 'border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5]'
                                    : 'bg-[#2B124C] text-[#FBE4D8] border-[#2B124C] hover:bg-[#522B5B]' }}">
                            {{ $p->sudah_dinilai ? 'Edit Penilaian' : 'Isi Penilaian' }}
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">Belum ada data peserta.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── MODAL PENILAIAN ── --}}
    <div x-show="modalOpen"
         x-transition.opacity
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         x-cloak>
        <div @click.outside="modalOpen = false"
             class="bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] flex flex-col">

            <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5] shrink-0">
                <div>
                    <h3 class="font-semibold text-[#2B124C]">Isi Penilaian</h3>
                    <p class="text-xs text-gray-400 mt-0.5">untuk: <span x-text="namaPeserta" class="font-medium text-gray-600"></span></p>
                </div>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div x-show="loading" class="flex items-center justify-center py-16">
                <svg class="animate-spin w-6 h-6 text-[#522B5B]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>

            <div x-show="!loading" class="overflow-y-auto flex-1">
                <form id="formPenilaian" action="{{ route('admin.penilaian.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" :value="userId">

                    <div class="px-6 py-4 space-y-5">
                        @forelse($kriteria as $k)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                {{ $k->nama }}
                            </p>

                            @if($k->has_sub_kriteria)
                            <div class="space-y-3 pl-3 border-l-2 border-[#EDE8F5]">
                                @foreach($k->subKriteria as $sub)
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                        {{ $sub->nama }}
                                        @if($sub->tipe_input === 'numerik')
                                            <span class="text-gray-400 font-normal">({{ $sub->input_min ?? 0 }} – {{ $sub->input_max ?? 100 }})</span>
                                        @elseif($sub->tipe_input === 'persentase')
                                            <span class="text-gray-400 font-normal">(0% – 100%)</span>
                                        @endif
                                        @if(in_array($sub->tipe_input, ['numerik', 'persentase']) && $sub->tipe_nilai === 'cost')
                                            <span class="text-red-400 font-normal text-[10px]">· semakin rendah semakin baik</span>
                                        @endif
                                    </label>

                                    @if($sub->tipe_input === 'numerik')
                                    <input type="number"
                                           name="penilaian[{{ $sub->id }}]"
                                           :value="nilaiExisting[{{ $sub->id }}]?.nilai_numerik ?? ''"
                                           min="{{ $sub->input_min ?? 0 }}"
                                           max="{{ $sub->input_max ?? 100 }}"
                                           step="0.01"
                                           placeholder="Masukkan nilai..."
                                           class="w-full px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">

                                    @elseif($sub->tipe_input === 'persentase')
                                    <div class="relative">
                                        <input type="number"
                                               name="penilaian[{{ $sub->id }}]"
                                               :value="nilaiExisting[{{ $sub->id }}]?.nilai_numerik ?? ''"
                                               min="0" max="100" step="0.01"
                                               placeholder="0 – 100"
                                               class="w-full px-3 py-2 pr-8 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                        <span class="absolute right-3 top-2 text-gray-400 text-sm">%</span>
                                    </div>

                                    @elseif($sub->tipe_input === 'linguistik')
                                    <div class="flex flex-wrap gap-2" x-data="{ pilihan_{{ $sub->id }}: nilaiExisting[{{ $sub->id }}]?.nilai_linguistik ?? '' }">
                                        @foreach($sub->skalaLinguistik as $skala)
                                        <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer transition-colors text-xs"
                                               :class="pilihan_{{ $sub->id }} === '{{ $skala->label }}'
                                                   ? 'border-[#522B5B] bg-[#EDE8F5] text-[#522B5B] font-medium'
                                                   : 'border-[#E8D8F0] text-gray-600 hover:border-[#522B5B]'">
                                            <input type="radio"
                                                   name="penilaian[{{ $sub->id }}]"
                                                   value="{{ $skala->label }}"
                                                   x-model="pilihan_{{ $sub->id }}"
                                                   class="sr-only">
                                            {{ $skala->label }}
                                        </label>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            @else
                            {{-- Kriteria tanpa sub kriteria --}}
                            @if($k->tipe_input === 'numerik')
                            <label class="block text-xs text-gray-400 mb-1.5">
                                Range: {{ $k->input_min ?? 0 }} – {{ $k->input_max ?? 100 }}
                                @if($k->tipe_nilai === 'cost')
                                    <span class="text-red-400">· semakin rendah semakin baik</span>
                                @endif
                            </label>
                            <input type="number"
                                   name="penilaian[{{ $k->id }}]"
                                   :value="nilaiExisting[{{ $k->id }}]?.nilai_numerik ?? ''"
                                   min="{{ $k->input_min ?? 0 }}"
                                   max="{{ $k->input_max ?? 100 }}"
                                   step="0.01"
                                   placeholder="Masukkan nilai..."
                                   class="w-full px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">

                            @elseif($k->tipe_input === 'persentase')
                            <label class="block text-xs text-gray-400 mb-1.5">
                                0% – 100%
                                @if($k->tipe_nilai === 'cost')
                                    <span class="text-red-400">· semakin rendah semakin baik</span>
                                @endif
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="penilaian[{{ $k->id }}]"
                                       :value="nilaiExisting[{{ $k->id }}]?.nilai_numerik ?? ''"
                                       min="0" max="100" step="0.01"
                                       placeholder="0 – 100"
                                       class="w-full px-3 py-2 pr-8 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                                <span class="absolute right-3 top-2 text-gray-400 text-sm">%</span>
                            </div>

                            @elseif($k->tipe_input === 'linguistik')
                            <div class="flex flex-wrap gap-2" x-data="{ pilihan_{{ $k->id }}: nilaiExisting[{{ $k->id }}]?.nilai_linguistik ?? '' }">
                                @foreach($k->skalaLinguistik as $skala)
                                <label class="flex items-center gap-1.5 px-3 py-1.5 border rounded-lg cursor-pointer transition-colors text-xs"
                                       :class="pilihan_{{ $k->id }} === '{{ $skala->label }}'
                                           ? 'border-[#522B5B] bg-[#EDE8F5] text-[#522B5B] font-medium'
                                           : 'border-[#E8D8F0] text-gray-600 hover:border-[#522B5B]'">
                                    <input type="radio"
                                           name="penilaian[{{ $k->id }}]"
                                           value="{{ $skala->label }}"
                                           x-model="pilihan_{{ $k->id }}"
                                           class="sr-only">
                                    {{ $skala->label }}
                                </label>
                                @endforeach
                            </div>
                            @endif
                            @endif
                        </div>

                        @if(!$loop->last)
                        <hr class="border-[#F0E8F5]">
                        @endif

                        @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada kriteria yang dikonfigurasi.</p>
                        @endforelse
                    </div>

                    <div class="px-6 py-4 border-t border-[#F0E8F5] flex justify-end gap-3 shrink-0">
                        <button type="button" @click="modalOpen = false"
                                class="text-sm px-4 py-2 rounded-xl border border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5] transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="text-sm px-4 py-2 rounded-xl bg-[#2B124C] text-[#FBE4D8] hover:bg-[#522B5B] transition-colors">
                            Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function penilaianPage() {
    return {
        modalOpen: false,
        loading: false,
        userId: null,
        namaPeserta: '',
        nilaiExisting: {},

        openPenilaian(id, nama) {
            this.userId = id;
            this.namaPeserta = nama;
            this.nilaiExisting = {};
            this.loading = true;
            this.modalOpen = true;

            fetch(`/admin/penilaian/${id}/get`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                this.nilaiExisting = data;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
        },
    }
}
</script>
@endpush