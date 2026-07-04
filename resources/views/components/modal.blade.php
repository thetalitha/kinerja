@props(['show', 'title', 'maxWidth' => 'max-w-md'])

<div x-show="{{ $show }}" x-transition.opacity
     class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-cloak>
    <div @click.outside="{{ $show }} = false"
         class="bg-white rounded-2xl shadow-xl w-full {{ $maxWidth }} max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-[#F0E8F5]">
            <div>
                <h3 class="font-semibold text-[#2B124C]">{{ $title }}</h3>
                @isset($subtitle)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $subtitle }}</p>
                @endisset
            </div>
            <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>