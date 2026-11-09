@extends('layouts.app')

@section('title', 'Masuk ke Infralink')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-surface text-brand-dark mb-3">
                <i class="fa-solid fa-lock text-2xl text-brand-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">Masuk ke Akun Anda</h2>
            <p class="text-sm text-slate-500 mt-1">Sistem Peminjaman Fasilitas & Sarpras Kampus</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-envelope text-sm"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="contoh@infralink.ac.id">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded-sm border-slate-300 text-brand-primary focus:ring-brand-primary">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-brand-dark to-brand-primary text-white font-bold text-sm shadow-md hover:opacity-95 transition-opacity">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Belum memiliki akun pemohon?
                <a href="{{ route('register') }}" class="font-bold text-brand-primary hover:underline">Daftar sekarang</a>
            </p>
        </div>

        <!-- Akun Demo Cepat -->
        <div class="mt-6 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
            <div class="font-bold text-slate-700 flex items-center justify-between">
                <span><i class="fa-solid fa-circle-info text-brand-primary mr-1"></i>Akun Uji Coba (Seeder):</span>
                <span class="text-slate-400">pass: password123</span>
            </div>
            <div class="flex justify-between">
                <span>Admin:</span>
                <code class="font-semibold text-brand-dark">admin@infralink.ac.id</code>
            </div>
            <div class="flex justify-between">
                <span>Approver:</span>
                <code class="font-semibold text-brand-dark">approver@infralink.ac.id</code>
            </div>
            <div class="flex justify-between">
                <span>Pemohon:</span>
                <code class="font-semibold text-brand-dark">mahasiswa@infralink.ac.id</code>
            </div>
        </div>
    </div>
</div>
@endsection
