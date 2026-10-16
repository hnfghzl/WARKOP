<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        $checkoutTransaction = null;

        if ($transactionId = session('checkout_transaction_id')) {
            $checkoutTransaction = Transaction::find($transactionId);
        }

        return view('pos', array_merge([
            'products' => $products,
            'checkoutTransaction' => $checkoutTransaction,
        ], $this->cartData()));
    }

    private function cartData(): array
    {
        $cart = session('cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (! $product) {
                continue;
            }

            $itemSubtotal = $product->sell_price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $cartItems[] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->sell_price,
                'quantity' => $item['quantity'],
                'subtotal' => $itemSubtotal,
            ];
        }

        return compact('cartItems', 'subtotal');
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);
        $quantity = (int) $request->quantity;

        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;
        if (($currentQuantity + $quantity) > $product->stock) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart[$product->id] = [
            'quantity' => $currentQuantity + $quantity,
        ];

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->view('pos-cart', $this->cartData());
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function adjustCartItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        $productId = (int) $request->product_id;
        $adjustment = (int) $request->quantity;

        if (! isset($cart[$productId])) {
            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        $newQuantity = $cart[$productId]['quantity'] + $adjustment;

        if ($newQuantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $newQuantity;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Jumlah item di keranjang berhasil diubah.');
    }

    public function removeCartItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function clearCart(Request $request)
    {
        session()->forget('cart');

        if ($request->ajax()) {
            return response()->view('pos-cart', $this->cartData());
        }

        return back()->with('success', 'Keranjang berhasil dibersihkan.');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50|in:cash,qris',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $subtotal = 0;
        $transactionItems = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if (! $product) {
                continue;
            }

            if ($item['quantity'] > $product->stock) {
                return back()->with('error', 'Stok produk '.$product->name.' tidak mencukupi.');
            }

            $lineSubtotal = $product->sell_price * $item['quantity'];
            $subtotal += $lineSubtotal;

            $transactionItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $item['quantity'],
                'unit_price' => $product->sell_price,
                'subtotal' => $lineSubtotal,
            ];
        }

        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;
        $payment = (float) $request->payment;
        $change = $payment - $total;

        if ($payment < $total) {
            return back()->with('error', 'Pembayaran kurang dari total transaksi.');
        }

        return DB::transaction(function () use ($request, $transactionItems, $subtotal, $tax, $total, $payment, $change) {
            $transaction = Transaction::create([
                'invoice_number' => 'INV-'.now()->format('YmdHis'),
                'customer_name' => $request->customer_name ?? 'Umum',
                'cashier_name' => 'Kasir',
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => $tax,
                'total' => $total,
                'payment' => $payment,
                'change' => $change,
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => 'completed',
            ]);

            foreach ($transactionItems as $item) {
                $transaction->items()->create($item);

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            session()->forget('cart');

            return redirect()->route('pos.index')
                ->with('checkout_transaction_id', $transaction->id)
                ->with('success', 'Transaksi berhasil disimpan.');
        });
    }
}
