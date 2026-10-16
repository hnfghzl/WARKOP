@extends('layout')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Detail Transaksi</h1>
            <p class="text-slate-600">{{ $transaction->invoice_number }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" data-open-receipt-modal class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Cetak Struk
            </button>
            <a href="{{ route('transactions.index') }}" class="rounded-lg bg-slate-600 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <!-- Informasi Transaksi -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Transaksi</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Invoice Number</p>
                        <p class="text-lg font-semibold">{{ $transaction->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Tanggal & Waktu</p>
                        <p class="text-lg font-semibold">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Pelanggan</p>
                        <p class="text-lg font-semibold">{{ $transaction->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Kasir</p>
                        <p class="text-lg font-semibold">{{ $transaction->cashier_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Metode Pembayaran</p>
                        <p class="text-lg font-semibold">
                            @if($transaction->payment_method === 'cash')
                                <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-sm">Cash</span>
                            @elseif($transaction->payment_method === 'qris')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">QRIS</span>
                            @else
                                {{ $transaction->payment_method }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Status</p>
                        <p class="text-lg font-semibold">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Completed</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detail Item -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h2 class="text-xl font-semibold">Detail Item</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Produk</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">SKU</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-slate-600">Qty</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-slate-600">Harga</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-slate-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($transaction->items as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3">{{ $item->product_sku }}</td>
                                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-semibold mb-6">Ringkasan Pembayaran</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between pb-4 border-b border-slate-200">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-semibold">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($transaction->discount > 0)
                    <div class="flex justify-between pb-4 border-b border-slate-200">
                        <span class="text-slate-600">Diskon</span>
                        <span class="font-semibold text-red-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between pb-4 border-b border-slate-200">
                    <span class="text-slate-600">Pajak (11%)</span>
                    <span class="font-semibold">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pb-4 border-b border-slate-200">
                    <span class="text-lg font-semibold">Total</span>
                    <span class="text-lg font-semibold text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pb-4 border-b border-slate-200">
                    <span class="text-slate-600">Pembayaran</span>
                    <span class="font-semibold">Rp {{ number_format($transaction->payment, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between bg-slate-50 -mx-6 -mb-6 px-6 py-4 rounded-b-xl">
                    <span class="text-slate-600">Kembalian</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    @include('receipt-modal', ['transaction' => $transaction])
@endsection
