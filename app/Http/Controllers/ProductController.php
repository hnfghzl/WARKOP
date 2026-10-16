<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();

        return view('products', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image') && ! $request->file('image')->isValid()) {
            return back()->withErrors(['image' => 'Gambar gagal diunggah. Pastikan ukuran file tidak melebihi batas PHP.'])->withInput();
        }

        $imagePath = $request->file('image')?->store('products', 'public');

        Product::create([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'category' => $data['category'] ?? 'Umum',
            'stock' => (int) $data['stock'],
            'buy_price' => (float) $data['buy_price'],
            'sell_price' => (float) $data['sell_price'],
            'description' => $data['description'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'category' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image') && ! $request->file('image')->isValid()) {
            return back()->withErrors(['image' => 'Gambar gagal diunggah. Pastikan ukuran file tidak melebihi batas PHP.'])->withInput();
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'category' => $data['category'] ?? 'Umum',
            'stock' => (int) $data['stock'],
            'buy_price' => (float) $data['buy_price'],
            'sell_price' => (float) $data['sell_price'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? $product->image,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product->increment('stock', (int) $request->quantity);

        return redirect()->route('products.index')->with('success', 'Stok berhasil ditambah.');
    }

    public function reduceStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $request->quantity;

        if ($product->stock < $quantity) {
            return back()->with('error', 'Jumlah pengurangan stok melebihi stok yang tersedia.');
        }

        $product->decrement('stock', $quantity);

        return redirect()->route('products.index')->with('success', 'Stok berhasil dikurangi.');
    }
}
