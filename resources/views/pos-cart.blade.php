<div id="cartPanel" class="bg-white rounded-xl shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Keranjang</h2>

    @if(count($cartItems) > 0)
        <div class="space-y-3 mb-6">
            @foreach($cartItems as $item)
                <div class="border rounded-lg p-3">
                    <div class="flex justify-between gap-3">
                        <div>
                            <p class="font-semibold">{{ $item['name'] }}</p>
                            <p class="text-sm text-slate-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <p class="font-semibold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('pos.cart.adjust') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="-1">
                                <button type="submit" class="h-8 w-8 rounded bg-slate-200 text-lg font-semibold hover:bg-slate-300">-</button>
                            </form>

                            <span class="min-w-8 text-center text-sm font-semibold">{{ $item['quantity'] }}</span>

                            <form method="POST" action="{{ route('pos.cart.adjust') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="h-8 w-8 rounded bg-slate-200 text-lg font-semibold hover:bg-slate-300">+</button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('pos.cart.remove') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                            <button type="submit" class="text-red-500 text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t pt-4 space-y-3">
            <div class="flex justify-between text-lg font-semibold">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            <form method="POST" action="{{ route('pos.checkout') }}">
                @csrf
                <div class="space-y-3">
                    <input type="text" name="customer_name" placeholder="Nama pelanggan" class="w-full border rounded px-3 py-2">
                    <select id="paymentMethod" name="payment_method" class="w-full border rounded px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                    </select>
                    <input id="paymentAmount" type="text" inputmode="numeric" pattern="[0-9]*" name="payment" min="0" step="1" placeholder="Jumlah bayar" class="w-full border rounded px-3 py-2">
                    <div class="rounded bg-slate-50 px-3 py-2 text-sm text-slate-700">
                        <div class="flex items-center justify-between gap-3">
                            <span>Total tagihan</span>
                            <span id="posTotalAmount" data-total="{{ round($subtotal + ($subtotal * 0.11)) }}" class="font-semibold">Rp {{ number_format(round($subtotal + ($subtotal * 0.11)), 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-3 text-sm">
                            <span>Kembalian</span>
                            <span id="changeAmount" class="font-semibold text-emerald-600">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium">Bayar</button>
                </div>
            </form>

            <form method="POST" action="{{ route('pos.cart.clear') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 px-4 py-3 rounded-lg">Clear Keranjang</button>
            </form>
        </div>
    @else
        <p class="text-slate-500 text-center py-8">Keranjang masih kosong.</p>
    @endif
</div>
