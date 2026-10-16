<div id="receiptModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4" role="dialog" aria-modal="true" aria-labelledby="receiptModalTitle">
    <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h2 id="receiptModalTitle" class="text-xl font-semibold">Preview Struk</h2>
            <button type="button" data-close-receipt-modal aria-label="Tutup" class="text-2xl leading-none text-slate-500 hover:text-slate-800">&times;</button>
        </div>
        <iframe title="Preview struk pembayaran" src="{{ route('transactions.receipt', $transaction) }}" class="h-[70vh] w-full border-0"></iframe>
    </div>
</div>

@vite('resources/js/receipt-modal.js')
