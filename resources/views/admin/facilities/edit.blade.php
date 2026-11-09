@extends('layouts.app')

@section('title', 'Edit Fasilitas - ' . $facility->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.facilities.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke Daftar Fasilitas
        </a>
        <h1 class="text-2xl font-bold text-slate-800 mt-2">Edit Fasilitas: {{ $facility->name }}</h1>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <form action="{{ route('admin.facilities.update', $facility->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Fasilitas *</label>
                    <input type="text" name="name" value="{{ old('name', $facility->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $facility->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gedung / Kompleks *</label>
                    <input type="text" name="building" value="{{ old('building', $facility->building) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Detail Lokasi</label>
                    <input type="text" name="location_detail" value="{{ old('location_detail', $facility->location_detail) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas Maksimal *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $facility->capacity) }}" required min="1"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Buka (HH:mm) *</label>
                    <input type="time" name="open_time" value="{{ old('open_time', $facility->open_time) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tutup (HH:mm) *</label>
                    <input type="time" name="close_time" value="{{ old('close_time', $facility->close_time) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Durasi Maks (Menit) *</label>
                    <input type="number" name="max_duration_minutes" value="{{ old('max_duration_minutes', $facility->max_duration_minutes) }}" required step="30"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Fasilitas Pendukung</label>
                <textarea name="description" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">{{ old('description', $facility->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tata Tertib & Aturan Penggunaan</label>
                <textarea name="usage_rules" rows="3"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">{{ old('usage_rules', $facility->usage_rules) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tambah Foto Fasilitas Baru</label>
                <input type="file" name="photos[]" multiple accept="image/*"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-surface file:text-brand-dark">
            </div>

            <div class="pt-4 text-right">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-dark shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
