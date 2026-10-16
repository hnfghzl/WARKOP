@extends('layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-7">
    <!-- Greeting & Filter Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                Selamat Datang, {{ auth()->user()->name ?? 'Admin' }} ! <span class="inline-block animate-bounce">👋</span>
            </h1>
            <p class="mt-1 text-sm text-slate-400 font-medium">
                Berikut ringkasan performa dan penjualan toko Anda (Periode: <span class="font-bold text-blue-600">{{ $periodLabel }}</span>).
            </p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <!-- Filter Rentang Waktu Dropdown -->
            <div class="relative">
                <button type="button" 
                        onclick="const m = document.getElementById('periodMenu'); m.classList.toggle('hidden');"
                        class="flex items-center gap-2 bg-white border border-slate-200/90 rounded-2xl px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm hover:border-blue-300">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>{{ $periodLabel }}</span>
                    <svg class="w-4 h-4 text-slate-400 ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="periodMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-40">
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'semua']) }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold {{ $period === 'semua' ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Semua Waktu</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-bold">Total</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'bulan-ini']) }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold {{ $period === 'bulan-ini' ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Bulan Ini ({{ now()->translatedFormat('M Y') }})</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'bulan-lalu']) }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold {{ $period === 'bulan-lalu' ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Bulan Lalu ({{ now()->subMonth()->translatedFormat('M Y') }})</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'minggu-ini']) }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold {{ $period === 'minggu-ini' ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Minggu Ini</span>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['period' => 'hari-ini']) }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold {{ $period === 'hari-ini' ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>Hari Ini</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Top KPI Metric Cards (100% Sinkron dengan Data Penjualan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Penjualan (Hero Blue Gradient Card) -->
        <div class="rounded-[28px] bg-gradient-to-br from-[#3B46F1] via-[#4352FF] to-[#5563FF] p-6 text-white shadow-xl shadow-blue-500/25 relative overflow-hidden flex flex-col justify-between min-h-[175px] group transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/35 hover:-translate-y-0.5">
            <!-- Sparkline wave background SVG -->
            <div class="absolute inset-x-0 bottom-0 pointer-events-none opacity-80 group-hover:opacity-100 transition-opacity">
                <svg viewBox="0 0 300 80" class="w-full h-20 text-white/20" preserveAspectRatio="none" fill="none">
                    <defs>
                        <linearGradient id="heroWaveGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,50 Q40,35 80,48 T160,25 T230,45 T300,15 L300,80 L0,80 Z" fill="url(#heroWaveGrad)"/>
                    <path d="M0,50 Q40,35 80,48 T160,25 T230,45 T300,15" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Top Row -->
            <div class="flex items-start justify-between relative z-10">
                <span class="text-xs font-semibold text-white/85 tracking-wide">Total Penjualan</span>
                <div class="w-9 h-9 rounded-full bg-white text-blue-600 font-extrabold flex items-center justify-center text-xs shadow-md shadow-blue-900/10">
                    Rp
                </div>
            </div>

            <!-- Middle Value & Bottom Trend -->
            <div class="relative z-10 pt-4">
                <div class="text-2xl sm:text-[28px] font-extrabold tracking-tight">
                    {{ $kpiData['revenue']['formatted'] }}
                </div>
                <div class="mt-2 flex items-center text-xs font-medium">
                    <span class="inline-flex items-center gap-0.5 font-bold {{ $kpiData['revenue']['growth_positive'] ? 'text-emerald-300' : 'text-rose-300' }}">
                        {{ $kpiData['revenue']['growth'] }}
                    </span>
                    <span class="ml-1.5 text-white/70">{{ $kpiData['revenue']['comparison'] }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pesanan -->
        <div class="rounded-[28px] bg-white p-6 border border-slate-100/90 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[175px] group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <!-- Sparkline wave background SVG -->
            <div class="absolute inset-x-0 bottom-0 pointer-events-none opacity-60 group-hover:opacity-90 transition-opacity">
                <svg viewBox="0 0 300 80" class="w-full h-16" preserveAspectRatio="none" fill="none">
                    <defs>
                        <linearGradient id="waveIndigo" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#818CF8" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#818CF8" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,60 Q50,45 100,55 T200,30 T300,45 L300,80 L0,80 Z" fill="url(#waveIndigo)"/>
                    <path d="M0,60 Q50,45 100,55 T200,30 T300,45" stroke="#818CF8" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Top Row -->
            <div class="flex items-start justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-500 tracking-wide">Total Pesanan</span>
                <div class="w-9 h-9 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
            </div>

            <!-- Middle Value & Bottom Trend -->
            <div class="relative z-10 pt-4">
                <div class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-slate-900">
                    {{ number_format($kpiData['orders']['value'], 0, ',', '.') }}
                </div>
                <div class="mt-2 flex items-center text-xs font-medium">
                    <span class="inline-flex items-center gap-0.5 font-bold {{ $kpiData['orders']['growth_positive'] ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $kpiData['orders']['growth'] }}
                    </span>
                    <span class="ml-1.5 text-slate-400">{{ $kpiData['orders']['comparison'] }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Pelanggan / Transaksi -->
        <div class="rounded-[28px] bg-white p-6 border border-slate-100/90 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[175px] group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <!-- Sparkline wave background SVG -->
            <div class="absolute inset-x-0 bottom-0 pointer-events-none opacity-60 group-hover:opacity-90 transition-opacity">
                <svg viewBox="0 0 300 80" class="w-full h-16" preserveAspectRatio="none" fill="none">
                    <defs>
                        <linearGradient id="wavePurple" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#C084FC" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#C084FC" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,55 Q50,65 110,40 T220,50 T300,35 L300,80 L0,80 Z" fill="url(#wavePurple)"/>
                    <path d="M0,55 Q50,65 110,40 T220,50 T300,35" stroke="#C084FC" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Top Row -->
            <div class="flex items-start justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-500 tracking-wide">Pelanggan Aktif</span>
                <div class="w-9 h-9 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
            </div>

            <!-- Middle Value & Bottom Trend -->
            <div class="relative z-10 pt-4">
                <div class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-slate-900">
                    {{ $kpiData['customers']['value'] }}
                </div>
                <div class="mt-2 flex items-center text-xs font-medium">
                    <span class="inline-flex items-center gap-0.5 font-bold text-emerald-500">
                        {{ $kpiData['customers']['growth'] }}
                    </span>
                    <span class="ml-1.5 text-slate-400">tercatat</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Rata-rata Transaksi -->
        <div class="rounded-[28px] bg-white p-6 border border-slate-100/90 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[175px] group transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <!-- Sparkline wave background SVG -->
            <div class="absolute inset-x-0 bottom-0 pointer-events-none opacity-60 group-hover:opacity-90 transition-opacity">
                <svg viewBox="0 0 300 80" class="w-full h-16" preserveAspectRatio="none" fill="none">
                    <defs>
                        <linearGradient id="waveEmerald" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#34D399" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#34D399" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,65 Q60,50 120,60 T220,38 T300,48 L300,80 L0,80 Z" fill="url(#waveEmerald)"/>
                    <path d="M0,65 Q60,50 120,60 T220,38 T300,48" stroke="#34D399" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Top Row -->
            <div class="flex items-start justify-between relative z-10">
                <span class="text-xs font-semibold text-slate-500 tracking-wide">Rata-rata Transaksi</span>
                <div class="w-9 h-9 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="21" r="1"/>
                        <circle cx="19" cy="21" r="1"/>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                    </svg>
                </div>
            </div>

            <!-- Middle Value & Bottom Trend -->
            <div class="relative z-10 pt-4">
                <div class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-slate-900">
                    {{ $kpiData['average_order']['formatted'] }}
                </div>
                <div class="mt-2 flex items-center text-xs font-medium">
                    <span class="inline-flex items-center gap-0.5 font-bold {{ $kpiData['average_order']['growth_positive'] ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ $kpiData['average_order']['growth'] }}
                    </span>
                    <span class="ml-1.5 text-slate-400">{{ $kpiData['average_order']['comparison'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Bar Chart & Donut Chart (Real Data) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Tren Penjualan (~65%) -->
        <div class="lg:col-span-8 rounded-[28px] bg-white p-6 sm:p-7 border border-slate-100 shadow-sm flex flex-col justify-between">
            <!-- Header -->
            <div class="flex items-center justify-between pb-6">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">
                        Tren Penjualan Harian
                    </h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">
                        Aktivitas penjualan per hari (Senin s/d Minggu)
                    </p>
                </div>
                <div class="relative">
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200/80 rounded-xl px-3 py-1.5 text-xs font-bold text-blue-700 shadow-sm">
                        <span>{{ $periodLabel }}</span>
                    </span>
                </div>
            </div>

            <!-- Bar Chart Component -->
            <div class="pt-4 pb-2">
                <div class="flex items-end gap-3 sm:gap-6 h-56 sm:h-64 border-b border-slate-100 px-2 sm:px-4 relative">
                    <!-- Dynamic Y-Axis Grid Lines & Labels -->
                    <div class="absolute inset-x-0 inset-y-0 flex flex-col justify-between pointer-events-none text-[11px] text-slate-300 font-medium pr-2">
                        <div class="border-b border-dashed border-slate-100 flex items-center justify-between">
                            <span>{{ $yScaleLabels['p100'] }}</span>
                        </div>
                        <div class="border-b border-dashed border-slate-100 flex items-center justify-between">
                            <span>{{ $yScaleLabels['p75'] }}</span>
                        </div>
                        <div class="border-b border-dashed border-slate-100 flex items-center justify-between">
                            <span>{{ $yScaleLabels['p50'] }}</span>
                        </div>
                        <div class="border-b border-dashed border-slate-100 flex items-center justify-between">
                            <span>{{ $yScaleLabels['p25'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ $yScaleLabels['p0'] }}</span>
                        </div>
                    </div>

                    <!-- Bars (SEN s/d MIN) -->
                    @foreach($weeklyChart as $bar)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative z-10">
                            <!-- Floating Tooltip on Hover -->
                            <div class="absolute -top-12 bg-slate-900 text-white text-[11px] font-semibold rounded-xl px-2.5 py-1.5 opacity-0 pointer-events-none group-hover:opacity-100 transition-all duration-200 transform -translate-y-1 shadow-lg whitespace-nowrap z-20">
                                <div>{{ $bar['name'] }}: <span class="font-bold text-blue-300">{{ $bar['amount'] }}</span></div>
                                <div class="text-[10px] text-slate-300">{{ $bar['orders'] }} pesanan</div>
                            </div>

                            <!-- Bar Pillar with Rounded Top and Blue Gradient -->
                            <div class="w-full max-w-[42px] bg-slate-100 rounded-t-2xl relative overflow-hidden transition-all duration-300 group-hover:bg-blue-100" style="height: {{ max(4, $bar['height']) }}%;">
                                @if($bar['amount_raw'] > 0)
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#3B46F1] to-[#60A5FA] rounded-t-2xl transition-all duration-300 group-hover:brightness-110"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- X-Axis Labels (SEN, SEL, RAB, KAM, JUM, SAB, MIN) -->
                <div class="flex items-center justify-between gap-3 sm:gap-6 px-2 sm:px-4 mt-3">
                    @foreach($weeklyChart as $bar)
                        <div class="flex-1 text-center">
                            <span class="text-xs font-bold {{ $bar['amount_raw'] > 0 ? 'text-blue-600' : 'text-slate-400' }} transition-colors uppercase tracking-wider block">
                                {{ $bar['day'] }}
                            </span>
                            <span class="text-[10px] text-slate-400 hidden sm:block">
                                {{ $bar['name'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Column: Distribusi Penjualan Kategori (~35%) -->
        <div class="lg:col-span-4 rounded-[28px] bg-white p-6 sm:p-7 border border-slate-100 shadow-sm flex flex-col justify-between">
            <!-- Header -->
            <div class="pb-2">
                <h2 class="text-base sm:text-lg font-bold text-slate-900">
                    Distribusi Penjualan
                </h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">
                    Proporsi penjualan per kategori produk
                </p>
            </div>

            <!-- Donut Chart & Side-by-side Legend (Real Category Calculation) -->
            <div class="py-4 flex flex-col sm:flex-row lg:flex-col xl:flex-row items-center justify-center gap-6">
                <!-- SVG Donut Chart with real segments -->
                <div class="relative w-40 h-40 shrink-0">
                    <svg viewBox="0 0 140 140" class="w-full h-full transform -rotate-90">
                        <!-- Background Circle -->
                        <circle cx="70" cy="70" r="50" fill="none" stroke="#F1F5F9" stroke-width="20"/>

                        @if($totalCategoryRevenue > 0)
                            @foreach($donutCategories as $cat)
                                @if($cat['percentage'] > 0)
                                    <circle cx="70" cy="70" r="50" fill="none" stroke="{{ $cat['color'] }}" stroke-width="20"
                                            stroke-dasharray="{{ $cat['dasharray'] }}" stroke-dashoffset="{{ $cat['dashoffset'] }}"
                                            stroke-linecap="round"
                                            class="transition-all duration-300 hover:stroke-[22] cursor-pointer"
                                            title="{{ $cat['name'] }}: {{ $cat['percentage'] }}% ({{ $cat['amount'] }})"/>
                                @endif
                            @endforeach
                        @endif
                    </svg>

                    <!-- Center Label / Total -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-xs text-slate-400 font-medium">Total</span>
                        <span class="text-sm font-extrabold text-slate-800">
                            {{ $totalCategoryRevenue > 0 ? '100%' : '0%' }}
                        </span>
                    </div>
                </div>

                <!-- Legend List with real percentages and amounts -->
                <div class="space-y-3 w-full max-w-[210px]">
                    @forelse($donutCategories as $cat)
                        <div class="flex items-center justify-between text-xs font-semibold group cursor-pointer hover:bg-slate-50 p-1.5 rounded-xl transition">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $cat['color'] }};"></span>
                                <div class="truncate">
                                    <span class="text-slate-700 font-medium group-hover:text-slate-900 block truncate">
                                        {{ $cat['name'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block font-normal">
                                        {{ $cat['amount'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-slate-900 font-bold ml-2 shrink-0">
                                {{ $cat['percentage'] }} %
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Belum ada kategori penjualan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: 3 Mini KPI Cards (Real Data) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Bottom Card 1: Pesanan Selesai (Headset) -->
        <div class="rounded-[24px] bg-white p-5 border border-slate-100 shadow-sm flex items-center gap-4 transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-2xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">{{ $bottomCards[0]['title'] }}</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-slate-900">{{ $bottomCards[0]['value'] }}</span>
                    <span class="text-xs font-bold text-emerald-500">{{ $bottomCards[0]['trend'] }}</span>
                    <span class="text-xs text-slate-400">{{ $bottomCards[0]['trend_label'] }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom Card 2: Produk Terjual (Shopping Bag) -->
        <div class="rounded-[24px] bg-white p-5 border border-slate-100 shadow-sm flex items-center gap-4 transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-400 text-white flex items-center justify-center shrink-0 shadow-md shadow-emerald-400/20">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                    <path d="M3 6h18"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">{{ $bottomCards[1]['title'] }}</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-slate-900">{{ $bottomCards[1]['value'] }}</span>
                    <span class="text-xs font-bold text-emerald-500">{{ $bottomCards[1]['trend'] }}</span>
                    <span class="text-xs text-slate-400">{{ $bottomCards[1]['trend_label'] }}</span>
                </div>
            </div>
        </div>

        <!-- Bottom Card 3: Metode Pembayaran (Cash vs Non-Cash) -->
        <div class="rounded-[24px] bg-white p-5 border border-slate-100 shadow-sm flex items-center gap-4 transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-400 text-white flex items-center justify-center shrink-0 shadow-md shadow-amber-400/20">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400">{{ $bottomCards[2]['title'] }}</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-slate-900">{{ $bottomCards[2]['value'] }}</span>
                    <span class="text-xs font-bold text-blue-600">{{ $bottomCards[2]['trend'] }}</span>
                    <span class="text-xs text-slate-400">{{ $bottomCards[2]['trend_label'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Transactions Drawer -->
    <div class="rounded-[28px] bg-slate-50/70 border border-slate-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/60">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span>⚡</span> Aksi Cepat & Transaksi Terkini
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Akses langsung ke fitur kasir, manajemen inventaris, dan laporan.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <span>+</span> Kasir (POS)
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold rounded-xl shadow-sm transition">
                    Katalog Produk
                </a>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold rounded-xl shadow-sm transition">
                    Laporan
                </a>
            </div>
        </div>

        @if(count($recentTransactions) > 0)
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold border-b border-slate-200/50 pb-2">
                            <th class="py-2.5 font-semibold">No. Invoice</th>
                            <th class="py-2.5 font-semibold">Tanggal</th>
                            <th class="py-2.5 font-semibold">Pelanggan</th>
                            <th class="py-2.5 font-semibold">Metode</th>
                            <th class="py-2.5 font-semibold text-right">Total</th>
                            <th class="py-2.5 font-semibold text-center">Status</th>
                            <th class="py-2.5 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-white/80 transition-colors">
                                <td class="py-3 font-bold text-blue-600">
                                    {{ $tx->invoice_number }}
                                </td>
                                <td class="py-3 text-slate-500">
                                    {{ $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3 text-slate-800">
                                    {{ $tx->customer_name ?: 'Pelanggan Umum' }}
                                </td>
                                <td class="py-3">
                                    <span class="capitalize px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-semibold text-[11px]">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 text-right font-bold text-slate-900">
                                    Rp {{ number_format($tx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $tx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('transactions.show', $tx) }}" class="text-blue-600 hover:text-blue-800 font-bold text-[11px] underline">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-6 text-center text-xs text-slate-400">
                Belum ada transaksi pada periode ini.
            </div>
        @endif
    </div>
</div>
@endsection
