@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daftar Peminjaman Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau status persetujuan dan riwayat pengajuan fasilitas Anda</p>
        </div>

        <a href="{{ route('facilities.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-brand-primary text-white text-xs font-bold hover:bg-brand-dark transition shadow-xs">
            <i class="fa-solid fa-plus mr-1.5"></i>Ajukan Peminjaman Baru
        </a>
    </div>

    <!-- Filter Bar (FR-40) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 mb-6">
        <form action="{{ route('bookings.mine') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-auto">
                <select name="status" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                    <option value="">Semua Status</option>
                    <option value="DIAJUKAN" {{ request('status') === 'DIAJUKAN' ? 'selected' : '' }}>Diajukan (Menunggu)</option>
                    <option value="DISETUJUI" {{ request('status') === 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                    <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                    <option value="DIBATALKAN" {{ request('status') === 'DIBATALKAN' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="w-full sm:w-auto">
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full sm:w-44 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                <i class="fa-solid fa-filter mr-1"></i>Filter
            </button>

            @if(request()->hasAny(['status', 'date']))
                <a href="{{ route('bookings.mine') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Booking List Table (FR-40) -->
    @if($bookings->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5">ID & Tanggal Pengajuan</th>
                        <th class="px-5 py-3.5">Fasilitas</th>
                        <th class="px-5 py-3.5">Jadwal Acara</th>
                        <th class="px-5 py-3.5">Nama Kegiatan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">#{{ $booking->id }}</span>
                            <span class="block text-[11px] text-slate-400 mt-0.5">
                                {{ $booking->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('facilities.show', $booking->facility_id) }}" class="font-bold text-slate-800 hover:text-brand-primary transition">
                                {{ $booking->facility->name }}
                            </a>
                            <span class="block text-[11px] text-slate-400 mt-0.5">{{ $booking->facility->building }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-700 block">
                                {{ $booking->booking_date->translatedFormat('d F Y') }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                {{ $booking->start_time }} - {{ $booking->end_time }} WIB
                            </span>
                        </td>
                        <td class="px-5 py-4 max-w-xs">
                            <span class="font-semibold text-slate-800 line-clamp-1">{{ $booking->event_name }}</span>
                            <span class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $booking->purpose }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $booking->status_badge_class }}">
                                {{ $booking->display_status }}
                            </span>
                            @if($booking->status === 'DITOLAK')
                                <span class="block text-[10px] text-rose-600 mt-1 max-w-[150px] truncate" title="{{ $booking->decision_note }}">
                                    Alasan: {{ $booking->decision_note }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right space-x-2">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-brand-surface hover:text-brand-dark font-bold text-xs transition">
                                Detail
                            </a>

                            @if($booking->status === 'DIAJUKAN')
                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition">
                                    Batalkan
                                </button>
                            </form>
                            @endif
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
        <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
            <i class="fa-solid fa-calendar-xmark"></i>
        </div>
        <h3 class="text-base font-bold text-slate-700">Belum ada riwayat peminjaman</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum pernah mengajukan peminjaman fasilitas atau tidak ada data yang cocok dengan filter yang dipilih.</p>
        <a href="{{ route('facilities.index') }}" class="inline-block mt-4 px-4 py-2 rounded-xl bg-brand-primary text-white font-bold text-xs">
            Cari Fasilitas Sekarang
        </a>
    </div>
    @endif
</div>
@endsection
