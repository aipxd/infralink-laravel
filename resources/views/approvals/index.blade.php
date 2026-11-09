@extends('layouts.app')

@section('title', 'Antrean Persetujuan Pengajuan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-slate-800">Antrean Persetujuan Peminjaman</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">
                    {{ $bookings->total() }} Menunggu
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar pengajuan peminjaman fasilitas yang membutuhkan persetujuan pengelola</p>
        </div>
    </div>

    <!-- Filter (FR-50) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 mb-6">
        <form action="{{ route('approvals.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-auto">
                <select name="facility_id" class="w-full sm:w-56 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                    <option value="">Semua Fasilitas</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}" {{ request('facility_id') == $facility->id ? 'selected' : '' }}>
                            {{ $facility->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-auto">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full sm:w-44 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                <i class="fa-solid fa-filter mr-1"></i>Filter
            </button>

            @if(request()->hasAny(['facility_id', 'date']))
                <a href="{{ route('approvals.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Antrean Table (FR-50) -->
    @if($bookings->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5">Diajukan Pada</th>
                        <th class="px-5 py-3.5">Fasilitas</th>
                        <th class="px-5 py-3.5">Jadwal Diminta</th>
                        <th class="px-5 py-3.5">Pemohon / Ormawa</th>
                        <th class="px-5 py-3.5">Nama Kegiatan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">#{{ $booking->id }}</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">
                                {{ $booking->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800 block">{{ $booking->facility->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ $booking->facility->building }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-700 block">
                                {{ $booking->booking_date->translatedFormat('d F Y') }}
                            </span>
                            <span class="text-[11px] font-bold text-brand-dark">
                                {{ $booking->start_time }} - {{ $booking->end_time }} WIB
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-semibold text-slate-800 block">{{ $booking->user->name }}</span>
                            <span class="text-[11px] text-brand-primary block">{{ $booking->user->unit_name ?: 'Mahasiswa' }}</span>
                        </td>
                        <td class="px-5 py-4 max-w-xs">
                            <span class="font-semibold text-slate-800 line-clamp-1">{{ $booking->event_name }}</span>
                            <span class="text-[11px] text-slate-400">{{ $booking->participants }} Peserta</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('approvals.show', $booking->id) }}"
                                class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-primary text-white font-bold text-xs hover:bg-brand-dark transition shadow-xs">
                                <i class="fa-solid fa-clipboard-check mr-1.5"></i>Tinjau Pengajuan
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $bookings->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center my-6">
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 mx-auto flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <h3 class="text-base font-bold text-slate-700">Semua pengajuan telah diproses!</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Saat ini tidak ada antrean pengajuan peminjaman baru yang menunggu persetujuan.</p>
    </div>
    @endif
</div>
@endsection
