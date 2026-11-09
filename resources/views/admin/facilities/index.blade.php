@extends('layouts.app')

@section('title', 'Kelola Fasilitas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Fasilitas & Ruangan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola daftar sarana dan prasarana kampus yang dapat dipinjam</p>
        </div>

        <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-dark transition shadow-xs">
            <i class="fa-solid fa-plus mr-1.5"></i>Tambah Fasilitas Baru
        </a>
    </div>

    <!-- Filter & Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5">Fasilitas & Kategori</th>
                        <th class="px-5 py-3.5">Gedung / Lokasi</th>
                        <th class="px-5 py-3.5">Kapasitas</th>
                        <th class="px-5 py-3.5">Jam Operasional</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($facilities as $facility)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $facility->primary_photo_url }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" alt="">
                                <div>
                                    <span class="font-bold text-slate-800 block text-sm">{{ $facility->name }}</span>
                                    <span class="text-[11px] text-brand-primary">{{ $facility->category->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-semibold text-slate-700 block">{{ $facility->building }}</span>
                            <span class="text-[11px] text-slate-400">{{ $facility->location_detail ?: '-' }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $facility->capacity }}</span> Orang
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-700">{{ $facility->open_time }} - {{ $facility->close_time }} WIB</span>
                            <span class="text-[10px] text-slate-400 block">Maks {{ $facility->max_duration_minutes / 60 }} jam</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($facility->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right space-x-1.5">
                            <a href="{{ route('facilities.show', $facility->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-[11px]">
                                Kalender
                            </a>
                            <a href="{{ route('admin.facilities.edit', $facility->id) }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-[11px]">
                                Edit
                            </a>
                            <form action="{{ route('admin.facilities.toggle-status', $facility->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border {{ $facility->is_active ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} font-semibold text-[11px]">
                                    {{ $facility->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.facilities.destroy', $facility->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus fasilitas ini secara permanen?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-[11px]">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $facilities->links() }}
        </div>
    </div>
</div>
@endsection
