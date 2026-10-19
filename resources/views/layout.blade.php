<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir App - Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Smooth floating sidebar dock styles */
        #sidebarFloatingDock {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, background-color 0.3s ease;
            will-change: transform;
        }
        #sidebarFloatingDock.is-floating {
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            box-shadow: 0 16px 36px -10px rgba(59, 130, 246, 0.16), 0 0 0 1px rgba(226, 232, 240, 0.85);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    </style>
</head>
<body class="{{ auth()->check() ? 'bg-gradient-to-br from-[#EEF2FF] via-[#F4F6FC] to-[#E0E7FF]' : 'bg-gradient-to-br from-[#5A63F7] via-[#6B73FF] to-[#8089FF]' }} text-slate-800 min-h-screen relative overflow-x-hidden selection:bg-indigo-600 selection:text-white">
    <!-- Ambient glowing backdrop orbs -->
    <div class="pointer-events-none fixed -top-40 -left-40 w-[500px] h-[500px] bg-blue-400/15 rounded-full blur-[100px] z-0"></div>
    <div class="pointer-events-none fixed -bottom-40 -right-40 w-[500px] h-[500px] bg-indigo-400/15 rounded-full blur-[100px] z-0"></div>

    @auth
        <!-- Mobile Sidebar Backdrop (Fixed full viewport, high z-index) -->
        <div id="sidebarBackdrop" onclick="closeMobileSidebar()" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Mobile Navigation Drawer (Fixed full viewport height, slides in from left) -->
        <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col border-r border-slate-100 bg-white p-5 shadow-2xl transition-transform duration-300 ease-in-out lg:hidden rounded-r-3xl">
            <!-- Header Brand & Close Button -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-blue-500/25">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-extrabold tracking-wider text-slate-900 block leading-tight">WARKOP</span>
                        <span class="text-[9px] text-blue-600 font-bold uppercase tracking-wider block">Sistem Kasir</span>
                    </div>
                </div>
                <button id="sidebarCloseBtn" type="button" onclick="closeMobileSidebar()" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition cursor-pointer" aria-label="Tutup Menu">
                    <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- User Profile Summary -->
            <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-2xl flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-500/10 to-indigo-500/15 border border-blue-200/60 flex items-center justify-center text-blue-600 font-bold shrink-0">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->isAdmin())
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[10px] font-bold">Administrator</span>
                    @else
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-bold">Kasir (POS)</span>
                    @endif
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="my-5 flex flex-col gap-1.5 flex-1 overflow-y-auto">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                            <path d="m3.3 7 8.7 5 8.7-5"/>
                            <path d="M12 22V12"/>
                        </svg>
                        <span class="text-sm">Katalog Produk</span>
                    </a>
                @endif

                <a href="{{ route('pos.index') }}" 
                   class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span class="text-sm">Point of Sale (Kasir)</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('reports.index') }}" 
                       class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="M18 17V9"/>
                            <path d="M13 17V5"/>
                            <path d="M8 17v-3"/>
                        </svg>
                        <span class="text-sm">Laporan Penjualan</span>
                    </a>

                    <a href="{{ route('cashiers.index') }}" 
                       class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('cashiers.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="text-sm">Kelola Kasir</span>
                    </a>
                @endif

                <a href="{{ route('transactions.index') }}" 
                   class="flex items-center gap-3 w-full p-3 rounded-2xl transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/80 font-medium' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span class="text-sm">Riwayat Transaksi</span>
                </a>
            </nav>

            <!-- Logout Button -->
            <div class="pt-4 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="flex items-center gap-3 w-full p-3 rounded-2xl text-rose-600 hover:bg-rose-50 font-semibold transition-all duration-200 cursor-pointer">
                        <svg class="w-5 h-5 shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                            <line x1="12" y1="2" x2="12" y2="12"/>
                        </svg>
                        <span class="text-sm">Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <div id="adminShell" class="relative z-10 p-1 sm:p-4 lg:p-6 min-h-screen flex items-start justify-center">

            <!-- Main Floating Rounded Container -->
            <div class="w-full max-w-[1580px] bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-[36px] shadow-[0_20px_50px_rgba(79,70,229,0.08)] border border-white ring-1 ring-slate-900/5 flex flex-col lg:flex-row min-h-[92vh] relative">
                
                <!-- Left Vertical Icon Dock Sidebar (Full-height column with sticky floating dock) -->
                <aside id="desktopSidebarDock" class="hidden lg:flex w-20 flex-col border-r border-slate-100 bg-slate-50/60 p-2.5 relative z-40 rounded-l-[36px]">
                    <!-- Floating Dock: Follows page scroll dynamically on desktop -->
                    <div id="sidebarFloatingDock" class="w-full flex-1 flex flex-col justify-between items-center lg:sticky lg:top-6 lg:self-start lg:h-[calc(100vh-3rem)] lg:max-h-[640px] px-1 py-1 relative z-40">
                        <!-- Top section: Avatar / Brand -->
                    <div class="flex flex-col items-center w-full">
                        <div class="flex items-center justify-between w-full lg:justify-center">
                            <!-- Mobile Brand on Drawer Header -->
                            <div class="flex items-center gap-2.5 lg:hidden">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-blue-500/25">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-extrabold tracking-wider text-slate-900 block leading-tight">WARKOP</span>
                                    <span class="text-[9px] text-blue-600 font-bold uppercase tracking-wider block">Sistem Kasir</span>
                                </div>
                            </div>

                            <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('pos.index') }}" class="hidden lg:flex group relative items-center justify-center" title="{{ auth()->user()->isAdmin() ? 'Dashboard' : 'Kasir / POS' }}">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500/10 to-indigo-500/15 border border-blue-200/60 flex items-center justify-center text-blue-600 font-bold transition-all group-hover:scale-105 group-hover:border-blue-400">
                                    <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                        {{ auth()->user()->name ?? 'Profil' }}
                                    </span>
                                </span>
                            </a>
                            <!-- Close button for mobile -->
                            <button id="sidebarCloseBtn" type="button" class="lg:hidden p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-100 transition" aria-label="Tutup Menu">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="lg:hidden mt-3.5 pb-3 border-b border-slate-100 w-full text-left bg-slate-50 p-3 rounded-xl">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            @if(auth()->user()->isAdmin())
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[10px] font-bold">Administrator</span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-bold">Kasir (POS)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Middle Navigation Icons -->
                    <nav class="my-6 flex flex-col items-center gap-3 w-full">
                        @if(auth()->user()->isAdmin())
                            <!-- 1. Dashboard (Home) -->
                            <a href="{{ route('dashboard') }}" 
                               class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                               title="Dashboard">
                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <span class="text-sm lg:hidden font-medium">Dashboard</span>
                                <!-- Desktop Tooltip -->
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                        Dashboard
                                    </span>
                                </span>
                            </a>

                            <!-- 2. Produk (Catalog / Box) -->
                            <a href="{{ route('products.index') }}" 
                               class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                               title="Produk">
                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                                    <path d="m3.3 7 8.7 5 8.7-5"/>
                                    <path d="M12 22V12"/>
                                </svg>
                                <span class="text-sm lg:hidden font-medium">Produk</span>
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                        Produk
                                    </span>
                                </span>
                            </a>
                        @endif

                        <!-- 3. POS / Transaksi Baru (Shopping Bag) -->
                        <a href="{{ route('pos.index') }}" 
                           class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('pos.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                           title="Point of Sale (Kasir)">
                            <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                <path d="M3 6h18"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            <span class="text-sm lg:hidden font-medium">Point of Sale (Kasir)</span>
                            <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                    <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                    Kasir / POS
                                </span>
                            </span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <!-- 4. Laporan / Analytics (Bar Chart) -->
                            <a href="{{ route('reports.index') }}" 
                               class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                               title="Laporan Penjualan">
                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 3v18h18"/>
                                    <path d="M18 17V9"/>
                                    <path d="M13 17V5"/>
                                    <path d="M8 17v-3"/>
                                </svg>
                                <span class="text-sm lg:hidden font-medium">Laporan Penjualan</span>
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                        Laporan
                                    </span>
                                </span>
                            </a>
                        @endif

                        <!-- 5. Riwayat Transaksi (Receipt / Chat) -->
                        <a href="{{ route('transactions.index') }}" 
                           class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('transactions.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                           title="Riwayat Transaksi">
                            <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <span class="text-sm lg:hidden font-medium">Riwayat Transaksi</span>
                            <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                    <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                    Transaksi
                                </span>
                            </span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <!-- 6. Kelola Kasir (Users Icon) -->
                            <a href="{{ route('cashiers.index') }}" 
                               class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl transition-all duration-200 lg:justify-center {{ request()->routeIs('cashiers.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/35 font-semibold' : 'text-slate-400 hover:text-blue-600 hover:bg-blue-50/80' }}"
                               title="Kelola Kasir">
                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <span class="text-sm lg:hidden font-medium">Kelola Kasir</span>
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-slate-900 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-white/10">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-slate-900 rotate-45"></span>
                                        Kelola Kasir
                                    </span>
                                </span>
                            </a>
                        @endif
                    </nav>

                    <!-- Bottom section: Logout / Power Button -->
                    <div class="flex flex-col items-center w-full pt-4 border-t border-slate-100">
                        <form action="{{ route('logout') }}" method="POST" class="w-full flex justify-center">
                            @csrf
                            <button type="submit" 
                                    class="group relative flex items-center gap-3 w-full lg:w-12 lg:h-12 p-3 lg:p-0 rounded-2xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all duration-200 lg:justify-center"
                                    title="Keluar">
                                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                    <line x1="12" y1="2" x2="12" y2="12"/>
                                </svg>
                                <span class="text-sm lg:hidden font-medium text-rose-600">Keluar</span>
                                <span class="hidden lg:flex items-center absolute left-14 top-1/2 -translate-y-1/2 pl-2 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-200 z-50">
                                    <span class="relative px-3 py-1.5 bg-rose-600 text-white text-xs font-semibold rounded-xl whitespace-nowrap shadow-xl ring-1 ring-rose-500/20">
                                        <span class="absolute -left-1 top-1/2 -translate-y-1/2 w-2 h-2 bg-rose-600 rotate-45"></span>
                                        Keluar
                                    </span>
                                </span>
                            </button>
                        </form>
                    </div>
                    </div>
                </aside>

                <!-- Right Main Content Area -->
                <div class="flex-1 flex flex-col min-w-0 bg-white rounded-2xl sm:rounded-r-[36px] lg:rounded-l-none">
                    <!-- Top Modern Header Bar (Sticky at top of content area) -->
                    <header class="sticky top-0 z-20 flex items-center justify-between px-3.5 py-2.5 sm:px-8 sm:py-5 border-b border-slate-100 bg-white/95 backdrop-blur-md rounded-t-2xl sm:rounded-none sm:rounded-tr-[36px]">
                        <!-- Left: Search Box (compact on mobile) -->
                        <div class="flex items-center gap-2 sm:gap-3 flex-1 max-w-xs sm:max-w-md">
                            <!-- Mobile menu trigger -->
                            <button id="sidebarToggle" type="button" onclick="openMobileSidebar()" class="lg:hidden p-2 sm:p-2.5 rounded-xl sm:rounded-2xl border border-slate-200/80 text-slate-600 hover:bg-slate-50 active:scale-95 transition shrink-0 cursor-pointer" aria-label="Buka Menu">
                                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>

                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </span>
                                <input type="text" 
                                       placeholder="Cari transaksi, produk..." 
                                       class="w-full pl-9 pr-3 py-1.5 sm:py-2 text-xs sm:text-sm bg-slate-50 border border-slate-100 rounded-full text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white focus:border-blue-200 transition-all">
                            </div>
                        </div>

                        <!-- Right: Actions & Profile (Fully Functional Interactive Dropdowns) -->
                        <div class="flex items-center gap-1.5 sm:gap-3 pl-2 sm:pl-4 relative">
                            <!-- 1. Smile / Help Button & Dropdown -->
                            <div class="relative">
                                <button id="helpBtn" type="button" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition" title="Informasi & Bantuan">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                                    </svg>
                                </button>
                                <!-- Help Dropdown -->
                                <div id="helpDropdown" class="hidden absolute right-0 mt-3 w-[calc(100vw-2rem)] max-w-sm sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 sm:p-5 z-50">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">💡</span>
                                            <div>
                                                <h3 class="text-sm font-bold text-slate-900">Bantuan & Panduan Kasir</h3>
                                                <p class="text-[11px] text-slate-400">Sistem Kasir Modern</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold">Online</span>
                                    </div>
                                    <div class="mt-3 space-y-2.5 text-xs">
                                        <a href="{{ route('pos.index') }}" class="block p-3 rounded-xl bg-blue-50/70 border border-blue-100/80 hover:bg-blue-100/70 transition">
                                            <p class="font-bold text-blue-900 flex items-center gap-1.5">
                                                <span>🛒</span> Menu Kasir / Point of Sale
                                            </p>
                                            <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                                                Pilih produk, sesuaikan jumlah, hitung PPN otomatis, dan cetak struk pembayaran.
                                            </p>
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('products.index') }}" class="block p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition">
                                                <p class="font-bold text-slate-800 flex items-center gap-1.5">
                                                    <span>📦</span> Inventaris & Stok Barang
                                                </p>
                                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                                    Tambah produk baru, kelola harga modal/jual, serta pantau stok menipis.
                                                </p>
                                            </a>
                                            <a href="{{ route('reports.index') }}" class="block p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100 transition">
                                                <p class="font-bold text-slate-800 flex items-center gap-1.5">
                                                    <span>📊</span> Rekap & Laporan Omset
                                                </p>
                                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                                    Filter tanggal untuk analisis omset harian dan unduh/pantau rekapitulasi penjualan.
                                                </p>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Notification Bell & Dropdown -->
                            <div class="relative">
                                <button id="notifBtn" type="button" class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-slate-100 transition" title="Notifikasi Toko">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                    </svg>
                                    <span id="notifDot" class="absolute top-1 right-1 w-2 sm:w-2.5 h-2 sm:h-2.5 bg-blue-600 rounded-full ring-2 ring-white"></span>
                                </button>
                                <!-- Notification Dropdown -->
                                <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-[calc(100vw-2rem)] max-w-sm sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                                    <div class="flex items-center justify-between p-3.5 sm:p-4 border-b border-slate-100 bg-slate-50/70">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-900">Notifikasi Toko</h3>
                                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold">Terbaru</span>
                                        </div>
                                        <button type="button" id="markReadBtn" class="text-[11px] text-blue-600 font-semibold hover:underline">
                                            Tandai dibaca
                                        </button>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                        @php
                                            $notifLowStock = \App\Models\Product::where('stock', '<=', 5)->take(3)->get();
                                            $notifLatestTx = \App\Models\Transaction::latest()->take(2)->get();
                                        @endphp

                                        @foreach($notifLowStock as $lowProd)
                                            <a href="{{ auth()->user()->isAdmin() ? route('products.index', ['edit' => $lowProd->id]) : route('pos.index') }}" class="flex items-start gap-3 p-3.5 hover:bg-amber-50/60 transition">
                                                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-sm font-bold">
                                                    ⚠️
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-900 truncate">Stok Menipis: {{ $lowProd->name }}</p>
                                                    <p class="text-[11px] text-amber-800 mt-0.5">Tersisa hanya <span class="font-bold">{{ $lowProd->stock }} unit</span>! {{ auth()->user()->isAdmin() ? 'Klik untuk tambah stok.' : 'Laporkan ke admin.' }}</p>
                                                </div>
                                            </a>
                                        @endforeach

                                        @foreach($notifLatestTx as $latestTx)
                                            <a href="{{ route('transactions.show', $latestTx) }}" class="flex items-start gap-3 p-3.5 hover:bg-blue-50/60 transition">
                                                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-sm font-bold">
                                                    ✅
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-900 truncate">Transaksi Berhasil</p>
                                                    <p class="text-[11px] text-slate-600 mt-0.5">Kasir &bull; Rp {{ number_format($latestTx->total, 0, ',', '.') }}</p>
                                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $latestTx->created_at ? $latestTx->created_at->diffForHumans() : 'baru saja' }}</p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="p-3 bg-slate-50 text-center border-t border-slate-100">
                                        <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                                            Buka Riwayat Semua Transaksi &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Profile Avatar & Dropdown -->
                            <div class="relative">
                                <button id="profileBtn" type="button" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full overflow-hidden ring-2 ring-blue-100/90 shadow-sm hover:ring-blue-400 transition shrink-0 block focus:outline-none" title="{{ auth()->user()->name }}">
                                    <img src="{{ asset('images/avatar.svg') }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                </button>
                                <!-- Profile Dropdown -->
                                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-[calc(100vw-2rem)] max-w-xs sm:w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2.5 z-50">
                                    <div class="p-3 bg-slate-50 rounded-xl mb-2 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full overflow-hidden ring-1 ring-blue-200 shrink-0">
                                            <img src="{{ asset('images/avatar.svg') }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                            <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                            @if(auth()->user()->isAdmin())
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[10px] font-bold">Administrator</span>
                                            @else
                                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-bold">Kasir (POS)</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-0.5 text-xs font-semibold text-slate-700">
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                Dashboard
                                            </a>
                                        @endif
                                        <a href="{{ route('pos.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            Kasir (POS)
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                Kelola Produk
                                            </a>
                                            <a href="{{ route('cashiers.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                Kelola Kasir
                                            </a>
                                            <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                Laporan Penjualan
                                            </a>
                                        @endif
                                        <a href="{{ route('transactions.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Riwayat Transaksi
                                        </a>
                                    </div>

                                    <div class="mt-2 pt-2 border-t border-slate-100">
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                Keluar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Page Body Content -->
                    <main class="flex-1 p-3.5 sm:p-7 lg:p-8">
                        @if(session('success'))
                            <div class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 px-5 py-3.5 text-sm font-medium shadow-sm">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 flex items-center gap-3 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 px-5 py-3.5 text-sm font-medium shadow-sm">
                                <svg class="w-5 h-5 text-rose-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 p-5 text-sm shadow-sm">
                                <p class="font-bold mb-2">Terjadi kesalahan input:</p>
                                <ul class="list-disc pl-5 space-y-1 text-rose-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    @else
        <!-- Guest View (Login) -->
        <main class="min-h-screen flex items-center justify-center p-2 sm:p-4">
            @yield('content')
        </main>
    @endauth

    <script>
        // Global mobile drawer toggle functions
        window.openMobileSidebar = function () {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }
        };

        window.closeMobileSidebar = function () {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const helpBtn = document.getElementById('helpBtn');
            const helpDropdown = document.getElementById('helpDropdown');
            const notifBtn = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifDot = document.getElementById('notifDot');
            const markReadBtn = document.getElementById('markReadBtn');
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            const closeAllHeaderDropdowns = function () {
                if (helpDropdown) helpDropdown.classList.add('hidden');
                if (notifDropdown) notifDropdown.classList.add('hidden');
                if (profileDropdown) profileDropdown.classList.add('hidden');
            };

            if (helpBtn && helpDropdown) {
                helpBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = helpDropdown.classList.contains('hidden');
                    closeAllHeaderDropdowns();
                    if (isHidden) helpDropdown.classList.remove('hidden');
                });
            }

            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = notifDropdown.classList.contains('hidden');
                    closeAllHeaderDropdowns();
                    if (isHidden) {
                        notifDropdown.classList.remove('hidden');
                        if (notifDot) notifDot.classList.add('hidden');
                    }
                });
            }

            if (markReadBtn && notifDot) {
                markReadBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    notifDot.classList.add('hidden');
                });
            }

            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = profileDropdown.classList.contains('hidden');
                    closeAllHeaderDropdowns();
                    if (isHidden) profileDropdown.classList.remove('hidden');
                });
            }

            // Click outside to close
            document.addEventListener('click', function (e) {
                if (!e.target.closest('#helpDropdown') && !e.target.closest('#helpBtn') &&
                    !e.target.closest('#notifDropdown') && !e.target.closest('#notifBtn') &&
                    !e.target.closest('#profileDropdown') && !e.target.closest('#profileBtn')) {
                    closeAllHeaderDropdowns();
                }
            });

            // ESC key to close
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeAllHeaderDropdowns();
                    window.closeMobileSidebar();
                }
            });

            // Mobile Sidebar Drawer Navigation Listeners
            const sidebarToggle = document.getElementById('sidebarToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    window.openMobileSidebar();
                });
            }

            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    window.closeMobileSidebar();
                });
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.addEventListener('click', function () {
                    window.closeMobileSidebar();
                });
            }

            // Auto close mobile drawer when clicking any link inside it
            if (adminSidebar) {
                adminSidebar.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 1024) {
                            window.closeMobileSidebar();
                        }
                    });
                });
            }

            // Sticky Floating Sidebar Dock Scroll Motion
            const floatingDock = document.getElementById('sidebarFloatingDock');
            if (floatingDock) {
                let lastScrollY = window.scrollY;
                let ticking = false;
                let currentOffset = 0;
                let targetOffset = 0;

                const updateFloatingDock = function () {
                    if (window.innerWidth >= 1024) {
                        const scrollY = window.scrollY;
                        const delta = scrollY - lastScrollY;

                        // Subtle inertial movement during scroll: responds dynamically to scroll direction
                        targetOffset = Math.max(-8, Math.min(8, delta * 0.25));
                        currentOffset += (targetOffset - currentOffset) * 0.2;

                        if (scrollY > 30) {
                            floatingDock.classList.add('is-floating');
                        } else {
                            floatingDock.classList.remove('is-floating');
                        }

                        floatingDock.style.transform = `translateY(${currentOffset.toFixed(2)}px)`;

                        targetOffset *= 0.8;
                        if (Math.abs(currentOffset) > 0.05 || Math.abs(targetOffset) > 0.05) {
                            requestAnimationFrame(updateFloatingDock);
                            return;
                        } else {
                            floatingDock.style.transform = 'translateY(0px)';
                        }
                    } else {
                        floatingDock.style.transform = 'none';
                        floatingDock.classList.remove('is-floating');
                    }
                    ticking = false;
                    lastScrollY = window.scrollY;
                };

                window.addEventListener('scroll', function () {
                    if (!ticking) {
                        requestAnimationFrame(updateFloatingDock);
                        ticking = true;
                    }
                }, { passive: true });
            }
        });
    </script>
</body>
</html>
