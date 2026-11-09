<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - Infralink Kampus</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">

    <!-- Tailwind CSS CDN with Infralink Brand Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            dark: '#0B4F5C',
                            primary: '#0B7A8C',
                            green: '#17855F',
                            lime: '#4DA843',
                            surface: '#EDF6F4',
                            light: '#F4FAF8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .fc-event { cursor: pointer; border-radius: 6px; padding: 2px 4px; font-weight: 500; font-size: 0.825rem; }
        .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700 !important; color: #0B4F5C; }
        .fc-button-primary { background-color: #0B7A8C !important; border-color: #0B7A8C !important; }
        .fc-button-primary:hover { background-color: #0B4F5C !important; border-color: #0B4F5C !important; }
        .fc-button-active { background-color: #0B4F5C !important; border-color: #0B4F5C !important; }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased">

    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Brand Logo & Main Nav -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('facilities.index') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-dark to-brand-green flex items-center justify-center text-white shadow-md shadow-brand-primary/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-building-columns text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-brand-dark via-brand-primary to-brand-green bg-clip-text text-transparent">INFRALINK</span>
                            <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold -mt-1">Sistem Sarpras Kampus</span>
                        </div>
                    </a>

                    @auth
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('facilities.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('facilities.*') && !request()->routeIs('facilities.create') ? 'bg-brand-surface text-brand-dark font-bold' : 'text-slate-600 hover:text-brand-primary hover:bg-slate-50' }}">
                            <i class="fa-solid fa-compass mr-1.5 text-xs text-brand-primary"></i>Katalog Fasilitas
                        </a>

                        <a href="{{ route('bookings.mine') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('bookings.mine') ? 'bg-brand-surface text-brand-dark font-bold' : 'text-slate-600 hover:text-brand-primary hover:bg-slate-50' }}">
                            <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs text-brand-primary"></i>Peminjaman Saya
                        </a>

                        @if(auth()->user()->isApprover())
                        @php
                            $pendingCount = \App\Models\Booking::where('status', 'DIAJUKAN')->count();
                        @endphp
                        <a href="{{ route('approvals.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors relative {{ request()->routeIs('approvals.*') ? 'bg-brand-surface text-brand-dark font-bold' : 'text-slate-600 hover:text-brand-primary hover:bg-slate-50' }}">
                            <i class="fa-solid fa-clipboard-check mr-1.5 text-xs text-brand-primary"></i>Antrean Approval
                            @if($pendingCount > 0)
                                <span class="ml-1.5 px-2 py-0.5 text-xs font-bold rounded-full bg-amber-500 text-white animate-pulse">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <div class="relative group" x-data="{ open: false }">
                            <button id="adminDropdownBtn" class="flex items-center px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('admin.*') ? 'bg-brand-surface text-brand-dark font-bold' : 'text-slate-600 hover:text-brand-primary hover:bg-slate-50' }}">
                                <i class="fa-solid fa-screwdriver-wrench mr-1.5 text-xs text-brand-primary"></i>Panel Admin
                                <i class="fa-solid fa-chevron-down ml-1.5 text-[10px]"></i>
                            </button>
                            <div id="adminDropdownMenu" class="hidden absolute left-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                    <i class="fa-solid fa-chart-pie w-5 text-slate-400"></i>Dashboard Ringkasan
                                </a>
                                <a href="{{ route('admin.facilities.index') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                    <i class="fa-solid fa-door-open w-5 text-slate-400"></i>Kelola Fasilitas
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                    <i class="fa-solid fa-tags w-5 text-slate-400"></i>Kategori Fasilitas
                                </a>
                                <a href="{{ route('admin.blocked-slots.index') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                    <i class="fa-solid fa-calendar-xmark w-5 text-slate-400"></i>Blokir Jadwal
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                    <i class="fa-solid fa-users w-5 text-slate-400"></i>Kelola Pengguna
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endauth
                </div>

                <!-- Right: User Menu & Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                        <div class="flex items-center justify-end space-x-1.5 mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide
                                {{ auth()->user()->role === 'ADMIN' ? 'bg-purple-100 text-purple-700' : (auth()->user()->role === 'APPROVER' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ auth()->user()->role }}
                            </span>
                            @if(auth()->user()->unit_name)
                                <span class="text-[11px] text-slate-400 truncate max-w-[130px]">• {{ auth()->user()->unit_name }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="relative">
                        <button id="userProfileBtn" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 border border-slate-200 text-brand-dark font-bold hover:bg-brand-surface transition-colors">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </button>
                        <div id="userProfileMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-brand-surface hover:text-brand-dark">
                                <i class="fa-solid fa-user-gear w-5 text-slate-400"></i>Profil & Sandi
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-5"></i>Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-brand-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg text-sm font-bold text-white bg-brand-primary hover:bg-brand-dark transition-colors shadow-sm">Daftar Akun</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Toggle -->
        @auth
        <div class="md:hidden border-t border-slate-100 px-4 py-2 flex items-center justify-around text-xs font-semibold text-slate-600">
            <a href="{{ route('facilities.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('facilities.*') ? 'text-brand-primary font-bold' : '' }}">
                <i class="fa-solid fa-compass text-base mb-0.5"></i>Katalog
            </a>
            <a href="{{ route('bookings.mine') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('bookings.mine') ? 'text-brand-primary font-bold' : '' }}">
                <i class="fa-solid fa-clock-rotate-left text-base mb-0.5"></i>Peminjaman
            </a>
            @if(auth()->user()->isApprover())
            <a href="{{ route('approvals.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('approvals.*') ? 'text-brand-primary font-bold' : '' }}">
                <i class="fa-solid fa-clipboard-check text-base mb-0.5"></i>Approval
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.*') ? 'text-brand-primary font-bold' : '' }}">
                <i class="fa-solid fa-gear text-base mb-0.5"></i>Admin
            </a>
            @endif
        </div>
        @endauth
    </nav>

    <!-- Global Alerts / Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
        <div class="flex items-center p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs" role="alert">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg mr-3"></i>
            <div class="text-sm font-medium">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs" role="alert">
            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg mr-3"></i>
            <div class="text-sm font-medium">{{ session('error') }}</div>
        </div>
        @endif

        @if(isset($errors) && $errors->any())
        <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-xs">
            <div class="flex items-center mb-1">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg mr-2"></i>
                <span class="text-sm font-bold">Harap periksa kesalahan berikut:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1 ml-6 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-brand-dark">INFRALINK</span>
                <span>• Sistem Manajemen Peminjaman Sarpras Kampus (MVP)</span>
            </div>
            <div class="text-slate-400">
                Dikembangkan dengan PHP 8.2 & Laravel 11 • Kelompok 1
            </div>
        </div>
    </footer>

    <!-- FullCalendar JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <!-- Dropdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Admin Dropdown Toggle
            const adminBtn = document.getElementById('adminDropdownBtn');
            const adminMenu = document.getElementById('adminDropdownMenu');
            if (adminBtn && adminMenu) {
                adminBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    adminMenu.classList.toggle('hidden');
                });
            }

            // User Profile Dropdown Toggle
            const userBtn = document.getElementById('userProfileBtn');
            const userMenu = document.getElementById('userProfileMenu');
            if (userBtn && userMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
            }

            // Close dropdowns on outside click
            document.addEventListener('click', function() {
                if (adminMenu) adminMenu.classList.add('hidden');
                if (userMenu) userMenu.classList.add('hidden');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
