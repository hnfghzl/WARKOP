@php
    $editingProduct = request('edit') ? \App\Models\Product::find(request('edit')) : null;
    $shouldShowForm = $editingProduct || $errors->any() || request('create');
    $totalProducts = $products->count();
    $lowStockProducts = $products->where('stock', '<=', 5)->count();
    $totalStock = $products->sum('stock');
@endphp

@extends('layout')

@section('title', 'Daftar Produk')

@section('content')
    <div class="mb-6 sm:mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-1 text-xs sm:text-sm font-semibold uppercase tracking-wider text-blue-600">Inventaris</p>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Daftar Produk</h1>
            <p class="mt-1 sm:mt-2 max-w-xl text-xs sm:text-sm text-slate-500">Kelola katalog, stok, harga, dan gambar produk dari satu tempat.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            @if($editingProduct)
                <a href="{{ route('products.index', ['create' => 1]) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Tambah Produk Baru</span>
                </a>
            @else
                <button type="button" id="btnToggleProductForm" data-action="toggle-product-form" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 transition-transform duration-200" id="btnToggleProductFormIcon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span id="btnToggleProductFormText">{{ $shouldShowForm ? 'Tutup Form' : 'Tambah Produk' }}</span>
                </button>
            @endif
            <a href="{{ route('pos.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Buka POS</span>
            </a>
        </div>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Total Produk</p><p id="statTotalProducts" class="mt-2 text-3xl font-bold text-slate-900">{{ $totalProducts }}</p><p class="mt-1 text-xs text-slate-400">Produk terdaftar</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Total Stok</p><p id="statTotalStock" class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($totalStock, 0, ',', '.') }}</p><p class="mt-1 text-xs text-slate-400">Unit tersedia</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Perlu Restok</p><p id="statLowStock" class="mt-2 text-3xl font-bold {{ $lowStockProducts > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $lowStockProducts }}</p><p class="mt-1 text-xs text-slate-400">Stok 5 atau kurang</p></div>
    </div>

    <div id="productFormContainer" data-is-editing="{{ $editingProduct ? 'true' : 'false' }}" class="{{ $shouldShowForm ? '' : 'hidden' }} mb-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all duration-300">
        <div class="border-b border-slate-200 px-6 py-5 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 text-sm">
                            {{ $editingProduct ? '✏️' : '📦' }}
                        </span>
                        {{ $editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $editingProduct ? 'Perbarui informasi produk yang dipilih.' : 'Tambahkan produk baru ke katalog penjualan.' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if($editingProduct)
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                            ✕ Batal edit
                        </a>
                    @else
                        <button type="button" data-action="close-product-form" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                            ✕ Batal / Tutup
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mx-6 mt-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                <p class="font-semibold mb-1">Gagal menyimpan produk:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="productForm" action="{{ $editingProduct ? route('products.update', $editingProduct) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:gap-6 p-4 sm:p-6 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            @if($editingProduct)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Produk</label>
                <input id="name" name="name" type="text" value="{{ $editingProduct->name ?? old('name') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none" placeholder="Coklat Susu">
            </div>

            <div>
                <label for="sku" class="mb-1 block text-sm font-medium text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ $editingProduct->sku ?? old('sku') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none" placeholder="CK-001">
            </div>

            <div>
                <label for="category" class="mb-1 block text-sm font-medium text-slate-700">Kategori</label>
                <input id="category" name="category" type="text" value="{{ $editingProduct->category ?? old('category', 'Umum') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="stock" class="mb-1 block text-sm font-medium text-slate-700">Stok Awal</label>
                <input id="stock" name="stock" type="number" min="0" value="{{ $editingProduct->stock ?? old('stock', 0) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="buy_price" class="mb-1 block text-sm font-medium text-slate-700">Harga Beli</label>
                <div class="relative mt-1 rounded-xl shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <span class="text-sm font-bold text-slate-400">Rp</span>
                    </div>
                    <input id="buy_price" name="buy_price" type="number" min="0" step="1" value="{{ old('buy_price', isset($editingProduct) ? (int) $editingProduct->buy_price : '') }}" placeholder="0" required class="w-full rounded-xl border border-slate-300 pl-11 pr-3 py-2.5 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label for="sell_price" class="mb-1 block text-sm font-medium text-slate-700">Harga Jual</label>
                <div class="relative mt-1 rounded-xl shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                        <span class="text-sm font-bold text-slate-400">Rp</span>
                    </div>
                    <input id="sell_price" name="sell_price" type="number" min="0" step="1" value="{{ old('sell_price', isset($editingProduct) ? (int) $editingProduct->sell_price : '') }}" placeholder="0" required class="w-full rounded-xl border border-slate-300 pl-11 pr-3 py-2.5 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none" placeholder="Deskripsi produk ...">{{ $editingProduct->description ?? old('description') }}</textarea>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label for="image" class="mb-2 block text-sm font-semibold text-slate-700">Foto Produk</label>
                <label for="image" class="flex min-h-28 cursor-pointer items-center gap-4 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-50">
                    @if($editingProduct?->image)
                        <img id="imagePreview" src="{{ $editingProduct->image_url }}" alt="{{ $editingProduct->name }}" class="h-20 w-20 rounded-xl object-cover">
                    @else
                        <img id="imagePreview" src="" alt="Preview gambar" class="hidden h-20 w-20 rounded-xl object-cover">
                        <span id="imagePlaceholder" class="flex h-20 w-20 items-center justify-center rounded-xl bg-blue-100 text-3xl text-blue-600">+</span>
                    @endif
                    <span>
                        <span class="block text-sm font-semibold text-slate-700">Upload gambar produk</span>
                        <span class="mt-1 block text-xs text-slate-400">JPG, PNG, atau WEBP. Tersimpan langsung ke database.</span>
                    </span>
                </label>
                <!-- File picker without name attribute so it never triggers PHP multipart upload_tmp_dir errors -->
                <input id="image" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                <input type="hidden" name="image_base64" id="imageBase64" value="{{ old('image_base64') }}">
                @if($editingProduct?->image)
                    <p class="mt-2 text-xs text-slate-400">Pilih gambar baru untuk mengganti foto saat ini.</p>
                @endif
            </div>

            <div class="md:col-span-2 xl:col-span-3 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-3 border-t border-slate-100">
                @if($editingProduct)
                    <a href="{{ route('products.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-xs sm:text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </a>
                @else
                    <button type="button" data-action="close-product-form" class="rounded-xl border border-slate-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </button>
                @endif
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm shadow-blue-500/20 transition hover:bg-blue-700 active:scale-95">
                    {{ $editingProduct ? 'Update Produk' : 'Simpan Produk' }}
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-xl font-bold text-slate-900">Semua Produk</h2><p class="mt-1 text-sm text-slate-500">{{ $totalProducts }} produk dalam katalog</p></div>
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Data terbaru</span>
                @if(!$editingProduct)
                    <button type="button" data-action="open-product-form" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3.5 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                        <span>+</span> Tambah Produk
                    </button>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[760px] w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody" class="divide-y divide-slate-200">
                    @foreach($products as $product)
                        <tr id="product-row-{{ $product->id }}" data-product-id="{{ $product->id }}" data-stock="{{ $product->stock }}" class="transition-all duration-300 hover:bg-slate-50">
                            <td class="px-6 py-4">
                                @if($product->image)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-xl object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400">Tanpa gambar</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-800 font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600 font-mono">{{ $product->sku }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $product->category }}</td>
                            <td class="px-4 py-4 whitespace-nowrap"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->stock <= 5 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $product->stock }} unit</span></td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('products.index', ['edit' => $product->id]) }}" class="inline-flex items-center gap-1 rounded-xl bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100 active:scale-95" title="Edit Produk">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        <span>Edit</span>
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block delete-product-form" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95" title="Hapus Produk">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>

                                    <form method="POST" class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-0.5 shadow-sm">
                                        @csrf
                                        <button type="submit" formaction="{{ route('products.stock.reduce', $product) }}" title="Kurang Stok" class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-xs font-bold text-amber-700 hover:bg-amber-100 transition active:scale-95">
                                            −
                                        </button>
                                        <input type="number" name="quantity" value="1" min="1" class="h-7 w-12 text-center text-xs font-semibold text-slate-700 focus:bg-slate-50 focus:outline-none" title="Jumlah unit stok untuk ditambah/dikurang">
                                        <button type="submit" formaction="{{ route('products.stock.add', $product) }}" title="Tambah Stok" class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition active:scale-95">
                                            +
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="emptyProductRow" class="{{ $products->isEmpty() ? '' : 'hidden' }}">
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            <div class="flex flex-col items-center justify-center py-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-2xl text-slate-400 mb-3">
                                    📦
                                </div>
                                <p class="font-semibold text-slate-700 text-base">Belum ada produk.</p>
                                <p class="text-xs text-slate-400 mt-1 mb-4">Mulai dengan menambahkan produk pertama ke dalam katalog.</p>
                                <button type="button" data-action="open-product-form" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                                    <span>+</span> Tambah Produk Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating Toast Notification Container -->
    <div id="productToastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2 pointer-events-none"></div>

    @vite('resources/js/products.js')
@endsection
