@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard Administrator</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Ringkasan status sarpras, pengajuan peminjaman, dan operasional kampus</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Fasilitas</span>
                <span class="w-9 h-9 rounded-xl bg-brand-surface text-brand-dark flex items-center justify-center text-sm">
                    <i class="fa-solid fa-door-open"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline">
                <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $stats['total_facilities'] }}</span>
                <span class="ml-2 text-xs font-bold text-emerald-600">({{ $stats['active_facilities'] }} aktif)</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Menunggu Approval</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl sm:text-3xl font-black text-amber-600">{{ $stats['pending_bookings'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peminjaman Disetujui</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="mt-3">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['approved_bookings'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengguna Terdaftar</span>
                <span class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="mt-3 flex items-baseline">
                <span class="text-2xl sm:text-3xl font-black text-slate-800">{{ $stats['total_users'] }}</span>
                <span class="ml-2 text-xs font-bold text-slate-400">Akun</span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Panels & Recent Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Navigasi Cepat -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Manajemen Cepat</h3>

            <a href="{{ route('admin.facilities.create') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-brand-surface hover:text-brand-dark transition group">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-lg bg-brand-primary text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                    <span class="text-xs font-bold text-slate-700 group-hover:text-brand-dark">Tambah Fasilitas Baru</span>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-brand-primary"></i>
            </a>

            <a href="{{ route('admin.facilities.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-brand-surface hover:text-brand-dark transition group">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-lg bg-slate-700 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-door-open"></i>
                    </span>
                    <span class="text-xs font-bold text-slate-700 group-hover:text-brand-dark">Kelola Fasilitas</span>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-brand-primary"></i>
            </a>

            <a href="{{ route('admin.blocked-slots.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-brand-surface hover:text-brand-dark transition group">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </span>
                    <span class="text-xs font-bold text-slate-700 group-hover:text-brand-dark">Blokir Jadwal (Maintenance/Libur)</span>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-brand-primary"></i>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 hover:bg-brand-surface hover:text-brand-dark transition group">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>
                    <span class="text-xs font-bold text-slate-700 group-hover:text-brand-dark">Kelola Pengguna & Peran</span>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-brand-primary"></i>
            </a>
        </div>

        <!-- Recent Bookings Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Pengajuan Peminjaman Terbaru</h3>
                <a href="{{ route('approvals.index') }}" class="text-xs font-bold text-brand-primary hover:underline">Lihat Antrean</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Fasilitas</th>
                            <th class="py-2.5 px-3">Pemohon</th>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentBookings as $rb)
                        <tr>
                            <td class="py-3 px-3 font-semibold text-slate-800">{{ $rb->facility->name }}</td>
                            <td class="py-3 px-3">{{ $rb->user->name }}</td>
                            <td class="py-3 px-3">{{ $rb->booking_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rb->status_badge_class }}">
                                    {{ $rb->display_status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
