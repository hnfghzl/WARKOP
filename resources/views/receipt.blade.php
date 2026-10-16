<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/receipt.css')
</head>
<body class="bg-slate-100 text-slate-800">
    <div class="max-w-md mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6 print:shadow-none print:rounded-none">
            <div class="text-center border-b border-dashed border-slate-300 pb-4 mb-4">
                <h1 class="text-2xl font-bold">Kasir</h1>
                <p class="text-sm text-slate-500">Struk Pembayaran</p>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Invoice</span>
                    <span class="font-semibold">{{ $transaction->invoice_number }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Tanggal</span>
                    <span>{{ $transaction->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Pelanggan</span>
                    <span>{{ $transaction->customer_name ?? 'Umum' }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Kasir</span>
                    <span>{{ $transaction->cashier_name }}</span>
                </div>
            </div>

            <div class="mt-5 border-t border-dashed border-slate-300 pt-4">
                @foreach($transaction->items as $item)
                    <div class="mb-3">
                        <div class="flex justify-between gap-3 font-medium">
                            <span>{{ $item->product_name }}</span>
                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-3 text-xs text-slate-500">
                            <span>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            <span>{{ $item->quantity }} pcs</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 border-t border-dashed border-slate-300 pt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span>Pajak</span>
                    <span>Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-3 text-base font-bold">
                    <span>Total</span>
                    <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-3">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($transaction->payment, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-3 text-emerald-700 font-semibold">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500 border-t border-dashed border-slate-300 pt-4">
                Terima kasih atas kunjungan Anda
            </div>

            <div class="no-print mt-6 flex gap-3">
                <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    Cetak Struk
                </button>
                <a href="{{ route('transactions.show', $transaction) }}" class="flex-1 text-center bg-slate-200 hover:bg-slate-300 px-4 py-2 rounded-lg font-medium">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>
