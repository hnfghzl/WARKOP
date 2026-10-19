<div id="cartPanel" 
     data-cart-count="{{ array_sum(array_column($cartItems, 'quantity')) }}" 
     data-cart-total="{{ number_format(round($subtotal + ($subtotal * 0.11)), 0, ',', '.') }}"
     class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-6 scroll-mt-24">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <path d="M3 6h18"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span>Keranjang</span>
            @if(count($cartItems) > 0)
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-bold ml-1">
                    {{ array_sum(array_column($cartItems, 'quantity')) }}
                </span>
            @endif
        </h2>
        <a href="#productsSection" class="xl:hidden inline-flex items-center gap-1 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-xl transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            <span>Pilih Produk</span>
        </a>
    </div>

    @if(count($cartItems) > 0)
        <div class="space-y-3 mb-6">
            @foreach($cartItems as $item)
                <div class="border border-slate-200/80 rounded-2xl p-3 sm:p-3.5 bg-slate-50/60 hover:bg-slate-50 transition">
                    <div class="flex justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-slate-900 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $item['quantity'] }} &times; Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <p class="font-extrabold text-sm text-slate-900 shrink-0">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                        <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl p-0.5 shadow-xs">
                            <form method="POST" action="{{ route('pos.cart.adjust') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="-1">
                                <button type="submit" class="h-7 w-7 rounded-lg bg-slate-100 text-base font-bold text-slate-700 hover:bg-slate-200 transition flex items-center justify-center active:scale-95">&minus;</button>
                            </form>

                            <span class="min-w-8 text-center text-xs font-bold text-slate-800">{{ $item['quantity'] }}</span>

                            <form method="POST" action="{{ route('pos.cart.adjust') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="h-7 w-7 rounded-lg bg-blue-50 text-base font-bold text-blue-600 hover:bg-blue-100 transition flex items-center justify-center active:scale-95">&plus;</button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('pos.cart.remove') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                            <button type="submit" class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700 text-xs font-semibold px-2 py-1 rounded-lg hover:bg-rose-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-slate-100 pt-4 space-y-3">
            <div class="flex justify-between text-sm font-semibold text-slate-600">
                <span>Subtotal</span>
                <span class="text-slate-900 font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-slate-500">
                <span>PPN (11%)</span>
                <span>Rp {{ number_format(round($subtotal * 0.11), 0, ',', '.') }}</span>
            </div>

            <form method="POST" action="{{ route('pos.checkout') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label for="customerNameInput" class="block text-xs font-bold text-slate-600 mb-1">Nama Pelanggan (Opsional)</label>
                        <input id="customerNameInput" type="text" name="customer_name" placeholder="Pelanggan umum..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm font-medium focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    <div>
                        <label for="paymentMethod" class="block text-xs font-bold text-slate-600 mb-1">Metode Pembayaran</label>
                        <select id="paymentMethod" name="payment_method" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm font-medium focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="qris">QRIS Digital</option>
                        </select>
                    </div>

                    <div>
                        <label for="paymentAmount" class="block text-xs font-bold text-slate-600 mb-1">Jumlah Uang Diterima</label>
                        <input id="paymentAmount" type="text" inputmode="numeric" pattern="[0-9]*" name="payment" min="0" step="1" placeholder="Masukkan nominal bayar" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-blue-50/50 border border-slate-200/70 p-3.5 text-xs text-slate-700">
                        <div class="flex items-center justify-between gap-3 text-sm font-bold text-slate-900">
                            <span>Total Tagihan</span>
                            <span id="posTotalAmount" data-total="{{ round($subtotal + ($subtotal * 0.11)) }}" class="text-base text-blue-600 font-extrabold">Rp {{ number_format(round($subtotal + ($subtotal * 0.11)), 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-slate-500">Kembalian</span>
                            <span id="changeAmount" class="font-extrabold text-emerald-600 text-sm">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md shadow-emerald-500/25 transition active:scale-98">
                        Bayar Sekarang
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('pos.cart.clear') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-800 py-2.5 rounded-xl text-xs font-bold transition">
                    Kosongkan Keranjang
                </button>
            </form>
        </div>
    @else
        <div class="py-12 text-center text-slate-400">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-3">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <p class="font-bold text-slate-700 text-sm">Keranjang masih kosong.</p>
            <p class="text-xs text-slate-400 mt-1">Pilih menu produk di sebelah kiri untuk menambahkan pesanan.</p>
        </div>
    @endif
</div>
