@extends('layouts.mentor')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div>
    <h1 class="text-2xl font-semibold text-gray-800">Selamat datang, {{ Auth::user()->nama }} 👋</h1>
    <p class="text-gray-500 text-sm mt-1">Ini adalah panel mentor SIMAGANG.</p>
</div>
@endsection