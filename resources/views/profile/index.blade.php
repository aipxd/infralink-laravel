@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Profil & Pengaturan Akun</h1>
        <p class="text-sm text-slate-500">Kelola informasi pribadi dan perbarui kata sandi akun Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-brand-surface text-brand-dark flex items-center justify-center text-2xl font-black mb-3 border-2 border-brand-primary/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h3 class="text-lg font-bold text-slate-800">{{ $user->name }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>

            <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-left text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400">Peran:</span>
                    <span class="font-bold text-brand-primary">{{ $user->role }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">NIM / NIP:</span>
                    <span class="font-medium text-slate-700">{{ $user->identity_number ?: '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Unit / Ormawa:</span>
                    <span class="font-medium text-slate-700">{{ $user->unit_name ?: '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Status Akun:</span>
                    <span class="font-bold text-emerald-600">Aktif</span>
                </div>
            </div>
        </div>

        <!-- Form Edit Profil & Password -->
        <div class="md:col-span-2 space-y-6">
            <!-- Edit Profile -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center">
                    <i class="fa-solid fa-id-card text-brand-primary mr-2"></i>Informasi Akun
                </h3>
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="identity_number" class="block text-xs font-bold text-slate-600 mb-1">NIM / NIP</label>
                            <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number', $user->identity_number) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-600 mb-1">No. Kontak / WhatsApp</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label for="unit_name" class="block text-xs font-bold text-slate-600 mb-1">Unit / Ormawa / Bagian</label>
                        <input type="text" name="unit_name" id="unit_name" value="{{ old('unit_name', $user->unit_name) }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold shadow-sm hover:bg-brand-dark transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center">
                    <i class="fa-solid fa-key text-brand-primary mr-2"></i>Perbarui Kata Sandi
                </h3>
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-600 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-600 mb-1">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden" placeholder="Min. 8 karakter">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-600 mb-1">Ulangi Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden">
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold shadow-sm hover:bg-slate-900 transition-colors">
                            Ubah Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
