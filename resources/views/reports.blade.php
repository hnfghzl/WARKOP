@extends('layout')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Laporan Penjualan</h1>
        <p class="text-slate-600">Ringkasan omset dan transaksi</p>
    </div>

    <form method="GET" action="{{ route('reports.index') }}" class="mb-8 bg-white rounded-xl shadow p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-slate-600 mb-1">Dari tanggal</label>
                <input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-slate-600 mb-1">Sampai tanggal</label>
                <input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium">Filter</button>
                <a href="{{ route('reports.index') }}" class="bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded font-medium">Reset</a>
            </div>
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-slate-500">Total Penjualan Periode</p>
            <p class="text-3xl font-bold mt-2">Rp {{ number_format($reportTotal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-slate-500">Jumlah Transaksi</p>
            <p class="text-3xl font-bold mt-2">{{ $transactionCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-sm text-slate-500">Rata-rata Transaksi</p>
            <p class="text-3xl font-bold mt-2">Rp {{ number_format($transactionCount > 0 ? $reportTotal / $transactionCount : 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-xl font-semibold">Detail Harian Periode</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Jumlah Transaksi</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($dailyBreakdown as $day)
                        <tr>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $day->count }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($day->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-slate-500">Belum ada data penjualan bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-xl font-semibold">Transaksi Terbaru</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Invoice</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Total</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-slate-600">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $transaction->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $transaction->customer_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
