@extends('layout')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                    Arsip Penjualan
                </span>
                <span class="text-xs text-slate-400 font-medium">Semua Transaksi Toko</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Riwayat Transaksi
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                Daftar semua faktur dan pembayaran kasir yang telah tercatat dalam sistem.
            </p>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-col sm:flex-row gap-3 rounded-2xl bg-white p-4 sm:p-5 border border-slate-100 shadow-sm">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="search" 
                       name="search" 
                       value="{{ $filters['search'] ?? '' }}" 
                       placeholder="Cari invoice atau nama pelanggan..." 
                       class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-xs sm:text-sm font-medium focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10 transition">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition active:scale-95">
                    <span>Cari</span>
                </button>
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-600 transition active:scale-95">
                    Reset
                </a>
            </div>
        </form>

        <!-- Transactions Table Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[640px] w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/75">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Invoice</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Pelanggan</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Total</th>
                            <th class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Bayar</th>
                            <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Waktu</th>
                            <th class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 whitespace-nowrap font-mono font-bold text-blue-600">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="hover:underline">
                                        {{ $transaction->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-800">
                                    {{ $transaction->customer_name ?: 'Pelanggan Umum' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right font-extrabold text-slate-900 text-sm">
                                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-slate-600">
                                    Rp {{ number_format($transaction->payment, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex items-center gap-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 font-bold text-[11px] transition">
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="font-bold text-slate-700 text-sm">Belum ada transaksi</p>
                                        <p class="text-xs text-slate-400 mt-1">Transaksi yang selesai akan tercatat di sini secara otomatis.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
