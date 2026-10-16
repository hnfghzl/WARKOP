@extends('layout')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-600">
                ✓
            </div>
            <h1 class="mt-5 text-3xl font-bold text-slate-900">Pembayaran Berhasil</h1>
            <p class="mt-2 text-slate-600">Transaksi {{ $transaction->invoice_number }} telah berhasil disimpan.</p>

            <div class="mt-6 rounded-lg bg-slate-50 p-5 text-left">
                <div class="flex justify-between gap-4 border-b border-slate-200 pb-3">
                    <span class="text-slate-600">Pelanggan</span>
                    <span class="font-medium">{{ $transaction->customer_name ?? 'Umum' }}</span>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-200 py-3">
                    <span class="text-slate-600">Metode pembayaran</span>
                    <span class="font-medium uppercase">{{ $transaction->payment_method }}</span>
                </div>
                <div class="flex justify-between gap-4 pt-3 text-lg">
                    <span class="font-semibold">Total</span>
                    <span class="font-bold text-blue-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-4 pt-2">
                    <span class="text-slate-600">Kembalian</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <button type="button" data-open-receipt-modal class="rounded-lg bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">Cetak Struk</button>
                <a href="{{ route('pos.index') }}" class="rounded-lg bg-emerald-600 px-4 py-3 font-medium text-white hover:bg-emerald-700">Transaksi Baru</a>
                <a href="{{ route('transactions.show', $transaction) }}" class="rounded-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 hover:bg-slate-300">Lihat Detail</a>
            </div>
        </div>
    </div>

    @include('receipt-modal', ['transaction' => $transaction])
@endsection
