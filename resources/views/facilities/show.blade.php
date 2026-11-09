@extends('layouts.app')

@section('title', $facility->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('facilities.index') }}" class="hover:text-brand-primary">Katalog Fasilitas</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
        <span class="text-slate-800 font-semibold">{{ $facility->name }}</span>
    </nav>

    <!-- Top Facility Detail Header -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Facility Photo Gallery (Left 5 Cols) -->
            <div class="lg:col-span-5 space-y-3">
                <div class="h-64 sm:h-72 w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img id="mainPhoto" src="{{ $facility->primary_photo_url }}" alt="{{ $facility->name }}" class="w-full h-full object-cover">
                </div>
                @if($facility->photos->count() > 1)
                <div class="flex items-center space-x-2 overflow-x-auto pb-1">
                    @foreach($facility->photos as $photo)
                    <button type="button" onclick="document.getElementById('mainPhoto').src = '{{ $photo->url }}'"
                        class="w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-brand-primary focus:border-brand-primary shrink-0 transition">
                        <img src="{{ $photo->url }}" alt="thumb" class="w-full h-full object-cover">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Facility Metadata (Right 7 Cols) -->
            <div class="lg:col-span-7 flex flex-col justify-between h-full">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-brand-surface text-brand-dark">
                            {{ $facility->category->name }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                            <i class="fa-solid fa-users mr-1 text-slate-400"></i>Kapasitas: {{ $facility->capacity }} Orang
                        </span>
                        @if($facility->is_active)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800">
                                <i class="fa-solid fa-circle-check mr-1 text-emerald-500"></i>Tersedia
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">
                        {{ $facility->name }}
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-500 flex items-center mt-1.5">
                        <i class="fa-solid fa-location-dot text-brand-primary mr-1.5"></i>
                        {{ $facility->building }} @if($facility->location_detail) — {{ $facility->location_detail }} @endif
                    </p>

                    <!-- Deskripsi & Fasilitas Pendukung -->
                    <div class="mt-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Deskripsi & Fasilitas Pendukung:</h4>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $facility->description ?: 'Belum ada deskripsi fasilitas pendukung.' }}
                        </p>
                    </div>

                    <!-- Aturan Pakai -->
                    @if($facility->usage_rules)
                    <div class="mt-4 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900">
                        <div class="font-bold mb-1 flex items-center">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5 text-amber-600"></i>Tata Tertib & Aturan Penggunaan:
                        </div>
                        <p class="whitespace-pre-line leading-relaxed text-[11px]">{{ $facility->usage_rules }}</p>
                    </div>
                    @endif
                </div>

                <!-- Bottom action info -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="block text-slate-400 font-medium">Jam Operasional:</span>
                            <span class="font-bold text-slate-800">{{ $facility->open_time }} - {{ $facility->close_time }} WIB</span>
                        </div>
                        <div>
                            <span class="block text-slate-400 font-medium">Durasi Maksimal:</span>
                            <span class="font-bold text-slate-800">{{ $facility->max_duration_minutes }} menit ({{ $facility->max_duration_minutes / 60 }} jam)</span>
                        </div>
                    </div>

                    <a href="{{ route('bookings.create', $facility->id) }}"
                        class="py-3 px-6 rounded-xl bg-gradient-to-r from-brand-dark to-brand-primary text-white font-bold text-sm shadow-md hover:opacity-95 transition flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Ajukan Peminjaman
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kalender Ketersediaan (FR-20 s.d FR-24) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 mb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center">
                    <i class="fa-regular fa-calendar-check text-brand-primary mr-2"></i>Kalender Ketersediaan Jadwal
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Klik pada tanggal atau slot kosong untuk mengajukan peminjaman secara langsung.
                </p>
            </div>

            <!-- Legend Status Jadwal -->
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-slate-600 font-medium">Disetujui (Terisi)</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-slate-600 font-medium">Diajukan (Antrean)</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-slate-600"></span>
                    <span class="text-slate-600 font-medium">Blokir Admin</span>
                </div>
            </div>
        </div>

        <!-- FullCalendar Container Element -->
        <div id="calendar" class="min-h-[600px]"></div>
    </div>
</div>

<!-- Modal Detail Event Kalender (FR-23) -->
<div id="eventDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <span id="modalTypeBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold"></span>
            <button onclick="closeEventModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>
        <div class="mt-4 space-y-3 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Nama Kegiatan:</span>
                <p id="modalEventName" class="text-sm font-bold text-slate-800 mt-0.5"></p>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Peminjam / Ormawa:</span>
                <p id="modalOrganizer" class="text-xs font-semibold text-brand-dark mt-0.5"></p>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Waktu Peminjaman:</span>
                <p id="modalTime" class="text-xs font-medium text-slate-700 mt-0.5"></p>
            </div>
        </div>
        <div class="mt-6 text-right">
            <button onclick="closeEventModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function closeEventModal() {
        document.getElementById('eventDetailModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const facilityId = {{ $facility->id }};
        const openTime = "{{ $facility->open_time }}";
        const closeTime = "{{ $facility->close_time }}";

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            slotMinTime: openTime + ':00',
            slotMaxTime: closeTime + ':00',
            allDaySlot: false,
            events: "{{ route('facilities.calendar', $facility->id) }}",
            
            // Klik pada slot/tanggal kosong (FR-22)
            dateClick: function(info) {
                let selectedDate = info.dateStr.split('T')[0];
                let startTime = openTime;
                if (info.dateStr.includes('T')) {
                    startTime = info.dateStr.split('T')[1].substring(0, 5);
                }
                
                // Hitung default jam selesai +2 jam
                let [h, m] = startTime.split(':').map(Number);
                let endH = Math.min(h + 2, parseInt(closeTime.split(':')[0]));
                let endTime = String(endH).padStart(2, '0') + ':' + String(m).padStart(2, '0');

                // Redirect ke form pengajuan dengan parameter terisi
                window.location.href = `{{ route('bookings.create', $facility->id) }}?date=${selectedDate}&start=${startTime}&end=${endTime}`;
            },

            // Klik event terisi untuk melihat detail (FR-23)
            eventClick: function(info) {
                const props = info.event.extendedProps;
                const modal = document.getElementById('eventDetailModal');
                const badge = document.getElementById('modalTypeBadge');

                if (props.type === 'DISETUJUI') {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
                    badge.innerText = 'DISETUJUI (TERISI)';
                    document.getElementById('modalEventName').innerText = props.eventName || info.event.title;
                    document.getElementById('modalOrganizer').innerText = props.organizer || '-';
                    document.getElementById('modalTime').innerText = props.time;
                } else if (props.type === 'BLOKIR') {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800';
                    badge.innerText = 'BLOKIR ADMIN';
                    document.getElementById('modalEventName').innerText = props.reason || info.event.title;
                    document.getElementById('modalOrganizer').innerText = 'Administrator Kampus';
                    document.getElementById('modalTime').innerText = props.time;
                } else {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                    badge.innerText = 'DIAJUKAN (MENUNGGU)';
                    document.getElementById('modalEventName').innerText = props.eventName || info.event.title;
                    document.getElementById('modalOrganizer').innerText = props.organizer || '-';
                    document.getElementById('modalTime').innerText = props.time;
                }

                modal.classList.remove('hidden');
            }
        });

        calendar.render();
    });
</script>
@endpush
