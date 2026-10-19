<div id="receiptModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4" role="dialog" aria-modal="true" aria-labelledby="receiptModalTitle">
    <div class="flex max-h-[92vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-5 sm:py-4 bg-slate-50/70">
            <div class="flex items-center gap-2">
                <span class="text-blue-600 font-bold text-sm">🧾</span>
                <h2 id="receiptModalTitle" class="text-base sm:text-lg font-bold text-slate-900">Preview Struk Pembayaran</h2>
            </div>
            <button type="button" data-close-receipt-modal aria-label="Tutup" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition text-xl font-bold leading-none">&times;</button>
        </div>
        <iframe title="Preview struk pembayaran" src="{{ route('transactions.receipt', $transaction) }}" class="h-[65vh] sm:h-[70vh] w-full border-0"></iframe>
    </div>
</div>

@vite('resources/js/receipt-modal.js')
