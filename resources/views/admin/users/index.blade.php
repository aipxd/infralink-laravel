@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Pengguna & Hak Akses (RBAC)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola pengguna terdaftar, tetapkan peran (Pemohon, Approver, Admin), dan status akun (FR-63)</p>
        </div>
    </div>

    <!-- Filter Pencarian Pengguna -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-64">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, NIM..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
            </div>

            <div class="w-full sm:w-44">
                <select name="role" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-primary outline-hidden bg-white">
                    <option value="">Semua Peran</option>
                    <option value="PEMOHON" {{ request('role') === 'PEMOHON' ? 'selected' : '' }}>PEMOHON</option>
                    <option value="APPROVER" {{ request('role') === 'APPROVER' ? 'selected' : '' }}>APPROVER</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition">
                <i class="fa-solid fa-magnifying-glass mr-1"></i>Cari
            </button>

            @if(request()->hasAny(['q', 'role']))
                <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 hover:bg-slate-50">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Email</th>
                        <th class="px-5 py-3.5">NIM / NIP</th>
                        <th class="px-5 py-3.5">Ormawa / Unit</th>
                        <th class="px-5 py-3.5">Peran (Role)</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800 block text-sm">{{ $u->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ $u->email }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-700">{{ $u->identity_number ?: '-' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-slate-700">{{ $u->unit_name ?: '-' }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.users.update-role', $u->id) }}" method="POST" class="inline-flex items-center">
                                @csrf
                                <select name="role" onchange="this.form.submit()" class="px-2.5 py-1 rounded-lg border text-xs font-bold outline-hidden cursor-pointer
                                    {{ $u->role === 'ADMIN' ? 'bg-purple-50 text-purple-700 border-purple-200' : ($u->role === 'APPROVER' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                    <option value="PEMOHON" {{ $u->role === 'PEMOHON' ? 'selected' : '' }}>PEMOHON</option>
                                    <option value="APPROVER" {{ $u->role === 'APPROVER' ? 'selected' : '' }}>APPROVER</option>
                                    <option value="ADMIN" {{ $u->role === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($u->is_active)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-right">
                            @if($u->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg border {{ $u->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }} font-bold text-[11px]">
                                    {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @else
                            <span class="text-[11px] text-slate-400 italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
