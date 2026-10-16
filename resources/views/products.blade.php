@php
    $editingProduct = request('edit') ? \App\Models\Product::find(request('edit')) : null;
    $totalProducts = $products->count();
    $lowStockProducts = $products->where('stock', '<=', 5)->count();
    $totalStock = $products->sum('stock');
@endphp

@extends('layout')

@section('title', 'Daftar Produk')

@section('content')
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-blue-600">Inventaris</p>
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Daftar Produk</h1>
            <p class="mt-2 max-w-xl text-slate-500">Kelola katalog, stok, harga, dan gambar produk dari satu tempat.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
            <span class="text-lg">+</span> Buka POS
        </a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Total Produk</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalProducts }}</p><p class="mt-1 text-xs text-slate-400">Produk terdaftar</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Total Stok</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($totalStock, 0, ',', '.') }}</p><p class="mt-1 text-xs text-slate-400">Unit tersedia</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"><p class="text-sm text-slate-500">Perlu Restok</p><p class="mt-2 text-3xl font-bold {{ $lowStockProducts > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ $lowStockProducts }}</p><p class="mt-1 text-xs text-slate-400">Stok 5 atau kurang</p></div>
    </div>

    <div class="mb-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div><h2 class="text-xl font-bold text-slate-900">{{ $editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2><p class="mt-1 text-sm text-slate-500">{{ $editingProduct ? 'Perbarui informasi produk yang dipilih.' : 'Tambahkan produk baru ke katalog penjualan.' }}</p></div>
            @if($editingProduct)
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">Batal edit</a>
            @endif
            </div>
        </div>

        <form action="{{ $editingProduct ? route('products.update', $editingProduct) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 p-6 md:grid-cols-2 xl:grid-cols-3">
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
                <input id="buy_price" name="buy_price" type="number" min="0" step="1" value="{{ $editingProduct->buy_price ?? old('buy_price', 0) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div>
                <label for="sell_price" class="mb-1 block text-sm font-medium text-slate-700">Harga Jual</label>
                <input id="sell_price" name="sell_price" type="number" min="0" step="1" value="{{ $editingProduct->sell_price ?? old('sell_price', 0) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none">
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none" placeholder="Deskripsi produk ...">{{ $editingProduct->description ?? old('description') }}</textarea>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <label for="image" class="mb-2 block text-sm font-semibold text-slate-700">Foto Produk</label>
                <label for="image" class="flex min-h-28 cursor-pointer items-center gap-4 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-4 transition hover:border-blue-400 hover:bg-blue-50">
                    @if($editingProduct?->image)
                        <img id="imagePreview" src="{{ asset('storage/' . $editingProduct->image) }}" alt="{{ $editingProduct->name }}" class="h-20 w-20 rounded-xl object-cover">
                    @else
                        <img id="imagePreview" src="" alt="Preview gambar" class="hidden h-20 w-20 rounded-xl object-cover">
                        <span id="imagePlaceholder" class="flex h-20 w-20 items-center justify-center rounded-xl bg-blue-100 text-3xl text-blue-600">+</span>
                    @endif
                    <span><span class="block text-sm font-semibold text-slate-700">Upload gambar produk</span><span class="mt-1 block text-xs text-slate-400">JPG, PNG, atau WEBP. Maksimal 10 MB.</span></span>
                </label>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                @if($editingProduct?->image)
                    <p class="mt-2 text-xs text-slate-400">Pilih gambar baru untuk mengganti foto saat ini.</p>
                @endif
            </div>

            <div class="md:col-span-2 xl:col-span-3 flex justify-end">
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    {{ $editingProduct ? 'Update Produk' : 'Simpan Produk' }}
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-xl font-bold text-slate-900">Semua Produk</h2><p class="mt-1 text-sm text-slate-500">{{ $totalProducts }} produk dalam katalog</p></div>
            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Data terbaru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Gambar</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">SKU</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($products as $product)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-xl object-cover ring-1 ring-slate-200">
                                @else
                                    <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400">Tanpa gambar</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-800">{{ $product->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $product->sku }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $product->category }}</td>
                            <td class="px-4 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->stock <= 5 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $product->stock }} unit</span></td>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-800">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('products.index', ['edit' => $product->id]) }}" class="rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                            Hapus
                                        </button>
                                    </form>

                                    <form action="{{ route('products.stock.add', $product) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="quantity" value="1" min="1" class="w-20 rounded border border-slate-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none">
                                        <button type="submit" class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                            Tambah
                                        </button>
                                    </form>

                                    <form action="{{ route('products.stock.reduce', $product) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="number" name="quantity" value="1" min="1" class="w-20 rounded border border-slate-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none">
                                        <button type="submit" class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                            Kurang
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @vite('resources/js/products.js')
@endsection
