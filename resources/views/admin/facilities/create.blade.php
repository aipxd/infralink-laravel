@extends('layouts.app')

@section('title', 'Tambah Fasilitas Baru')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.facilities.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke Daftar Fasilitas
        </a>
        <h1 class="text-2xl font-bold text-slate-800 mt-2">Tambah Fasilitas Baru</h1>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Fasilitas *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Gedung Serbaguna"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gedung / Kompleks *</label>
                    <input type="text" name="building" value="{{ old('building') }}" required placeholder="Gedung Rektorat"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Detail Lokasi (Lantai/Ruang)</label>
                    <input type="text" name="location_detail" value="{{ old('location_detail') }}" placeholder="Lantai 3, Sayap Kiri"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Maksimal (Orang) *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', 50) }}" required min="1"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Buka (HH:mm) *</label>
                    <input type="time" name="open_time" value="{{ old('open_time', '08:00') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tutup (HH:mm) *</label>
                    <input type="time" name="close_time" value="{{ old('close_time', '21:00') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Durasi Maks (Menit) *</label>
                    <input type="number" name="max_duration_minutes" value="{{ old('max_duration_minutes', 240) }}" required step="30"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">240 menit = 4 jam</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Fasilitas Pendukung</label>
                <textarea name="description" rows="3" placeholder="AC, Sound system, Proyektor, Panggung..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tata Tertib & Aturan Penggunaan</label>
                <textarea name="usage_rules" rows="3" placeholder="Dilarang membawa makanan berkuah, wajib mematikan AC..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">{{ old('usage_rules') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foto Fasilitas (Bisa pilih beberapa)</label>
                <input type="file" name="photos[]" multiple accept="image/*"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-surface file:text-brand-dark">
            </div>

            <div class="pt-4 text-right">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-dark shadow-sm transition">
                    Simpan Fasilitas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
