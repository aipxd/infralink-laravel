@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-8">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-surface text-brand-dark mb-3">
                <i class="fa-solid fa-user-plus text-2xl text-brand-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800">Registrasi Pemohon</h2>
            <p class="text-sm text-slate-500 mt-1">Daftar akun untuk meminjam sarana & prasarana kampus</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                    placeholder="Contoh: Muhammad Raihan">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Kampus *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                    placeholder="nama@infralink.ac.id">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="identity_number" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">NIM / NIP</label>
                    <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="Contoh: 2301010042">
                </div>
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">No. WhatsApp / HP</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div>
                <label for="unit_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Organisasi / Unit / Jurusan</label>
                <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name') }}"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                    placeholder="Contoh: BEM Fakultas Ilmu Komputer / Mahasiswa TI">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Kata Sandi *</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Ulangi Sandi *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden transition"
                        placeholder="Ulangi kata sandi">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-brand-dark to-brand-primary text-white font-bold text-sm shadow-md hover:opacity-95 transition-opacity">
                    Daftar Sebagai Pemohon
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
