@extends('layout')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-3.5 sm:space-y-6 print:space-y-4">
    <!-- Top Header: Title, Active Range, & Action Buttons in a single row on mobile -->
    <div class="flex items-center justify-between gap-2.5">
        <div class="min-w-0">
            <h1 class="text-base sm:text-2xl font-bold tracking-tight text-slate-900 truncate">
                Laporan Penjualan
            </h1>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium truncate mt-0.5">
                @if(!empty($filters['start_date']) || !empty($filters['end_date']))
                    Periode: <span class="font-bold text-blue-600">
                        {{ !empty($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date'])->format('d M') : 'Awal' }} - 
                        {{ !empty($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') : 'Hari Ini' }}
                    </span>
                @else
                    Periode: <span class="font-bold text-slate-700">{{ ($period ?? '') === 'today' ? 'Hari Ini' : (($period ?? '') === 'week' ? '7 Hari' : (($period ?? '') === 'month' ? 'Bulan Ini' : (($period ?? '') === 'last_month' ? 'Bulan Lalu' : (($period ?? '') === 'year' ? 'Tahun Ini' : 'Semua Waktu')))) }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 print:hidden">
            <!-- Export CSV Button -->
            <button type="button" 
                    id="btnExportCsv" 
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg sm:rounded-xl bg-white border border-slate-200/90 px-2.5 py-1.5 sm:px-3.5 sm:py-2 text-[11px] sm:text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition active:scale-95"
                    title="Unduh Rekap Spreadsheet (CSV)">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="hidden sm:inline">Ekspor CSV</span>
            </button>

            <!-- Print Button -->
            <button type="button" 
                    onclick="window.print()" 
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg sm:rounded-xl bg-slate-900 px-2.5 py-1.5 sm:px-3.5 sm:py-2 text-[11px] sm:text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition active:scale-95"
                    title="Cetak Dokumen Laporan">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span class="hidden sm:inline">Cetak</span>
            </button>
        </div>
    </div>

    <!-- Filter Card: Sleek, compact chip bar with toggleable custom date picker -->
    <div class="rounded-xl sm:rounded-2xl bg-white p-2 sm:p-3.5 border border-slate-100 shadow-sm print:hidden">
        <!-- Preset Date Quick Filter Chips -->
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs pb-1 sm:pb-0 {{ (!empty($filters['start_date']) || !empty($filters['end_date'])) ? 'border-b border-slate-100 pb-2 mb-2 sm:mb-2.5' : '' }}">
            <a href="{{ route('reports.index') }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ empty($filters['start_date']) && empty($filters['end_date']) && empty($period) ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('reports.index', ['period' => 'today']) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ ($period ?? '') === 'today' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Hari Ini
            </a>
            <a href="{{ route('reports.index', ['period' => 'week']) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ ($period ?? '') === 'week' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                7 Hari
            </a>
            <a href="{{ route('reports.index', ['period' => 'month']) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ ($period ?? '') === 'month' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Bulan Ini
            </a>
            <a href="{{ route('reports.index', ['period' => 'last_month']) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ ($period ?? '') === 'last_month' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Bulan Lalu
            </a>
            <a href="{{ route('reports.index', ['period' => 'year']) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition {{ ($period ?? '') === 'year' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Tahun Ini
            </a>

            <!-- Toggle Custom Date Button -->
            <button type="button" 
                    onclick="const c = document.getElementById('customDateCollapse'); c.classList.toggle('hidden');"
                    class="ml-auto inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] sm:text-xs font-bold whitespace-nowrap transition shrink-0 {{ (!empty($filters['start_date']) || !empty($filters['end_date'])) ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Kustom</span>
                <svg class="w-3 h-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>

        <!-- Custom Date Range Form (Hidden by default, opens smoothly when clicked or active) -->
        <div id="customDateCollapse" class="{{ (!empty($filters['start_date']) || !empty($filters['end_date'])) ? '' : 'hidden' }} pt-2">
            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-2 lg:grid-cols-12 gap-2 sm:gap-3 items-end">
                <div class="col-span-1 lg:col-span-4">
                    <label for="start_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">
                        Dari Tanggal
                    </label>
                    <input id="start_date" 
                           type="date" 
                           name="start_date" 
                           value="{{ $filters['start_date'] ?? '' }}" 
                           class="w-full px-2 py-1 sm:px-3 sm:py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <div class="col-span-1 lg:col-span-4">
                    <label for="end_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">
                        Sampai Tanggal
                    </label>
                    <input id="end_date" 
                           type="date" 
                           name="end_date" 
                           value="{{ $filters['end_date'] ?? '' }}" 
                           class="w-full px-2 py-1 sm:px-3 sm:py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <div class="col-span-2 lg:col-span-4 flex items-center gap-1.5 mt-1 sm:mt-0">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-blue-700 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('reports.index') }}" 
                       class="inline-flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition active:scale-95" 
                       title="Reset Filter Tanggal">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 4 KPI Summary Cards (2x2 on Mobile, 4 Columns on Desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-5">
        <!-- Card 1: Total Penjualan Periode (Hero Gradient Card) -->
        <div class="rounded-xl sm:rounded-2xl bg-gradient-to-br from-[#3B46F1] via-[#4352FF] to-[#5563FF] p-3 sm:p-5 text-white shadow-md shadow-blue-500/20 relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[140px] group transition-all duration-300">
            <div class="absolute inset-x-0 bottom-0 pointer-events-none opacity-60 sm:opacity-80 transition-opacity">
                <svg viewBox="0 0 300 80" class="w-full h-8 sm:h-14 text-white/20" preserveAspectRatio="none" fill="none">
                    <defs>
                        <linearGradient id="heroReportGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M0,50 Q40,35 80,48 T160,25 T230,45 T300,15 L300,80 L0,80 Z" fill="url(#heroReportGrad)"/>
                    <path d="M0,50 Q40,35 80,48 T160,25 T230,45 T300,15" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="flex items-center justify-between relative z-10 gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-white/90 truncate">Total Penjualan</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white text-blue-600 font-extrabold flex items-center justify-center text-[10px] sm:text-xs shrink-0 shadow-xs">
                    Rp
                </div>
            </div>
            <div class="relative z-10 pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-extrabold tracking-tight truncate leading-tight">
                    Rp {{ number_format($reportTotal, 0, ',', '.') }}
                </div>
                <div class="mt-0.5 sm:mt-1 flex items-center text-[9px] sm:text-xs text-white/80 truncate">
                    <span>Setelah diskon & pajak</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Jumlah Transaksi -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[140px] group transition-all duration-300">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">Total Transaksi</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate leading-tight">
                    {{ number_format($transactionCount, 0, ',', '.') }}
                </div>
                <div class="mt-0.5 sm:mt-1 flex items-center text-[9px] sm:text-xs font-medium text-slate-400 truncate">
                    <span class="text-emerald-600 font-bold">Faktur Selesai</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Rata-rata Transaksi (AOV) -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[140px] group transition-all duration-300">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">Rata-rata/Trx</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate leading-tight">
                    Rp {{ number_format($transactionCount > 0 ? $reportTotal / $transactionCount : 0, 0, ',', '.') }}
                </div>
                <div class="mt-0.5 sm:mt-1 flex items-center text-[9px] sm:text-xs font-medium text-slate-400 truncate">
                    <span>Rata-rata belanja</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Produk Terjual -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[140px] group transition-all duration-300">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">Unit Terjual</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate leading-tight">
                    {{ number_format($totalItemsSold ?? 0, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-400">unit</span>
                </div>
                <div class="mt-0.5 sm:mt-1 flex items-center text-[9px] sm:text-xs font-medium text-slate-400 truncate">
                    <span>Pajak: Rp {{ number_format($totalTax ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Daily Revenue Chart (Grafik Tren Penjualan Harian) -->
    <div class="rounded-xl sm:rounded-2xl bg-white p-3.5 sm:p-6 border border-slate-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 mb-3 sm:mb-6">
            <div>
                <h2 class="text-xs sm:text-base font-bold text-slate-900 flex items-center gap-1.5 sm:gap-2">
                    <span class="flex h-5 w-5 sm:h-7 sm:w-7 items-center justify-center rounded-lg sm:rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    <span>Tren Penjualan Harian</span>
                </h2>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Grafik perbandingan omset dan jumlah transaksi setiap hari</p>
            </div>
            @if($dailyBreakdown->isNotEmpty())
                <div class="flex items-center gap-2 text-[10px] sm:text-xs font-semibold text-slate-500 self-start sm:self-auto">
                    <span class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 rounded-sm bg-blue-600 inline-block"></span>
                        Omset
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span>Tertinggi: <strong class="text-slate-900">Rp {{ number_format($maxDailyTotal, 0, ',', '.') }}</strong></span>
                </div>
            @endif
        </div>

        @if($dailyBreakdown->isNotEmpty())
            <!-- Interactive Responsive Bar Chart Container (Optimized height for mobile) -->
            <div class="relative pt-2 sm:pt-4 pb-1 sm:pb-2">
                <div class="h-36 sm:h-52 flex items-end gap-1.5 sm:gap-3 px-1 sm:px-2 overflow-x-auto pb-2">
                    @foreach($dailyBreakdown as $day)
                        @php
                            $percentage = $maxDailyTotal > 0 ? max(12, round(($day->total / $maxDailyTotal) * 100)) : 12;
                            $isHighest = $day->total == $maxDailyTotal;
                        @endphp
                        <div class="flex-1 min-w-[34px] sm:min-w-[48px] max-w-[50px] sm:max-w-[72px] h-full flex flex-col justify-end items-center group relative cursor-pointer">
                            <!-- Tooltip on hover -->
                            <div class="opacity-0 group-hover:opacity-100 pointer-events-none absolute -top-11 z-20 transition-all duration-200 transform group-hover:-translate-y-1 bg-slate-900 text-white text-[10px] sm:text-[11px] font-medium py-1 px-2 rounded-lg whitespace-nowrap shadow-lg">
                                <p class="font-bold text-white">{{ \Carbon\Carbon::parse($day->date)->translatedFormat('d M Y') }}</p>
                                <p class="text-blue-300">Rp {{ number_format($day->total, 0, ',', '.') }} ({{ $day->count }} trx)</p>
                                <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-slate-900 rotate-45"></span>
                            </div>

                            <!-- Value Above Bar -->
                            <span class="text-[9px] sm:text-[10px] font-bold text-slate-500 mb-1 opacity-80 group-hover:opacity-100 transition-opacity">
                                {{ number_format($day->total / 1000, 0) }}k
                            </span>

                            <!-- Bar Column -->
                            <div class="w-full rounded-t-md sm:rounded-t-xl transition-all duration-300 {{ $isHighest ? 'bg-gradient-to-t from-blue-700 via-blue-600 to-indigo-500 shadow-xs group-hover:brightness-110' : 'bg-slate-100 group-hover:bg-blue-200' }}"
                                 style="height: {{ $percentage }}%;">
                            </div>

                            <!-- Date Label Below -->
                            <span class="mt-1 sm:mt-1.5 text-[10px] sm:text-[11px] font-bold {{ $isHighest ? 'text-blue-600 font-extrabold' : 'text-slate-400' }} group-hover:text-slate-800 transition-colors whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty state for chart -->
            <div class="py-8 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center mb-2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <p class="text-xs sm:text-sm font-bold text-slate-800">Tidak ada transaksi pada periode yang dipilih</p>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 max-w-sm">Pilih preset "Semua" atau rentang tanggal lain untuk melihat rekapitulasi data.</p>
            </div>
        @endif
    </div>

    <!-- 2-Column Insights Breakdown (Metode Pembayaran & Produk Terlaris) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-5">
        <!-- 1. Distribusi Metode Pembayaran -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3.5 sm:p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-0.5">
                    <span class="flex h-5 w-5 items-center justify-center rounded-md bg-emerald-50 text-emerald-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </span>
                    <span>Distribusi Metode Pembayaran</span>
                </h3>
                <p class="text-[10px] sm:text-xs text-slate-400">Persentase omset berdasarkan kanal pembayaran</p>

                <div class="mt-3 space-y-2.5">
                    @forelse($paymentBreakdown as $pay)
                        @php
                            $payPercent = $reportTotal > 0 ? round(($pay->total / $reportTotal) * 100, 1) : 0;
                            $methodName = strtolower($pay->payment_method);
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-[10px] font-bold {{ $methodName === 'cash' ? 'bg-emerald-100 text-emerald-700' : ($methodName === 'qris' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                        @if($methodName === 'cash')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                                        @elseif($methodName === 'qris')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        @endif
                                    </span>
                                    <span class="text-slate-800 font-bold uppercase text-[11px] sm:text-xs">{{ $pay->payment_method }}</span>
                                    <span class="text-slate-400 text-[10px]">({{ $pay->count }} trx)</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-extrabold text-slate-900 text-[11px] sm:text-xs">Rp {{ number_format($pay->total, 0, ',', '.') }}</span>
                                    <span class="text-slate-400 ml-1 text-[10px]">({{ $payPercent }}%)</span>
                                </div>
                            </div>
                            <div class="w-full h-1.5 sm:h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $methodName === 'cash' ? 'bg-emerald-500' : ($methodName === 'qris' ? 'bg-blue-600' : 'bg-purple-500') }}"
                                     style="width: {{ $payPercent }}%;">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada transaksi pembayaran.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] sm:text-xs text-slate-400">
                <span>Total: <strong class="text-slate-700">{{ $paymentBreakdown->count() }} metode</strong></span>
                <span class="font-bold text-slate-700">100% Rekonsiliasi</span>
            </div>
        </div>

        <!-- 2. Produk Terlaris Periode Ini -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3.5 sm:p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-2 mb-0.5">
                    <span class="flex h-5 w-5 items-center justify-center rounded-md bg-amber-50 text-amber-600">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </span>
                    <span>5 Produk Terlaris</span>
                </h3>
                <p class="text-[10px] sm:text-xs text-slate-400">Item dengan kuantitas penjualan tertinggi</p>

                <div class="mt-3 space-y-2">
                    @forelse($topProducts as $index => $topItem)
                        <div class="flex items-center justify-between p-2 sm:p-2.5 rounded-xl bg-slate-50/70 hover:bg-slate-100/70 transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-[11px] shrink-0 {{ $index === 0 ? 'bg-amber-100 text-amber-800' : ($index === 1 ? 'bg-slate-200 text-slate-700' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-white text-slate-600 border border-slate-200')) }}">
                                    #{{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $topItem->product_name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $topItem->total_qty }} unit terjual</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs font-bold text-slate-900">Rp {{ number_format($topItem->total_amount, 0, ',', '.') }}</p>
                                <span class="px-1.5 py-0.2 rounded bg-blue-100/80 text-blue-700 text-[9px] font-bold">Terlaris</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada riwayat produk terjual.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] sm:text-xs text-slate-400">
                <span>Total: <strong class="text-slate-700">{{ $totalItemsSold ?? 0 }} unit</strong></span>
                <a href="{{ route('products.index') }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    Katalog &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Harian Periode (Modern Table, Compact for mobile) -->
    <div class="rounded-xl sm:rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-3.5 py-3 sm:px-5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-md bg-blue-50 text-blue-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </span>
                    <span>Detail Harian Periode</span>
                </h2>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Rincian omset dan transaksi per hari</p>
            </div>
            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] sm:text-xs font-bold">
                {{ $dailyBreakdown->count() }} Hari
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[620px] w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/75">
                    <tr>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-left text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Tanggal</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-center text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Transaksi</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-right text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Rata-rata/Trx</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-right text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Total Penjualan</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-left text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Porsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($dailyBreakdown as $day)
                        @php
                            $dayContribution = $reportTotal > 0 ? round(($day->total / $reportTotal) * 100, 1) : 0;
                            $dayAvg = $day->count > 0 ? $day->total / $day->count : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 font-bold text-slate-900 whitespace-nowrap flex items-center gap-2">
                                <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-[11px] shrink-0">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d') }}
                                </span>
                                <div>
                                    <p class="text-xs">{{ \Carbon\Carbon::parse($day->date)->translatedFormat('l, d F Y') }}</p>
                                    <p class="text-[9px] sm:text-[10px] text-slate-400 font-normal">{{ \Carbon\Carbon::parse($day->date)->diffForHumans() }}</p>
                                </div>
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] sm:text-xs font-bold">
                                    {{ $day->count }} trx
                                </span>
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 text-right font-medium text-slate-600 whitespace-nowrap text-xs">
                                Rp {{ number_format($dayAvg, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap text-xs sm:text-sm">
                                Rp {{ number_format($day->total, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap min-w-[120px]">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full rounded-full bg-blue-600" style="width: {{ $dayContribution }}%;"></div>
                                    </div>
                                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 w-9 text-right">{{ $dayContribution }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">
                                Belum ada data penjualan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaksi Terbaru / Periode Ini -->
    <div class="rounded-xl sm:rounded-2xl bg-white border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-3.5 py-3 sm:px-5 sm:py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <span class="flex h-5 w-5 sm:h-6 sm:w-6 items-center justify-center rounded-md bg-slate-100 text-slate-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <span>Transaksi Terbaru</span>
                </h2>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">10 transaksi teranyar dalam rentang laporan</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="text-[10px] sm:text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[620px] w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/75">
                    <tr>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-left text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Invoice</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-left text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Pelanggan</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-center text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Metode</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-right text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Total</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-left text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Waktu</th>
                        <th class="px-3.5 py-2.5 sm:px-5 sm:py-3 text-center text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap">
                                <a href="{{ route('transactions.show', $transaction) }}" class="font-mono font-bold text-blue-600 hover:underline text-xs">
                                    {{ $transaction->invoice_number }}
                                </a>
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap font-medium text-slate-800 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-[9px] font-bold flex items-center justify-center">
                                        {{ strtoupper(substr($transaction->customer_name ?: 'P', 0, 1)) }}
                                    </div>
                                    <span class="truncate max-w-[120px]">{{ $transaction->customer_name ?: 'Pelanggan Umum' }}</span>
                                </div>
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap text-center">
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold uppercase {{ strtolower($transaction->payment_method) === 'cash' ? 'bg-emerald-100 text-emerald-700' : (strtolower($transaction->payment_method) === 'qris' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ $transaction->payment_method ?: 'Tunai' }}
                                </span>
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap text-right font-extrabold text-slate-900 text-xs sm:text-sm">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap text-slate-500 text-[10px] sm:text-xs">
                                {{ $transaction->created_at ? $transaction->created_at->format('d/m/y H:i') : '-' }}
                            </td>
                            <td class="px-3.5 py-2.5 sm:px-5 sm:py-3.5 whitespace-nowrap text-center print:hidden">
                                <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-1 font-bold text-[10px] transition">
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-xs">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Print Stylesheet -->
<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    aside, header, #sidebarToggle, #adminSidebar, .print\\:hidden {
        display: none !important;
    }
    main {
        padding: 0 !important;
    }
    .rounded-xl, .rounded-2xl, .rounded-\\[28px\\] {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: none !important;
    }
}
</style>

<!-- JavaScript for CSV Export -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnExport = document.getElementById('btnExportCsv');
    if (!btnExport) return;

    btnExport.addEventListener('click', function () {
        // Collect daily breakdown data
        const rows = [];
        rows.push(['Laporan Penjualan WARKOP POS']);
        rows.push(['Diekspor pada', new Date().toLocaleString('id-ID')]);
        rows.push([]);
        rows.push(['--- DETAIL HARIAN PERIODE ---']);
        rows.push(['Tanggal', 'Jumlah Transaksi', 'Total Penjualan (Rp)']);

        @foreach($dailyBreakdown as $day)
            rows.push([
                "{{ \Carbon\Carbon::parse($day->date)->format('Y-m-d') }}",
                "{{ $day->count }}",
                "{{ $day->total }}"
            ]);
        @endforeach

        rows.push([]);
        rows.push(['--- TRANSAKSI TERBARU ---']);
        rows.push(['Invoice', 'Pelanggan', 'Metode Pembayaran', 'Total (Rp)', 'Waktu']);

        @foreach($recentTransactions as $tx)
            rows.push([
                "{{ $tx->invoice_number }}",
                "{{ $tx->customer_name ?: 'Pelanggan Umum' }}",
                "{{ $tx->payment_method }}",
                "{{ $tx->total }}",
                "{{ $tx->created_at ? $tx->created_at->format('Y-m-d H:i:s') : '' }}"
            ]);
        @endforeach

        // Convert to CSV
        let csvContent = 'data:text/csv;charset=utf-8,\uFEFF';
        rows.forEach(function (rowArray) {
            const row = rowArray.map(item => `"${String(item).replace(/"/g, '""')}"`).join(',');
            csvContent += row + '\r\n';
        });

        // Trigger download
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `laporan_penjualan_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
@endsection
