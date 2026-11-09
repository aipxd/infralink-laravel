@extends('layouts.app')

@section('title', 'Katalog Fasilitas Sarpras')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-dark via-brand-primary to-brand-green p-8 sm:p-10 mb-8 text-white shadow-xl shadow-brand-dark/10">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-xs mb-3">
                <i class="fa-solid fa-sparkles mr-1.5"></i>Peminjaman Online Terpadu
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Katalog Fasilitas & Ruang Kampus</h1>
            <p class="mt-2 text-sm sm:text-base text-emerald-50 leading-relaxed">
                Cari ruangan, aula, laboratorium, atau sarana olahraga kampus. Cek ketersediaan jadwal secara real-time dan ajukan peminjaman secara transparan.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-10 opacity-15 pointer-events-none">
            <i class="fa-solid fa-building-columns text-[220px]"></i>
        </div>
    </div>

    <!-- Filter & Pencarian Box (FR-12 & FR-13) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 mb-8">
        <form action="{{ route('facilities.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Keyword search -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Cari Fasilitas / Gedung</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Contoh: Auditorium, Senat..."
                            class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden">
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Kategori</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gedung -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Lokasi Gedung</label>
                    <select name="building" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden bg-white">
                        <option value="">Semua Gedung</option>
                        @foreach($buildings as $bld)
                            <option value="{{ $bld }}" {{ request('building') == $bld ? 'selected' : '' }}>
                                {{ $bld }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Min Kapasitas -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Kapasitas Minimal</label>
                    <input type="number" name="min_capacity" value="{{ request('min_capacity') }}" placeholder="Jumlah orang (misal 50)"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-1 focus:ring-brand-primary outline-hidden">
                </div>
            </div>

            <!-- Filter Ketersediaan Tanggal & Jam (FR-13) -->
            <div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row items-end justify-between gap-4">
                <div class="w-full grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-brand-dark mb-1">
                            <i class="fa-regular fa-calendar mr-1"></i>Filter Ketersediaan: Tanggal
                        </label>
                        <input type="date" name="available_date" value="{{ request('available_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-brand-dark mb-1">
                            <i class="fa-regular fa-clock mr-1"></i>Jam Mulai (HH:mm)
                        </label>
                        <input type="time" name="start_time" value="{{ request('start_time') }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-brand-dark mb-1">
                            <i class="fa-regular fa-clock mr-1"></i>Jam Selesai (HH:mm)
                        </label>
                        <input type="time" name="end_time" value="{{ request('end_time') }}"
                            class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <a href="{{ route('facilities.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Reset
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-primary text-white text-xs font-bold shadow-sm hover:bg-brand-dark transition-colors flex items-center">
                        <i class="fa-solid fa-filter mr-1.5"></i>Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Fasilitas Grid (FR-10) -->
    @if($facilities->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($facilities as $facility)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col hover:shadow-md hover:border-brand-primary/30 transition-all group">
            <!-- Facility Image Thumbnail -->
            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                <img src="{{ $facility->primary_photo_url }}" alt="{{ $facility->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute top-3 left-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/90 text-brand-dark backdrop-blur-xs shadow-xs">
                        {{ $facility->category->name }}
                    </span>
                </div>
                <div class="absolute bottom-3 right-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-900/80 text-white backdrop-blur-xs">
                        <i class="fa-solid fa-users mr-1.5 text-xs text-brand-lime"></i>Kapasitas: {{ $facility->capacity }} Org
                    </span>
                </div>
            </div>

            <!-- Facility Content Info -->
            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800 group-hover:text-brand-primary transition-colors line-clamp-1">
                        {{ $facility->name }}
                    </h3>
                    <p class="text-xs text-slate-500 flex items-center mt-1">
                        <i class="fa-solid fa-location-dot mr-1.5 text-slate-400"></i>
                        {{ $facility->building }} @if($facility->location_detail) • {{ $facility->location_detail }} @endif
                    </p>

                    <p class="text-xs text-slate-600 mt-3 line-clamp-2 leading-relaxed">
                        {{ $facility->description ?: 'Fasilitas kampus terawat lengkap dengan sarana standar operasional kegiatan akademik dan mahasiswa.' }}
                    </p>
                </div>

                <!-- Footer Info & Action Buttons -->
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-3">
                        <span class="flex items-center">
                            <i class="fa-regular fa-clock mr-1 text-slate-400"></i>{{ $facility->open_time }} - {{ $facility->close_time }} WIB
                        </span>
                        <span>
                            Max: {{ $facility->max_duration_minutes / 60 }} Jam
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('facilities.show', $facility->id) }}" class="py-2 px-3 rounded-xl border border-slate-200 text-slate-700 text-center font-bold text-xs hover:bg-slate-50 transition-colors flex items-center justify-center">
                            <i class="fa-regular fa-calendar-days mr-1.5 text-slate-400"></i>Lihat Jadwal
                        </a>
                        <a href="{{ route('bookings.create', $facility->id) }}" class="py-2 px-3 rounded-xl bg-brand-primary text-white text-center font-bold text-xs hover:bg-brand-dark transition-colors flex items-center justify-center shadow-xs">
                            <i class="fa-solid fa-paper-plane mr-1.5"></i>Ajukan Pinjam
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4 mb-8">
        {{ $facilities->links() }}
    </div>

    @else
    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center my-6">
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-inbox"></i>
        </div>
        <h3 class="text-base font-bold text-slate-700">Tidak ada fasilitas yang sesuai</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Coba ubah kriteria pencarian, kurangi filter kapasitas atau rentang tanggal yang dipilih.</p>
        <a href="{{ route('facilities.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-brand-primary text-white font-bold text-xs">
            Reset Pencarian
        </a>
    </div>
    @endif
</div>
@endsection
