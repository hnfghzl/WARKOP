@extends('layout')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header: Greeting & Status Operasional Toko -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-4">
        <div>
            <h1 class="text-base sm:text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-1.5 sm:gap-2">
                Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! <span class="inline-block text-base sm:text-xl">👋</span>
            </h1>
            <p class="mt-0.5 text-[11px] sm:text-sm text-slate-500 font-medium">
                Pusat kendali operasional kasir dan ketersediaan menu toko hari ini.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-[10px] sm:text-xs font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Kasir Siap Melayani
            </span>
            <span class="text-[10px] sm:text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full hidden sm:inline-block">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <!-- 4 Operational Metric Cards (2x2 on mobile, 4 columns on desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-5">
        <!-- Card 1: Penjualan Hari Ini / Sesuai Periode -->
        <div class="rounded-xl sm:rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 p-3 sm:p-5 text-white shadow-md shadow-blue-500/20 relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[125px]">
            <div class="flex items-center justify-between gap-1 relative z-10">
                <span class="text-[11px] sm:text-xs font-semibold text-white/90 truncate">
                    {{ $period === 'semua' ? 'Total Penjualan' : 'Penjualan Hari Ini' }}
                </span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white text-blue-600 font-bold flex items-center justify-center text-[10px] sm:text-xs shrink-0 shadow-xs">
                    Rp
                </div>
            </div>
            <div class="relative z-10 pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-extrabold tracking-tight truncate leading-tight">
                    Rp {{ number_format($periodRevenue, 0, ',', '.') }}
                </div>
                <p class="text-[9px] sm:text-xs text-blue-100/80 mt-0.5 truncate">
                    {{ $period === 'semua' ? 'Akumulasi seluruh transaksi' : 'Omset kasir hari ini' }}
                </p>
            </div>
        </div>

        <!-- Card 2: Transaksi Kasir Hari Ini -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[125px]">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">
                    {{ $period === 'semua' ? 'Total Transaksi' : 'Transaksi Hari Ini' }}
                </span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-bold tracking-tight text-slate-900 truncate leading-tight">
                    {{ number_format($periodOrdersCount, 0, ',', '.') }}
                </div>
                <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 truncate">
                    Pesanan diproses kasir
                </p>
            </div>
        </div>

        <!-- Card 3: Unit Terjual Hari Ini -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[125px]">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">
                    {{ $period === 'semua' ? 'Total Item Terjual' : 'Produk Terjual' }}
                </span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-bold tracking-tight text-slate-900 truncate leading-tight">
                    {{ number_format($periodItemsCount, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-400">item</span>
                </div>
                <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 truncate">
                    Kuantitas terjual
                </p>
            </div>
        </div>

        <!-- Card 4: Total Produk di Katalog -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3 sm:p-5 border border-slate-100 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[92px] sm:min-h-[125px]">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-semibold text-slate-500 truncate">Katalog Produk</span>
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16.5 9.4 7.55 4.24a1.78 1.78 0 0 0-2.5 1.55v12.42a1.78 1.78 0 0 0 2.5 1.55L16.5 14.6a1.78 1.78 0 0 0 0-3.2Z"/>
                    </svg>
                </div>
            </div>
            <div class="pt-1.5 sm:pt-2">
                <div class="text-sm sm:text-2xl font-bold tracking-tight text-slate-900 truncate leading-tight">
                    {{ number_format($productsCount, 0, ',', '.') }} <span class="text-[10px] sm:text-xs font-normal text-slate-400">menu</span>
                </div>
                <p class="text-[9px] sm:text-xs text-slate-400 mt-0.5 truncate">
                    {{ $categories->count() }} kategori aktif
                </p>
            </div>
        </div>
    </div>


    <!-- 2 Column Section: Monitoring Stok & Kategori Menu -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-5">
        <!-- Monitoring Stok Rendah -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3.5 sm:p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Peringatan Stok Inventaris</span>
                    </h3>
                    <a href="{{ route('products.index') }}" class="text-[10px] sm:text-xs font-bold text-blue-600 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="mt-3 space-y-2">
                    @forelse($lowStockProducts as $low)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50/60 border border-amber-100/80 text-xs">
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 block truncate">{{ $low->name }}</span>
                                <span class="text-[10px] text-slate-400">SKU: {{ $low->sku }} &bull; {{ $low->category ?: 'Umum' }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-extrabold text-[11px]">
                                    Sisa {{ $low->stock }}
                                </span>
                                <a href="{{ route('products.index', ['edit' => $low->id]) }}" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-white border border-slate-200 px-2 py-0.5 rounded-md">
                                    Tambah
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <p class="text-xs font-bold text-slate-700">Semua Stok Produk Aman</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Tidak ada produk yang berada di bawah batas minimum (5 unit).</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kategori Menu Aktif di Toko -->
        <div class="rounded-xl sm:rounded-2xl bg-white p-3.5 sm:p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Kategori Menu di Kasir</span>
                    </h3>
                    <span class="text-[10px] sm:text-xs font-semibold text-slate-400">
                        {{ $categories->count() }} Kategori
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($categories as $cat)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>{{ $cat->category }}</span>
                            <span class="ml-1 text-[10px] font-semibold text-slate-400 bg-white px-1.5 py-0.5 rounded border border-slate-200">
                                {{ $cat->total }} item
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-3">Belum ada kategori produk.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Siap disajikan pada aplikasi kasir POS</span>
                <a href="{{ route('products.index') }}" class="font-bold text-blue-600 hover:underline">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Transaksi Terkini Kasir (Recent Activity Feed) -->
    <div class="rounded-xl sm:rounded-2xl bg-white border border-slate-100 shadow-sm p-3.5 sm:p-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Transaksi Terkini</span>
                </h2>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Aktivitas struk pesanan yang baru masuk di kasir</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-[10px] sm:text-xs font-bold text-blue-600 hover:underline">
                Lihat Laporan Lengkap &rarr;
            </a>
        </div>

        @if(count($recentTransactions) > 0)
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 font-bold border-b border-slate-100 pb-2">
                            <th class="py-2 font-semibold">No. Invoice</th>
                            <th class="py-2 font-semibold">Waktu</th>
                            <th class="py-2 font-semibold">Pelanggan</th>
                            <th class="py-2 font-semibold">Metode</th>
                            <th class="py-2 font-semibold text-right">Total</th>
                            <th class="py-2 font-semibold text-center">Status</th>
                            <th class="py-2 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2.5 font-bold text-blue-600">
                                    {{ $tx->invoice_number }}
                                </td>
                                <td class="py-2.5 text-slate-400 text-[11px]">
                                    {{ $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-2.5 text-slate-800">
                                    {{ $tx->customer_name ?: 'Pelanggan Umum' }}
                                </td>
                                <td class="py-2.5">
                                    <span class="capitalize px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-semibold text-[10px]">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($tx->total, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tx->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right">
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
                <p class="font-bold text-slate-700">Belum ada transaksi</p>
                <p class="text-[11px] mt-0.5">Buka menu kasir untuk mencatat transaksi penjualan perdana.</p>
            </div>
        @endif
    </div>

    <!-- Info Banner: Arahkan analisis & tren ke Laporan -->
    <div class="rounded-xl sm:rounded-2xl bg-gradient-to-r from-slate-50 to-blue-50/50 border border-slate-200/80 p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800">Mencari grafik tren penjualan atau perbandingan omset?</p>
                <p class="text-[10px] sm:text-xs text-slate-500">Semua analisis grafik tren harian, pembagian metode pembayaran, dan ekspor CSV tersedia di halaman Laporan.</p>
            </div>
        </div>
        <a href="{{ route('reports.index') }}" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 hover:bg-slate-50 text-blue-600 text-xs font-bold rounded-xl shadow-xs transition shrink-0">
            <span>Buka Laporan Penjualan</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
@endsection
