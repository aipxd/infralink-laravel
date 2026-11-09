@extends('layouts.app')

@section('title', 'Blokir Jadwal Fasilitas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Blokir Jadwal (Maintenance & Libur)</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Blokir slot waktu fasilitas tertentu agar tidak dapat diajukan oleh pemohon (FR-62)</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Form Tambah Blokir (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center">
                <i class="fa-solid fa-calendar-xmark text-rose-500 mr-2"></i>Buat Blokir Jadwal Baru
            </h3>
            <form action="{{ route('admin.blocked-slots.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Fasilitas *</label>
                    <select name="facility_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                        <option value="">Pilih Fasilitas</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} ({{ $facility->building }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Blokir *</label>
                    <input type="date" name="block_date" value="{{ old('block_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam Mulai *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam Selesai *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pemblokiran *</label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Perbaikan AC Sentral / Libur Nasional Dies Natalis"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden">{{ old('reason') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition shadow-xs">
                    Simpan Blokir Jadwal
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Blokir Jadwal (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Daftar Blokir Aktif</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="px-5 py-3.5">Fasilitas</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Rentang Jam</th>
                            <th class="px-5 py-3.5">Alasan</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($blockedSlots as $slot)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 font-bold text-slate-800">
                                {{ $slot->facility->name }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $slot->block_date->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-bold text-slate-700">
                                {{ $slot->start_time }} - {{ $slot->end_time }} WIB
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-slate-700 block">{{ $slot->reason }}</span>
                                <span class="text-[10px] text-slate-400">Oleh: {{ $slot->creator->name }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <form action="{{ route('admin.blocked-slots.destroy', $slot->id) }}" method="POST" class="inline" onsubmit="return confirm('Cabut blokir jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-[11px]">
                                        Cabut Blokir
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 italic">
                                Belum ada jadwal yang diblokir.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $blockedSlots->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
