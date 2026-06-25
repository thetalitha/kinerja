@extends('layouts.admin')

@section('title', 'Data Peserta')
@section('page-title', 'Data Peserta')

@section('content')
<div class="flex gap-5" x-data="pesertaPanel()">

    {{-- ── TABEL PESERTA ── --}}
    <div class="flex-1 min-w-0">

        {{-- Header + Filter --}}
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <p class="text-sm text-gray-500 mr-auto">
                Total <span class="font-medium text-gray-700" x-text="filteredCount"></span> peserta
            </p>

            {{-- Filter Room --}}
            <select x-model="filterRoom"
                    class="text-sm border border-[#E8D8F0] rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#522B5B] text-gray-600 bg-white">
                <option value="">Semua Room</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->room_id }}">{{ $room->nama_room }}</option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select x-model="filterStatus"
                    class="text-sm border border-[#E8D8F0] rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#522B5B] text-gray-600 bg-white">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="selesai">Selesai</option>
            </select>

            {{-- Search --}}
            <div class="relative">
                <input type="text"
                       x-model="search"
                       placeholder="Cari nama, NIM, institut..."
                       class="pl-9 pr-4 py-2 text-sm border border-[#E8D8F0] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#522B5B] w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                </svg>
            </div>

            {{-- Reset --}}
            <button x-show="search || filterRoom"
                    @click="search = ''; filterRoom = ''; filterStatus = ''"
                    class="text-xs text-[#522B5B] hover:underline"
                    x-cloak>
                Reset filter
            </button>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-xl border border-[#E8D8F0] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#2B124C] text-[#FBE4D8]">
                        <th class="px-4 py-3 text-left font-medium text-xs">Peserta</th>
                        <th class="px-4 py-3 text-left font-medium text-xs">Institut</th>
                        <th class="px-4 py-3 text-left font-medium text-xs hidden lg:table-cell">Fungsi</th>
                        <th class="px-4 py-3 text-left font-medium text-xs hidden lg:table-cell">Periode</th>
                        <th class="px-4 py-3 text-left font-medium text-xs hidden xl:table-cell">Mentor</th>
                        <th class="px-4 py-3 text-center font-medium text-xs">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F0E8F5]">
                    @forelse($peserta as $p)
                    @php
                        $rowData = json_encode([
                            'nama'     => strtolower($p->nama ?? ''),
                            'nim'      => strtolower($p->nim ?? ''),
                            'institut' => strtolower($p->institut ?? ''),
                            'room_id'  => (string) ($p->room_id ?? ''),
                             'status'   => ($p->periode_start && $p->periode_end && now()->between(
                                \Carbon\Carbon::parse($p->periode_start),
                                \Carbon\Carbon::parse($p->periode_end)
                            )) ? 'aktif' : 'selesai',
                        ]);
                        $now = now();
                        $aktif = $p->periode_start && $p->periode_end
                            && $now->between(
                                \Carbon\Carbon::parse($p->periode_start),
                                \Carbon\Carbon::parse($p->periode_end)
                            );
                    @endphp
                    <tr x-show="isVisible({{ $rowData }})"
                        :class="selectedId === {{ $p->id }} ? 'bg-[#EDE8F5]' : 'hover:bg-[#F5F0FF]'"
                        class="transition-colors duration-100">

                        {{-- Nama --}}
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

                        {{-- Institut --}}
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $p->institut ?? '-' }}</td>

                        {{-- Fungsi --}}
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($p->fungsi)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-[#EDE8F5] text-[#522B5B]">
                                    {{ $p->fungsi }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- Periode --}}
                        <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">
                            @if($p->periode_start && $p->periode_end)
                                {{ \Carbon\Carbon::parse($p->periode_start)->format('d M Y') }}
                                <span class="text-gray-300 mx-1">—</span>
                                {{ \Carbon\Carbon::parse($p->periode_end)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Mentor --}}
                        <td class="px-4 py-3 text-gray-600 text-xs hidden xl:table-cell">
                            {{ $p->nama_mentor ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 text-center">
                            @if($aktif)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 text-green-600 border border-green-200">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 border border-gray-200">Selesai</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3 text-right">
                            <button @click="loadDetail({{ $p->id }})"
                                    class="text-xs px-3 py-1.5 rounded-lg border transition-all duration-150"
                                    :class="selectedId === {{ $p->id }}
                                        ? 'bg-[#2B124C] text-[#FBE4D8] border-[#2B124C]'
                                        : 'border-[#522B5B] text-[#522B5B] hover:bg-[#EDE8F5]'">
                                Detail
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Belum ada data peserta.
                        </td>
                    </tr>
                    @endforelse

                    {{-- Empty state saat filter aktif tapi tidak ada hasil --}}
                    <tr x-show="filteredCount === 0 && {{ $peserta->count() }} > 0" x-cloak>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            Tidak ada peserta yang cocok dengan filter.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    {{-- ── DETAIL PANEL ── --}}
    <div x-show="panelOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         class="w-[360px] shrink-0"
         x-cloak>

        <div class="bg-white rounded-xl border border-[#E8D8F0] sticky top-20 overflow-hidden">

            {{-- Panel Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-[#F0E8F5]">
                <h3 class="text-sm font-semibold text-[#2B124C]">Detail Peserta</h3>
                <button @click="closePanel()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="loading" class="flex items-center justify-center py-16">
                <svg class="animate-spin w-6 h-6 text-[#522B5B]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>

            {{-- Content --}}
            <div x-show="!loading && data" class="overflow-y-auto max-h-[calc(100vh-10rem)]">

                {{-- Profil --}}
                <div class="px-4 py-4 border-b border-[#F0E8F5]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-[#2B124C] flex items-center justify-center text-[#FBE4D8] font-semibold shrink-0"
                             x-text="data?.peserta?.nama?.charAt(0).toUpperCase()">
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm" x-text="data?.peserta?.nama"></p>
                            <p class="text-gray-400 text-xs" x-text="data?.peserta?.nim ?? '-'"></p>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Institut</span>
                            <span class="text-gray-700 font-medium" x-text="data?.peserta?.institut ?? '-'"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Fungsi</span>
                            <span class="text-gray-700 font-medium" x-text="data?.peserta?.fungsi ?? '-'"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Email</span>
                            <span class="text-gray-700 font-medium" x-text="data?.peserta?.email ?? '-'"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Periode</span>
                            <span class="text-gray-700 font-medium" x-text="formatPeriode(data?.peserta?.periode_start, data?.peserta?.periode_end)"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Room</span>
                            <span class="text-gray-700 font-medium" x-text="data?.peserta?.nama_room ?? '-'"></span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-gray-400 w-16 shrink-0">Mentor</span>
                            <span class="text-gray-700 font-medium" x-text="data?.peserta?.nama_mentor ?? '-'"></span>
                        </div>
                    </div>
                </div>

                {{-- Kehadiran --}}
                <div class="px-4 py-4 border-b border-[#F0E8F5]">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Kehadiran</p>
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-2xl font-bold text-[#2B124C]" x-text="data?.kehadiran?.persen + '%'"></span>
                        <span class="text-xs text-gray-400" x-text="data?.kehadiran?.hadir + ' dari ' + data?.kehadiran?.total + ' hari'"></span>
                    </div>
                    <div class="w-full bg-[#F0E8F5] rounded-full h-1.5 mb-3">
                        <div class="bg-[#2B124C] h-1.5 rounded-full transition-all duration-500"
                             :style="'width: ' + data?.kehadiran?.persen + '%'"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-[#F7F8FA] rounded-lg px-2 py-2">
                            <p class="text-sm font-semibold text-gray-800" x-text="data?.kehadiran?.hadir"></p>
                            <p class="text-[10px] text-gray-400">Hadir</p>
                        </div>
                        <div class="bg-[#F7F8FA] rounded-lg px-2 py-2">
                            <p class="text-sm font-semibold text-gray-800" x-text="data?.kehadiran?.izin_sakit"></p>
                            <p class="text-[10px] text-gray-400">Izin/Sakit</p>
                        </div>
                        <div class="bg-[#F7F8FA] rounded-lg px-2 py-2">
                            <p class="text-sm font-semibold text-red-500" x-text="data?.kehadiran?.alpha"></p>
                            <p class="text-[10px] text-gray-400">Alpha</p>
                        </div>
                    </div>
                </div>

                {{-- Tugas --}}
                <div class="px-4 py-4 border-b border-[#F0E8F5]">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tugas</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-[#F7F8FA] rounded-lg px-3 py-2">
                            <p class="text-sm font-semibold text-gray-800" x-text="data?.tugas?.dikumpul + ' / ' + data?.tugas?.total"></p>
                            <p class="text-[10px] text-gray-400">Dikumpulkan</p>
                        </div>
                        <div class="bg-[#F7F8FA] rounded-lg px-3 py-2">
                            <p class="text-sm font-semibold text-gray-800"
                               x-text="data?.tugas?.rata_nilai !== null ? data?.tugas?.rata_nilai : '-'"></p>
                            <p class="text-[10px] text-gray-400">Rata-rata Nilai</p>
                        </div>
                    </div>
                </div>

                {{-- Evaluasi Mentor --}}
                <div class="px-4 py-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Evaluasi Mentor</p>
                    <template x-if="data?.evaluasi">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Kehadiran</span>
                                <span class="text-xs font-semibold text-gray-800" x-text="data?.evaluasi?.nilai_kehadiran ?? '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Tugas</span>
                                <span class="text-xs font-semibold text-gray-800" x-text="data?.evaluasi?.nilai_tugas ?? '-'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Sikap</span>
                                <span class="text-xs font-semibold text-gray-800" x-text="data?.evaluasi?.nilai_sikap ?? '-'"></span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-[#F0E8F5]">
                                <p class="text-[10px] text-gray-400 mb-1">Catatan</p>
                                <p class="text-xs text-gray-600" x-text="data?.evaluasi?.catatan ?? '-'"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="!data?.evaluasi">
                        <p class="text-xs text-gray-400">Belum ada evaluasi dari mentor.</p>
                    </template>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const __pesertaRows = {!! json_encode($peserta->map(function($p) {
        $aktif = $p->periode_start && $p->periode_end && now()->between(
            \Carbon\Carbon::parse($p->periode_start),
            \Carbon\Carbon::parse($p->periode_end)
        );
        return [
            'nama'     => strtolower($p->nama ?? ''),
            'nim'      => strtolower($p->nim ?? ''),
            'institut' => strtolower($p->institut ?? ''),
            'room_id'  => (string) ($p->room_id ?? ''),
            'status'   => $aktif ? 'aktif' : 'selesai',
        ];
    })) !!};
</script>
<script>
function pesertaPanel() {
    return {
        panelOpen: false,
        loading: false,
        selectedId: null,
        data: null,
        search: '',
        filterRoom: '',
        filterStatus: '',
        rows: __pesertaRows,

        get filteredCount() {
            return this.rows.filter(row => this.isVisible(row)).length;
        },

        isVisible(row) {
            const q = this.search.toLowerCase().trim();
            const matchSearch = !q
                || row.nama.includes(q)
                || row.nim.includes(q)
                || row.institut.includes(q);

            const matchRoom = !this.filterRoom
                || row.room_id === String(this.filterRoom);

            const matchStatus = !this.filterStatus
                || row.status === this.filterStatus;

            return matchSearch && matchRoom && matchStatus;
        },

        loadDetail(id) {
            this.selectedId = id;
            this.panelOpen = true;
            this.loading = true;
            this.data = null;

            fetch(`/admin/peserta/${id}/detail`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(json => {
                this.data = json;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
        },

        closePanel() {
            this.panelOpen = false;
            this.selectedId = null;
            this.data = null;
        },

        formatPeriode(start, end) {
            if (!start || !end) return '-';
            const fmt = (d) => new Date(d).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            });
            return fmt(start) + ' — ' + fmt(end);
        }
    }
}
</script>
@endpush