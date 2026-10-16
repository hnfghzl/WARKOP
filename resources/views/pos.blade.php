@php
    $cartTotal = $subtotal + ($subtotal * 0.11);
@endphp

@extends('layout')

@section('title', 'Point of Sale')

@section('content')
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Point of Sale</h1>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">Kasir Aktif</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($products as $product)
                        <form method="POST" action="{{ route('pos.cart.add') }}" data-cart-form class="bg-slate-50 border rounded-xl p-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="mb-3 h-32 w-full rounded-lg object-cover">
                                    @else
                                        <div class="mb-3 flex h-32 w-full items-center justify-center rounded-lg bg-slate-200 text-sm text-slate-400">Tanpa gambar</div>
                                    @endif
                                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                                    <p class="text-sm text-slate-500">{{ $product->sku }}</p>
                                </div>
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded">Stok {{ $product->stock }}</span>
                            </div>
                            <p class="text-blue-600 font-bold text-xl">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</p>
                            <div class="mt-4 flex items-center gap-2">
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 border rounded px-2 py-2">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">Tambah</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>

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
                                    <span id="posTotalAmount" data-total="{{ round($cartTotal) }}" class="font-semibold">Rp {{ number_format(round($cartTotal), 0, ',', '.') }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-3 text-sm">
                                    <span>Kembalian</span>
                                    <span id="changeAmount" class="font-semibold text-emerald-600">Rp 0</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium">
                                Bayar
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('pos.cart.clear') }}" data-cart-form class="mt-2">
                        @csrf
                        <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 px-4 py-3 rounded-lg">Clear Keranjang</button>
                    </form>
                </div>
            @else
                <p class="text-slate-500 text-center py-8">Keranjang masih kosong.</p>
            @endif
        </div>
    </div>

    @if($checkoutTransaction)
        <div id="checkoutSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 px-4" role="dialog" aria-modal="true" aria-labelledby="checkoutSuccessTitle">
            <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl">
                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-600">✓</div>
                    <h2 id="checkoutSuccessTitle" class="mt-4 text-2xl font-bold">Pembayaran Berhasil</h2>
                    <p class="mt-1 text-slate-600">{{ $checkoutTransaction->invoice_number }}</p>
                </div>

                <div class="mt-5 rounded-lg bg-slate-50 p-4">
                    <div class="flex justify-between gap-4 text-lg font-semibold">
                        <span>Total</span>
                        <span class="text-blue-600">Rp {{ number_format($checkoutTransaction->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex justify-between gap-4">
                        <span class="text-slate-600">Kembalian</span>
                        <span class="font-semibold text-emerald-600">Rp {{ number_format($checkoutTransaction->change, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <button type="button" data-open-receipt-modal class="rounded-lg bg-blue-600 px-4 py-3 font-medium text-white hover:bg-blue-700">Cetak Struk</button>
                    <button type="button" data-close-checkout-modal class="rounded-lg bg-emerald-600 px-4 py-3 font-medium text-white hover:bg-emerald-700">Transaksi Baru</button>
                    <a href="{{ route('transactions.show', $checkoutTransaction) }}" class="rounded-lg bg-slate-200 px-4 py-3 text-center font-medium text-slate-800 hover:bg-slate-300">Lihat Detail</a>
                </div>
            </div>
        </div>
    @endif

    @if($checkoutTransaction)
        @include('receipt-modal', ['transaction' => $checkoutTransaction])
    @endif

    @vite('resources/js/pos.js')
@endsection
