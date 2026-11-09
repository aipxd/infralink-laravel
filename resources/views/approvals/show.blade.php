@extends('layouts.app')

@section('title', 'Tinjau Pengajuan #' . $booking->id)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('approvals.index') }}" class="hover:text-brand-primary">Antrean Approval</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">Tinjau Pengajuan #{{ $booking->id }}</span>
    </nav>

    <!-- Overlap Warning Banner (FR-51) -->
    @if(count($conflictingPendingBookings) > 0)
    <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 shadow-xs">
        <div class="flex items-start">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg mr-3 mt-0.5"></i>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider">Perhatian: Ada Pengajuan Lain yang Bersinggungan Jadwal!</h4>
                <p class="text-xs mt-1 text-amber-800">
                    Terdapat <strong>{{ count($conflictingPendingBookings) }} pengajuan berstatus DIAJUKAN</strong> pada fasilitas yang sama dan waktu yang saling beririsan. Jika Anda menyetujui salah satu pengajuan, pengajuan lainnya yang bertabrakan tidak akan dapat disetujui lagi.
                </p>
                <div class="mt-3 space-y-1.5">
                    @foreach($conflictingPendingBookings as $overlap)
                    <div class="flex items-center justify-between text-xs bg-white/80 p-2.5 rounded-xl border border-amber-200/80">
                        <div>
                            <strong class="text-slate-800">#{{ $overlap->id }} - {{ $overlap->event_name }}</strong>
                            <span class="text-slate-500 text-[11px] block">
                                Oleh {{ $overlap->user->name }} ({{ $overlap->user->unit_name ?: 'Mahasiswa' }}) • Jam {{ $overlap->start_time }} - {{ $overlap->end_time }} WIB
                            </span>
                        </div>
                        <a href="{{ route('approvals.show', $overlap->id) }}" class="px-2.5 py-1 rounded-lg bg-amber-500 text-white font-bold text-[11px] hover:bg-amber-600">
                            Lihat Pengajuan
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri (2 Cols): Detail Pengajuan & Aksi -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <span class="text-xs font-bold text-slate-400">Pengajuan #{{ $booking->id }}</span>
                    <h1 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $booking->event_name }}</h1>
                    <span class="text-xs text-slate-500">Diajukan pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Tujuan / Latar Belakang Kegiatan:</span>
                        <p class="mt-1 text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100 whitespace-pre-line">
                            {{ $booking->purpose }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-3.5 rounded-xl">
                            <span class="text-slate-400 block font-medium">Jumlah Peserta:</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $booking->participants }} Orang</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Kapasitas Fasilitas: {{ $booking->facility->capacity }} Orang</span>
                        </div>
                        <div class="bg-slate-50 p-3.5 rounded-xl">
                            <span class="text-slate-400 block font-medium">Kontak Penanggung Jawab:</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $booking->contact_phone }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Pemohon: {{ $booking->user->name }}</span>
                        </div>
                    </div>

                    <!-- Lampiran -->
                    @if($booking->attachments->count() > 0)
                    <div>
                        <span class="text-slate-400 block font-medium mb-1">Dokumen Lampiran (Surat Pendukung):</span>
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
                    @endif
                </div>

                <!-- Form Keputusan Persetujuan (FR-52, BR-04, BR-11) -->
                @if($booking->status === 'DIAJUKAN')
                <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Keputusan Pengelola (Approver)</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Box Setujui -->
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200">
                            <h4 class="text-xs font-bold text-emerald-800 flex items-center mb-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 mr-1.5"></i>Setujui Pengajuan
                            </h4>
                            <p class="text-[11px] text-emerald-700 mb-3">Peminjaman akan dikonfirmasi dan jadwal akan memblokir kalender fasilitas.</p>
                            <form action="{{ route('approvals.approve', $booking->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Catatan Persetujuan (Opsional)</label>
                                    <textarea name="decision_note" rows="2" placeholder="Catatan teknis / koordinasi..."
                                        class="w-full p-2 rounded-lg border border-emerald-200 text-xs focus:border-emerald-500 outline-hidden bg-white"></textarea>
                                </div>
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MENYETUJUI pengajuan peminjaman ini?');"
                                    class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition shadow-xs flex items-center justify-center">
                                    <i class="fa-solid fa-check mr-1.5"></i>Setujui Pengajuan Ini
                                </button>
                            </form>
                        </div>

                        <!-- Box Tolak -->
                        <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200">
                            <h4 class="text-xs font-bold text-rose-800 flex items-center mb-2">
                                <i class="fa-solid fa-circle-xmark text-rose-600 mr-1.5"></i>Tolak Pengajuan
                            </h4>
                            <p class="text-[11px] text-rose-700 mb-3">Catatan alasan penolakan wajib diisi agar pemohon mengetahui penyebabnya.</p>
                            <form action="{{ route('approvals.reject', $booking->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Alasan Penolakan (Wajib) *</label>
                                    <textarea name="decision_note" rows="2" required placeholder="Jelaskan alasan penolakan pengajuan..."
                                        class="w-full p-2 rounded-lg border border-rose-200 text-xs focus:border-rose-500 outline-hidden bg-white"></textarea>
                                </div>
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?');"
                                    class="w-full py-2.5 px-3 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 transition shadow-xs flex items-center justify-center">
                                    <i class="fa-solid fa-xmark mr-1.5"></i>Tolak Pengajuan Ini
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    Status pengajuan ini sudah final (<strong>{{ $booking->status }}</strong>) pada {{ $booking->decided_at?->translatedFormat('d F Y, H:i') }}.
                    @if($booking->decision_note)
                        <div class="mt-1 font-medium">Catatan: {{ $booking->decision_note }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan (1 Col): Jadwal Fasilitas Hari Terkait -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-4 flex items-center">
                    <i class="fa-regular fa-calendar text-brand-primary mr-1.5"></i>Jadwal Pada Tanggal Terkait
                </h3>
                
                <div class="p-3 rounded-xl bg-brand-surface border border-brand-primary/20 text-xs mb-4">
                    <span class="text-slate-500 block font-medium">Fasilitas:</span>
                    <strong class="text-brand-dark block text-sm">{{ $booking->facility->name }}</strong>
                    <span class="text-slate-600 block mt-1">Tanggal: <strong>{{ $booking->booking_date->translatedFormat('d F Y') }}</strong></span>
                    <span class="text-brand-primary font-bold block">Diminta: {{ $booking->start_time }} - {{ $booking->end_time }} WIB</span>
                </div>

                <div class="space-y-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Jadwal Lain Pada Hari Tersebut:</span>

                    <!-- Approved bookings on that day -->
                    @forelse($dayApprovedBookings as $approved)
                    <div class="p-3 rounded-xl bg-red-50 border border-red-200 text-xs">
                        <div class="flex items-center justify-between font-bold text-red-800">
                            <span>{{ $approved->start_time }} - {{ $approved->end_time }} WIB</span>
                            <span class="text-[10px] uppercase px-1.5 py-0.5 rounded-sm bg-red-200">Disetujui</span>
                        </div>
                        <span class="font-semibold text-slate-800 block mt-1">{{ $approved->event_name }}</span>
                        <span class="text-[11px] text-slate-500 block">{{ $approved->user->unit_name ?: $approved->user->name }}</span>
                    </div>
                    @empty
                    @endforelse

                    <!-- Blocked slots on that day -->
                    @forelse($dayBlockedSlots as $blocked)
                    <div class="p-3 rounded-xl bg-slate-100 border border-slate-300 text-xs">
                        <div class="flex items-center justify-between font-bold text-slate-800">
                            <span>{{ $blocked->start_time }} - {{ $blocked->end_time }} WIB</span>
                            <span class="text-[10px] uppercase px-1.5 py-0.5 rounded-sm bg-slate-300">Blokir Admin</span>
                        </div>
                        <span class="text-slate-700 block mt-1">{{ $blocked->reason }}</span>
                    </div>
                    @empty
                    @endforelse

                    @if($dayApprovedBookings->isEmpty() && $dayBlockedSlots->isEmpty())
                    <p class="text-xs text-slate-400 italic">Tidak ada jadwal booking disetujui atau blokir pada tanggal ini.</p>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('facilities.show', $booking->facility_id) }}" target="_blank" class="text-xs font-bold text-brand-primary hover:underline flex items-center justify-center">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1.5"></i>Buka Kalender Fasilitas Penuh
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
