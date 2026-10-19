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
        // Fallback for standard multipart file upload (e.g. tests or direct API)
        if ($request->file('image') && ! $request->file('image')->isValid()) {
            $errorCode = $request->file('image')->getError();
            $errorMessage = match ($errorCode) {
                UPLOAD_ERR_INI_SIZE => 'Ukuran file foto melebihi batas upload server. Silakan pilih foto dengan ukuran lebih kecil.',
                UPLOAD_ERR_FORM_SIZE => 'Ukuran file foto melebihi batas yang diizinkan formulir.',
                UPLOAD_ERR_PARTIAL => 'File foto hanya terunggah sebagian. Silakan coba unggah kembali.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server tidak memiliki folder penyimpanan sementara.',
                UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file foto ke disk.',
                default => 'Foto gagal diunggah (Kode: '.$errorCode.'). Silakan pilih foto lain.',
            };

            return back()->withErrors(['image' => $errorMessage])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'image_base64' => ['nullable', 'string'],
        ], [
            'image.uploaded' => 'Foto produk gagal diunggah. Ukuran file kemungkinan melebihi kapasitas server atau file korup.',
            'image.max' => 'Ukuran foto produk maksimal 20 MB.',
            'image.image' => 'File yang diunggah harus berupa gambar yang valid.',
            'image.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU produk sudah terdaftar.',
            'stock.required' => 'Jumlah stok wajib diisi.',
            'buy_price.required' => 'Harga beli wajib diisi.',
            'sell_price.required' => 'Harga jual wajib diisi.',
        ]);

        $image = null;
        if ($request->filled('image_base64') && str_starts_with($request->input('image_base64'), 'data:image')) {
            // Save base64 image directly to MySQL (no temp folders or upload_tmp_dir needed)
            $image = $request->input('image_base64');
        } elseif ($request->hasFile('image')) {
            $image = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'category' => $data['category'] ?? 'Umum',
            'stock' => (int) $data['stock'],
            'buy_price' => (float) $data['buy_price'],
            'sell_price' => (float) $data['sell_price'],
            'description' => $data['description'] ?? null,
            'image' => $image,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        // Fallback for standard multipart file upload (e.g. tests or direct API)
        if ($request->file('image') && ! $request->file('image')->isValid()) {
            $errorCode = $request->file('image')->getError();
            $errorMessage = match ($errorCode) {
                UPLOAD_ERR_INI_SIZE => 'Ukuran file foto melebihi batas upload server. Silakan pilih foto dengan ukuran lebih kecil.',
                UPLOAD_ERR_FORM_SIZE => 'Ukuran file foto melebihi batas yang diizinkan formulir.',
                UPLOAD_ERR_PARTIAL => 'File foto hanya terunggah sebagian. Silakan coba unggah kembali.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server tidak memiliki folder penyimpanan sementara.',
                UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file foto ke disk.',
                default => 'Foto gagal diunggah (Kode: '.$errorCode.'). Silakan pilih foto lain.',
            };

            return back()->withErrors(['image' => $errorMessage])->withInput();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'category' => ['nullable', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:20480'],
            'image_base64' => ['nullable', 'string'],
        ], [
            'image.uploaded' => 'Foto produk gagal diunggah. Ukuran file kemungkinan melebihi kapasitas server atau file korup.',
            'image.max' => 'Ukuran foto produk maksimal 20 MB.',
            'image.image' => 'File yang diunggah harus berupa gambar yang valid.',
            'image.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'name.required' => 'Nama produk wajib diisi.',
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU produk sudah terdaftar.',
            'stock.required' => 'Jumlah stok wajib diisi.',
            'buy_price.required' => 'Harga beli wajib diisi.',
            'sell_price.required' => 'Harga jual wajib diisi.',
        ]);

        $image = $product->image;
        if ($request->filled('image_base64') && str_starts_with($request->input('image_base64'), 'data:image')) {
            if ($product->image && ! str_starts_with($product->image, 'data:image')) {
                Storage::disk('public')->delete($product->image);
            }
            $image = $request->input('image_base64');
        } elseif ($request->hasFile('image')) {
            if ($product->image && ! str_starts_with($product->image, 'data:image')) {
                Storage::disk('public')->delete($product->image);
            }
            $image = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'category' => $data['category'] ?? 'Umum',
            'stock' => (int) $data['stock'],
            'buy_price' => (float) $data['buy_price'],
            'sell_price' => (float) $data['sell_price'],
            'description' => $data['description'] ?? null,
            'image' => $image,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->image && ! str_starts_with($product->image, 'data:image')) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.',
            ]);
        }

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
