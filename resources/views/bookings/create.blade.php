@extends('layouts.app')

@section('title', 'Ajukan Peminjaman - ' . $facility->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('facilities.index') }}" class="hover:text-brand-primary">Katalog Fasilitas</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
        <a href="{{ route('facilities.show', $facility->id) }}" class="hover:text-brand-primary">{{ $facility->name }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">Form Pengajuan</span>
    </nav>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-10">
        <!-- Header Info -->
        <div class="border-b border-slate-100 pb-6 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-surface text-brand-dark mb-2">
                    {{ $facility->category->name }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">Form Pengajuan Peminjaman</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Fasilitas: <strong class="text-slate-700">{{ $facility->name }}</strong> ({{ $facility->building }})
                </p>
            </div>

            <div class="text-left sm:text-right text-xs bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                <span class="text-slate-400 block font-medium">Batas Ketentuan Fasilitas:</span>
                <span class="font-bold text-slate-700 block">Jam Operasional: {{ $facility->open_time }} - {{ $facility->close_time }} WIB</span>
                <span class="text-slate-600 block">Kapasitas Maks: <strong>{{ $facility->capacity }} Orang</strong> • Durasi Maks: <strong>{{ $facility->max_duration_minutes / 60 }} Jam</strong></span>
            </div>
        </div>

        <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="facility_id" value="{{ $facility->id }}">

            <!-- Row 1: Tanggal & Waktu -->
            <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-brand-dark mb-4 flex items-center">
                    <i class="fa-regular fa-calendar-check mr-2 text-brand-primary"></i>1. Waktu Pelaksanaan Kegiatan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="booking_date" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kegiatan *</label>
                        <input type="date" name="booking_date" id="booking_date"
                            value="{{ old('booking_date', $defaultDate) }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            max="{{ date('Y-m-d', strtotime('+90 days')) }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                        <span class="text-[10px] text-slate-400 mt-1 block">Minimal H+1, maksimal 90 hari ke depan</span>
                    </div>

                    <div>
                        <label for="start_time" class="block text-xs font-bold text-slate-700 mb-1">Jam Mulai (WIB) *</label>
                        <input type="time" name="start_time" id="start_time"
                            value="{{ old('start_time', $defaultStart) }}"
                            min="{{ $facility->open_time }}"
                            max="{{ $facility->close_time }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                        <span class="text-[10px] text-slate-400 mt-1 block">Buka mulai {{ $facility->open_time }} WIB</span>
                    </div>

                    <div>
                        <label for="end_time" class="block text-xs font-bold text-slate-700 mb-1">Jam Selesai (WIB) *</label>
                        <input type="time" name="end_time" id="end_time"
                            value="{{ old('end_time', $defaultEnd) }}"
                            min="{{ $facility->open_time }}"
                            max="{{ $facility->close_time }}"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                        <span class="text-[10px] text-slate-400 mt-1 block">Tutup pada {{ $facility->close_time }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Row 2: Informasi Acara -->
            <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-brand-dark mb-4 flex items-center">
                    <i class="fa-solid fa-bullhorn mr-2 text-brand-primary"></i>2. Rincian Kegiatan
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="event_name" class="block text-xs font-bold text-slate-700 mb-1">Nama Kegiatan / Acara *</label>
                        <input type="text" name="event_name" id="event_name" value="{{ old('event_name') }}" required
                            placeholder="Contoh: Rapat Kerja Ormawa 2026 / Seminar Nasional AI"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                    </div>

                    <div>
                        <label for="purpose" class="block text-xs font-bold text-slate-700 mb-1">Tujuan & Deskripsi Kegiatan *</label>
                        <textarea name="purpose" id="purpose" rows="3" required
                            placeholder="Jelaskan secara ringkas maksud dan agenda acara..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">{{ old('purpose') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="participants" class="block text-xs font-bold text-slate-700 mb-1">Perkiraan Jumlah Peserta (Orang) *</label>
                            <input type="number" name="participants" id="participants" value="{{ old('participants', 10) }}" min="1" max="{{ $facility->capacity }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                            <span class="text-[10px] text-slate-400 mt-1 block">Maksimal {{ $facility->capacity }} orang sesuai kapasitas ruang</span>
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-xs font-bold text-slate-700 mb-1">Nomor Kontak WhatsApp / HP Penanggung Jawab *</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}" required
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 outline-hidden bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Dokumen Pendukung (Opsional) -->
            <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/60">
                <h3 class="text-xs font-bold uppercase tracking-wider text-brand-dark mb-4 flex items-center">
                    <i class="fa-solid fa-file-arrow-up mr-2 text-brand-primary"></i>3. Dokumen Lampiran (Surat / Proposal)
                </h3>
                <div>
                    <label for="document" class="block text-xs font-bold text-slate-700 mb-1">Unggah Surat Pengantar / Lembar Persetujuan Unit (Opsional)</label>
                    <input type="file" name="document" id="document" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-surface file:text-brand-dark hover:file:bg-brand-primary hover:file:text-white bg-white transition">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, JPG, atau PNG (Maksimal 5 MB).</span>
                </div>
            </div>

            <!-- Submit Button & Disclaimer -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('facilities.show', $facility->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    <i class="fa-solid fa-arrow-left mr-1"></i>Kembali ke Detail Fasilitas
                </a>

                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-brand-dark to-brand-primary text-white font-bold text-sm shadow-md hover:opacity-95 transition flex items-center justify-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Kirim Pengajuan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
