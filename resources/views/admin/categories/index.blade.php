@extends('layouts.app')

@section('title', 'Kategori Fasilitas')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Kategori Fasilitas</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola kategori pengelompokan sarana dan prasarana kampus</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Form Tambah Kategori -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Tambah Kategori Baru</h3>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Gedung Olahraga"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-dark transition shadow-xs">
                    Simpan Kategori
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="md:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5">Nama Kategori</th>
                        <th class="px-5 py-3.5">Jumlah Fasilitas</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categories as $category)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 font-bold text-slate-800 text-sm">
                            {{ $category->name }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $category->facilities_count }} Fasilitas
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right space-x-2">
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold" {{ $category->facilities_count > 0 ? 'disabled title="Kategori masih memiliki fasilitas"' : '' }}>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
