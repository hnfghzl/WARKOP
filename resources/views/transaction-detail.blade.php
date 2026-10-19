@extends('layout')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold tracking-wide uppercase">
                        Faktur Kasir
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ $transaction->invoice_number }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Detail Transaksi
                </h1>
            </div>
            <div class="flex items-center gap-2.5 self-stretch sm:self-auto">
                <button type="button" 
                        data-open-receipt-modal 
                        class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk</span>
                </button>
                <a href="{{ route('transactions.index') }}" 
                   class="flex-1 sm:flex-initial inline-flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition active:scale-95">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 space-y-6">
                <!-- Informasi Transaksi -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6">
                    <h2 class="text-lg font-extrabold text-slate-900 mb-4 pb-3 border-b border-slate-100">
                        Informasi Transaksi
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Nomor Faktur</p>
                            <p class="text-base font-mono font-bold text-blue-600 mt-0.5">{{ $transaction->invoice_number }}</p>
                        </div>
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Waktu Transaksi</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $transaction->created_at->format('d M Y H:i:s') }}</p>
                        </div>
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Nama Pelanggan</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $transaction->customer_name ?: 'Pelanggan Umum' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Staf Kasir</p>
                            <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $transaction->cashier_name ?: 'Admin / Kasir' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Metode Pembayaran</p>
                            <p class="mt-1">
                                @if(strtolower($transaction->payment_method) === 'cash')
                                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Tunai (Cash)</span>
                                @elseif(strtolower($transaction->payment_method) === 'qris')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase">QRIS Digital</span>
                                @else
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase">{{ $transaction->payment_method }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                            <p class="text-xs text-slate-400 font-medium">Status Pembayaran</p>
                            <p class="mt-1">
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold">Lunas (Completed)</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detail Item -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h2 class="text-lg font-extrabold text-slate-900">Rincian Item Produk</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-[520px] w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50/75">
                                <tr>
                                    <th class="px-5 py-3 text-left font-bold uppercase tracking-wider text-slate-500">Produk</th>
                                    <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-slate-500">SKU</th>
                                    <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-slate-500">Qty</th>
                                    <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-slate-500">Harga</th>
                                    <th class="px-5 py-3 text-right font-bold uppercase tracking-wider text-slate-500">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($transaction->items as $item)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-5 py-3.5 font-bold text-slate-800">{{ $item->product_name }}</td>
                                        <td class="px-4 py-3.5 text-slate-500 font-mono">{{ $item->product_sku }}</td>
                                        <td class="px-4 py-3.5 text-right font-bold text-slate-700">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3.5 text-right text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3.5 text-right font-extrabold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-6 h-fit">
                <h2 class="text-lg font-extrabold text-slate-900 mb-5 pb-3 border-b border-slate-100">
                    Ringkasan Pembayaran
                </h2>
                
                <div class="space-y-3.5 text-xs sm:text-sm">
                    <div class="flex justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($transaction->discount > 0)
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500">Diskon</span>
                            <span class="font-bold text-rose-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">PPN (11%)</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between pb-3 border-b border-slate-100">
                        <span class="text-sm font-extrabold text-slate-900">Total Tagihan</span>
                        <span class="text-base font-extrabold text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Uang Diterima</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($transaction->payment, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between bg-emerald-50/70 border border-emerald-100 -mx-4 -mb-4 sm:-mx-6 sm:-mb-6 p-4 sm:p-5 rounded-b-2xl mt-4">
                        <span class="font-bold text-emerald-800">Uang Kembalian</span>
                        <span class="font-extrabold text-emerald-700 text-base">Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('receipt-modal', ['transaction' => $transaction])
@endsection
