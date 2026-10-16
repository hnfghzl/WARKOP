@extends('layout')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Riwayat Transaksi</h1>
        <p class="text-slate-600">Semua penjualan yang telah terjadi</p>
    </div>

    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6 flex flex-col gap-3 rounded-xl bg-white p-5 shadow sm:flex-row">
        <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari invoice atau nama pelanggan" class="flex-1 rounded border px-3 py-2">
        <div class="flex gap-2">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">Cari</button>
            <a href="{{ route('transactions.index') }}" class="rounded bg-slate-200 px-4 py-2 font-medium hover:bg-slate-300">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Invoice</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Pelanggan</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Total</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Bayar</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                {{ $transaction->invoice_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $transaction->customer_name }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($transaction->payment, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
