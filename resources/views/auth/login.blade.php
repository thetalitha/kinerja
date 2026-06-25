@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="bg-white rounded-2xl border border-[#E8D8F0] shadow-sm p-8">

    <h2 class="text-xl font-semibold text-[#2B124C] mb-1">Masuk ke Sistem</h2>
    <p class="text-gray-400 text-sm mb-6">Hanya admin yang dapat mengakses SPK-Fuzzy.</p>

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf

        {{-- Username --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Username</label>
            <input
                type="text"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm text-gray-700 placeholder-gray-300
                       focus:outline-none focus:ring-2 focus:ring-[#522B5B] focus:border-transparent
                       @error('username') border-red-400 @enderror"
            >
            @error('username')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-6">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Password</label>
            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                class="w-full px-4 py-2.5 border border-[#E8D8F0] rounded-xl text-sm text-gray-700 placeholder-gray-300
                       focus:outline-none focus:ring-2 focus:ring-[#522B5B] focus:border-transparent
                       @error('password') border-red-400 @enderror"
            >
            @error('password')
                <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-[#2B124C] text-[#FBE4D8] font-semibold py-2.5 rounded-xl
                       hover:bg-[#522B5B] transition-colors duration-150 text-sm">
            Masuk
        </button>

    </form>

</div>
@endsection