{{--
    Blok "cara penilaian": radio Angka/Persentase/Pilihan Kata + field yang
    relevan (min-max, benefit/cost, atau daftar tingkatan linguistik).

    Semua prop *Var isinya STRING path variabel Alpine (bukan nilai asli),
    supaya bisa dipakai baik untuk state top-level (mis. "tipeInput") maupun
    nested object (mis. "editData.tipe_input"). String ini di-echo apa adanya
    ke atribut x-model / x-show, jadi Alpine yang mengevaluasinya sebagai JS.
--}}
@props([
    'heading'      => 'Bagaimana cara menilai ini?',
    'tipeInputVar',
    'tipeNilaiVar',
    'inputMinVar',
    'inputMaxVar',
    'skalaVar',
    'addFn',
    'removeFn',
    'removeCond',
])

<div>
    <label class="block text-xs font-medium text-gray-500 mb-2">{{ $heading }}</label>
    <div class="flex gap-2">
        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
               :class="{{ $tipeInputVar }} === 'numerik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
            <input type="radio" name="tipe_input" value="numerik" x-model="{{ $tipeInputVar }}" class="sr-only">
            <span class="text-lg">📊</span>
            <p class="text-xs font-medium text-gray-700">Angka</p>
            <p class="text-[10px] text-gray-400">Contoh: 85, 90</p>
        </label>
        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
               :class="{{ $tipeInputVar }} === 'persentase' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
            <input type="radio" name="tipe_input" value="persentase" x-model="{{ $tipeInputVar }}" class="sr-only">
            <span class="text-lg">📐</span>
            <p class="text-xs font-medium text-gray-700">Persentase</p>
            <p class="text-[10px] text-gray-400">0% – 100%</p>
        </label>
        <label class="flex-1 flex flex-col items-center gap-2 border border-[#E8D8F0] rounded-xl px-3 py-3 cursor-pointer hover:border-[#522B5B] transition-colors text-center"
               :class="{{ $tipeInputVar }} === 'linguistik' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
            <input type="radio" name="tipe_input" value="linguistik" x-model="{{ $tipeInputVar }}" class="sr-only">
            <span class="text-lg">📝</span>
            <p class="text-xs font-medium text-gray-700">Pilihan Kata</p>
            <p class="text-[10px] text-gray-400">Baik, Sangat Baik</p>
        </label>
    </div>
</div>

<div x-show="{{ $tipeInputVar }} === 'numerik'" x-cloak class="flex gap-3">
    <div class="flex-1">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Minimum</label>
        <input type="number" name="input_min" x-model="{{ $inputMinVar }}"
               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
    </div>
    <div class="flex-1">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Nilai Maksimum</label>
        <input type="number" name="input_max" x-model="{{ $inputMaxVar }}"
               class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
    </div>
</div>

<div x-show="{{ $tipeInputVar }} === 'numerik' || {{ $tipeInputVar }} === 'persentase'" x-cloak>
    <label class="block text-xs font-medium text-gray-500 mb-2">Bagaimana pengaruh nilai ini terhadap kinerja?</label>
    <div class="flex gap-3">
        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
               :class="{{ $tipeNilaiVar }} === 'benefit' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
            <input type="radio" name="tipe_nilai" value="benefit" x-model="{{ $tipeNilaiVar }}" class="accent-[#522B5B]">
            <div>
                <p class="text-sm font-medium text-gray-700">📈 Semakin tinggi semakin baik</p>
                <p class="text-[11px] text-gray-400">contoh: kehadiran, nilai tugas</p>
            </div>
        </label>
        <label class="flex-1 flex items-center gap-3 border border-[#E8D8F0] rounded-xl px-4 py-3 cursor-pointer hover:border-[#522B5B] transition-colors"
               :class="{{ $tipeNilaiVar }} === 'cost' ? 'border-[#522B5B] bg-[#EDE8F5]' : ''">
            <input type="radio" name="tipe_nilai" value="cost" x-model="{{ $tipeNilaiVar }}" class="accent-[#522B5B]">
            <div>
                <p class="text-sm font-medium text-gray-700">📉 Semakin rendah semakin baik</p>
                <p class="text-[11px] text-gray-400">contoh: ketidakhadiran, pelanggaran</p>
            </div>
        </label>
    </div>
</div>

<div x-show="{{ $tipeInputVar }} === 'linguistik'" x-cloak>
    <div class="flex items-center justify-between mb-2">
        <label class="text-xs font-medium text-gray-500">Tingkatan (dari terendah ke tertinggi)</label>
        <button type="button" @click="{{ $addFn }}" class="text-xs text-[#522B5B] hover:underline">+ Tambah</button>
    </div>
    <div class="space-y-2">
        <template x-for="(skala, i) in {{ $skalaVar }}" :key="i">
            <div class="flex items-start gap-2">
                <span class="text-xs text-gray-400 w-5 text-right shrink-0 mt-2" x-text="i + 1 + '.'"></span>
                <div class="flex-1 space-y-1.5">
                    <input type="text" :name="'skala[' + i + '][label]'"
                           x-model="{{ $skalaVar }}[i].label" placeholder="contoh: Sangat Disiplin"
                           class="w-full px-3 py-2 border border-[#E8D8F0] rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#522B5B]">
                    <textarea :name="'skala[' + i + '][definisi]'" rows="2"
                              x-model="{{ $skalaVar }}[i].definisi" placeholder="Definisi label ini, contoh: Selalu masuk/pulang tepat waktu"
                              class="w-full px-3 py-2 border border-[#E8D8F0] rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#522B5B] resize-none"></textarea>
                </div>
                <button type="button" @click="{{ $removeFn }}" x-show="{{ $removeCond }}"
                        class="text-red-400 hover:text-red-600 shrink-0 mt-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>
    <p class="text-[11px] text-gray-400 mt-2">Sistem akan otomatis menentukan nilai label berdasarkan urutan tingkatan menggunakan TFN.</p>
</div>