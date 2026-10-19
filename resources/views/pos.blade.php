@php
    $cartTotal = $subtotal + ($subtotal * 0.11);
    $totalCartItems = array_sum(array_column($cartItems, 'quantity'));
@endphp

@extends('layout')

@section('title', 'Point of Sale (Kasir)')

@section('content')
    <div class="space-y-4 sm:space-y-6 pb-20 xl:pb-0">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
            <!-- Left Column: Product Catalog -->
            <div id="productsSection" class="xl:col-span-2 space-y-4 sm:space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-2.5 sm:p-6">
                    <!-- Title & Status -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3 mb-3.5 sm:mb-6 pb-3 sm:pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5 sm:mb-1">
                                <span class="px-2 sm:px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] sm:text-xs font-bold tracking-wide uppercase">
                                    POS Kasir
                                </span>
                                <span class="text-[11px] sm:text-xs text-slate-400 font-medium">Mode Transaksi Langsung</span>
                            </div>
                            <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                                Point of Sale
                            </h1>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200/80 text-emerald-700 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-[11px] sm:text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Kasir Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Products Grid: 3 columns on mobile & desktop -->
                    <div class="grid grid-cols-3 gap-1.5 sm:gap-4">
                        @forelse($products as $product)
                            <form method="POST" action="{{ route('pos.cart.add') }}" data-cart-form class="flex flex-col justify-between bg-slate-50/70 hover:bg-white border border-slate-200/80 hover:border-blue-300 rounded-xl sm:rounded-2xl p-1.5 sm:p-4 transition-all duration-200 hover:shadow-md group">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <div>
                                    <!-- Image & Stock Badge -->
                                    <div class="relative mb-1.5 sm:mb-3 overflow-hidden rounded-lg sm:rounded-xl bg-slate-100 aspect-square sm:aspect-4/3 flex items-center justify-center">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                        @else
                                            <div class="flex flex-col items-center justify-center text-slate-400 p-2 sm:p-4 text-center">
                                                <svg class="w-5 h-5 sm:w-8 sm:h-8 mb-0.5 sm:mb-1 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect width="18" height="18" x="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                                <span class="text-[9px] sm:text-[11px] font-medium leading-tight">Tanpa foto</span>
                                            </div>
                                        @endif
                                        <span class="absolute top-1 right-1 text-[8px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded sm:rounded-lg shadow-xs backdrop-blur-sm {{ $product->stock <= 5 ? 'bg-amber-500/95 text-white' : 'bg-emerald-600/95 text-white' }}">
                                            <span class="hidden sm:inline">Stok </span>{{ $product->stock }}
                                        </span>
                                    </div>

                                    <!-- Product Info -->
                                    <h3 class="font-bold text-[11px] sm:text-base text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-2 sm:line-clamp-1 leading-tight sm:leading-normal" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h3>
                                    <div class="hidden sm:flex items-center gap-1.5 mt-0.5 text-xs text-slate-400 font-mono">
                                        <span>{{ $product->sku }}</span>
                                        @if($product->category)
                                            <span>&bull;</span>
                                            <span class="font-sans text-[11px] text-slate-500 font-medium truncate">{{ $product->category }}</span>
                                        @endif
                                    </div>
                                    <p class="sm:hidden text-[9px] text-slate-400 truncate mt-0.5 font-medium leading-tight">
                                        {{ $product->category ?? $product->sku }}
                                    </p>
                                </div>

                                <div class="mt-1.5 sm:mt-4 pt-1.5 sm:pt-3 border-t border-slate-200/60">
                                    <p class="text-blue-600 font-extrabold text-[11px] sm:text-lg mb-1.5 sm:mb-3 truncate">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </p>
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <input type="number" 
                                               name="quantity" 
                                               value="1" 
                                               min="1" 
                                               max="{{ $product->stock }}" 
                                               class="hidden sm:block w-14 sm:w-16 border border-slate-200 rounded-xl px-2 py-2 text-center text-xs sm:text-sm font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                        @if($product->stock <= 0)
                                            <button type="button" 
                                                    disabled 
                                                    class="w-full sm:flex-1 inline-flex items-center justify-center gap-1 bg-slate-200 text-slate-400 px-1.5 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[10px] sm:text-sm font-bold cursor-not-allowed">
                                                <span>Habis</span>
                                            </button>
                                        @else
                                            <button type="submit" 
                                                    class="w-full sm:flex-1 inline-flex items-center justify-center gap-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-1.5 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl text-[10px] sm:text-sm font-bold shadow-xs shadow-blue-500/20 transition active:scale-95 cursor-pointer">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                <span>Tambah</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400">
                                <p class="font-bold text-slate-700 text-base">Belum ada produk untuk dijual</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan tambahkan produk di menu Produk terlebih dahulu.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart Panel (Sticky on Desktop) -->
            <div class="xl:col-span-1">
                <div class="xl:sticky xl:top-24">
                    @include('pos-cart', ['cartItems' => $cartItems, 'subtotal' => $subtotal])
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Floating Cart Sticky Bar (< xl screens) -->
    <div id="mobileCartStickyBar" class="{{ count($cartItems) > 0 ? '' : 'hidden' }} fixed bottom-4 inset-x-3 sm:inset-x-6 z-30 xl:hidden">
        <a href="#cartPanel" class="flex items-center justify-between bg-slate-900/95 backdrop-blur-md text-white p-3.5 rounded-2xl shadow-2xl ring-1 ring-white/10 active:scale-98 transition">
            <div class="flex items-center gap-3">
                <div class="relative w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span id="mobileCartBadge" class="absolute -top-1.5 -right-1.5 bg-emerald-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-slate-900">
                        {{ $totalCartItems }}
                    </span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium leading-tight">Total Tagihan</span>
                    <span id="mobileCartTotal" class="text-sm font-extrabold text-white leading-tight">
                        Rp {{ number_format(round($cartTotal), 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-md transition">
                <span>Lihat Keranjang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </span>
        </a>
    </div>

    @if($checkoutTransaction)
        <div id="checkoutSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4" role="dialog" aria-modal="true" aria-labelledby="checkoutSuccessTitle">
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 sm:p-6 shadow-2xl border border-slate-100">
                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-2xl text-emerald-600 font-bold shadow-sm">
                        ✓
                    </div>
                    <h2 id="checkoutSuccessTitle" class="mt-4 text-xl sm:text-2xl font-extrabold text-slate-900">Pembayaran Berhasil</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-mono">{{ $checkoutTransaction->invoice_number }}</p>
                </div>

                <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-100 p-4 text-xs sm:text-sm space-y-2">
                    <div class="flex justify-between gap-4 font-bold">
                        <span class="text-slate-600">Total Tagihan</span>
                        <span class="text-blue-600 text-base font-extrabold">Rp {{ number_format($checkoutTransaction->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-4 pt-2 border-t border-slate-200/60">
                        <span class="text-slate-500">Uang Diterima</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($checkoutTransaction->payment, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-4 pt-1">
                        <span class="text-slate-500">Kembalian</span>
                        <span class="font-extrabold text-emerald-600">Rp {{ number_format($checkoutTransaction->change, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                    <button type="button" data-open-receipt-modal class="rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition active:scale-95">
                        Cetak Struk
                    </button>
                    <button type="button" data-close-checkout-modal class="rounded-xl bg-emerald-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-emerald-700 shadow-sm transition active:scale-95">
                        Transaksi Baru
                    </button>
                    <a href="{{ route('transactions.show', $checkoutTransaction) }}" class="rounded-xl bg-slate-100 px-4 py-2.5 text-center text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-200 transition active:scale-95">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if($checkoutTransaction)
        @include('receipt-modal', ['transaction' => $checkoutTransaction])
    @endif

    @vite('resources/js/pos.js')
@endsection
