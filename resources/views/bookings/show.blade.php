@extends('layouts.app')

@section('title', 'Detail Pengajuan #' . $booking->id)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('bookings.mine') }}" class="hover:text-brand-primary">Peminjaman Saya</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">Pengajuan #{{ $booking->id }}</span>
    </nav>

    <div class="space-y-6">
        <!-- Main Card Information -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-xs font-bold text-slate-400">Pengajuan #{{ $booking->id }}</span>
                        <span class="text-slate-300">•</span>
                        <span class="text-xs text-slate-500">Diajukan pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-800">{{ $booking->event_name }}</h1>
                </div>

                <div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border {{ $booking->status_badge_class }}">
                        <i class="fa-solid fa-circle-dot mr-1.5 text-[10px]"></i>Status: {{ $booking->display_status }}
                    </span>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <!-- Fasilitas Info -->
                <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <span class="font-bold text-brand-dark uppercase tracking-wider block">Fasilitas Kampus</span>
                    <div>
                        <span class="text-slate-400 block font-medium">Nama Fasilitas:</span>
                        <a href="{{ route('facilities.show', $booking->facility_id) }}" class="font-bold text-sm text-slate-800 hover:text-brand-primary">
                            {{ $booking->facility->name }}
                        </a>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Lokasi:</span>
                        <span class="font-semibold text-slate-700">{{ $booking->facility->building }} ({{ $booking->facility->location_detail }})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Kapasitas Maksimal:</span>
                        <span class="font-semibold text-slate-700">{{ $booking->facility->capacity }} Orang</span>
                    </div>
                </div>

                <!-- Waktu & Kontak -->
                <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <span class="font-bold text-brand-dark uppercase tracking-wider block">Waktu & Kontak Peminjam</span>
                    <div>
                        <span class="text-slate-400 block font-medium">Tanggal Pelaksanaan:</span>
                        <span class="font-bold text-sm text-slate-800">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Rentang Jam:</span>
                        <span class="font-bold text-brand-primary">{{ $booking->start_time }} - {{ $booking->end_time }} WIB</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Pemohon & Unit:</span>
                        <span class="font-semibold text-slate-700">{{ $booking->user->name }} ({{ $booking->user->unit_name ?: 'Mahasiswa' }})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">No. WhatsApp / Kontak:</span>
                        <span class="font-semibold text-slate-700">{{ $booking->contact_phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Deskripsi & Peserta -->
            <div class="mt-6 space-y-4 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Perkiraan Jumlah Peserta:</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $booking->participants }} Orang</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Tujuan / Deskripsi Kegiatan:</span>
                    <p class="mt-1 text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100 whitespace-pre-line">
                        {{ $booking->purpose }}
                    </p>
                </div>

                <!-- Lampiran Dokumen -->
                @if($booking->attachments->count() > 0)
                <div>
                    <span class="text-slate-400 block font-medium mb-1.5">Dokumen Lampiran (Surat / Lembar Persetujuan):</span>
                    <div class="space-y-2">
                        @foreach($booking->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-brand-surface/60 border border-brand-primary/20">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-file-pdf text-brand-primary text-base"></i>
                                <span class="font-semibold text-slate-800">{{ $attachment->file_name }}</span>
                                <span class="text-slate-400 text-[10px]">({{ round($attachment->file_size / 1024, 1) }} KB)</span>
                            </div>
                            <a href="{{ route('bookings.attachment.download', $attachment->id) }}" class="px-3 py-1.5 rounded-lg bg-brand-primary text-white font-bold text-xs hover:bg-brand-dark transition">
                                <i class="fa-solid fa-download mr-1"></i>Unduh
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Action: Batalkan jika status DIAJUKAN (BR-10) -->
            @if($booking->status === 'DIAJUKAN' && (auth()->id() === $booking->user_id || auth()->user()->isAdmin()))
            <div class="mt-6 pt-6 border-t border-slate-100 flex justify-end">
                <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl border border-rose-300 text-rose-600 font-bold text-xs hover:bg-rose-50 transition">
                        <i class="fa-solid fa-ban mr-1.5"></i>Batalkan Pengajuan Ini
                    </button>
                </form>
            </div>
            @endif
        </div>

        <!-- Timeline Riwayat Status (FR-41 & FR-42) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
            <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center">
                <i class="fa-solid fa-clock-rotate-left text-brand-primary mr-2"></i>Riwayat Perubahan Status (Audit Trail)
            </h3>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @foreach($booking->statusLogs as $log)
                <div class="relative">
                    <span class="absolute -left-6 top-0.5 w-4 h-4 rounded-full border-2 border-white 
                        {{ $log->to_status === 'DISETUJUI' ? 'bg-emerald-500 ring-4 ring-emerald-100' : ($log->to_status === 'DITOLAK' ? 'bg-rose-500 ring-4 ring-rose-100' : ($log->to_status === 'DIBATALKAN' ? 'bg-slate-400 ring-4 ring-slate-100' : 'bg-amber-400 ring-4 ring-amber-100')) }}">
                    </span>
                    <div class="text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-slate-800">{{ $log->to_status }}</span>
                            <span class="text-slate-400">•</span>
                            <span class="text-slate-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Oleh: <strong>{{ $log->changer->name }}</strong> ({{ $log->changer->role }})</p>
                        @if($log->note)
                        <div class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-700">
                            <strong>Catatan:</strong> {{ $log->note }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
