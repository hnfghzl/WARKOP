<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class POSFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Login Admin');
    }

    public function test_admin_can_login(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_seeded_admin_account_can_login(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs(User::where('email', 'admin@example.com')->first());
    }

    public function test_admin_can_create_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Kopi Americano',
            'sku' => 'KOP-001',
            'category' => 'Minuman',
            'stock' => 15,
            'buy_price' => 5000,
            'sell_price' => 15000,
            'description' => 'Minuman kopi khas.',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'KOP-001', 'stock' => 15]);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('kopi.jpg');

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Kopi Gambar',
            'sku' => 'KOP-IMG-001',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 4000,
            'sell_price' => 12000,
            'image' => $image,
        ]);

        $response->assertRedirect('/products');
        $product = Product::where('sku', 'KOP-IMG-001')->firstOrFail();

        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_image_upload_rejects_invalid_file_with_clear_error(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/products', [
            'name' => 'Produk File Salah',
            'sku' => 'FILE-001',
            'stock' => 1,
            'buy_price' => 1000,
            'sell_price' => 2000,
            'image' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('products', ['sku' => 'FILE-001']);
    }

    public function test_admin_can_add_stock_to_product(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi Americano',
            'sku' => 'KOP-001',
            'category' => 'Minuman',
            'stock' => 5,
            'buy_price' => 5000,
            'sell_price' => 15000,
        ]);

        $response = $this->actingAs($user)->post(route('products.stock.add', $product), [
            'quantity' => 3,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
    }

    public function test_adding_product_with_ajax_returns_updated_cart_panel(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Nasi Goreng',
            'sku' => 'NAS-001',
            'category' => 'Makanan',
            'stock' => 10,
            'buy_price' => 8000,
            'sell_price' => 15000,
        ]);

        $response = $this->actingAs($user)->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('pos.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
            ->assertSee('id="cartPanel"', false)
            ->assertSee('Nasi Goreng');
    }

    public function test_clearing_cart_with_ajax_returns_empty_cart_panel(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Mie Ayam',
            'sku' => 'MIE-001',
            'category' => 'Makanan',
            'stock' => 10,
            'buy_price' => 7000,
            'sell_price' => 12000,
        ]);

        $this->withSession(['cart' => [$product->id => ['quantity' => 1]]]);

        $response = $this->actingAs($user)->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('pos.cart.clear'));

        $response->assertStatus(200)
            ->assertSee('Keranjang masih kosong.');
        $this->assertEmpty(session('cart', []));
    }

    public function test_admin_can_reduce_stock_from_product(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi Latte',
            'sku' => 'KOP-002',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 5500,
            'sell_price' => 18000,
        ]);

        $response = $this->actingAs($user)->post(route('products.stock.reduce', $product), [
            'quantity' => 4,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 6]);
    }

    public function test_admin_can_update_product(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi Tubruk',
            'sku' => 'KOP-003',
            'category' => 'Minuman',
            'stock' => 12,
            'buy_price' => 4000,
            'sell_price' => 12000,
        ]);

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'name' => 'Kopi Tubruk Premium',
            'sku' => 'KOP-003',
            'category' => 'Minuman Premium',
            'stock' => 15,
            'buy_price' => 4500,
            'sell_price' => 14000,
            'description' => 'Versi premium.',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Kopi Tubruk Premium', 'sell_price' => 14000]);
    }

    public function test_admin_can_replace_product_image_when_editing(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldImage = UploadedFile::fake()->image('old.jpg');
        $oldImagePath = $oldImage->store('products', 'public');
        $product = Product::create([
            'name' => 'Air Mineral',
            'sku' => 'AIR-001',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 2000,
            'sell_price' => 4000,
            'image' => $oldImagePath,
        ]);
        $newImage = UploadedFile::fake()->image('aqua.jpg');

        $response = $this->actingAs($user)->post(route('products.update', $product), [
            '_method' => 'PUT',
            'name' => 'Air Mineral Aqua',
            'sku' => 'AIR-001',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 2000,
            'sell_price' => 4500,
            'image' => $newImage,
        ]);

        $response->assertRedirect('/products');
        $product->refresh();
        $this->assertNotSame($oldImagePath, $product->image);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_delete_product(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Teh Tarik',
            'sku' => 'TEH-001',
            'category' => 'Minuman',
            'stock' => 8,
            'buy_price' => 3000,
            'sell_price' => 10000,
        ]);

        $response = $this->actingAs($user)->delete(route('products.destroy', $product));

        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_checkout_redirects_to_success_page_after_successful_payment(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Es Jeruk',
            'sku' => 'JER-001',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 3500,
            'sell_price' => 9000,
        ]);

        $this->withSession(['cart' => [$product->id => ['quantity' => 1]]]);

        $response = $this->actingAs($user)->followingRedirects()->post('/pos/checkout', [
            'customer_name' => 'Rina',
            'payment' => 10000,
            'payment_method' => 'cash',
        ]);

        $transaction = Transaction::latest()->first();

        $response->assertStatus(200)
            ->assertSee('Pembayaran Berhasil')
            ->assertSee('checkoutSuccessModal', false)
            ->assertSee('data-open-receipt-modal', false)
            ->assertSee('Transaksi Baru')
            ->assertSee('Lihat Detail');
        $this->assertDatabaseHas('transactions', ['customer_name' => 'Rina', 'payment_method' => 'cash']);
    }

    public function test_admin_can_reduce_quantity_in_cart_without_removing_item(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Es Teh',
            'sku' => 'TEH-002',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 2500,
            'sell_price' => 7000,
        ]);

        $this->withSession(['cart' => [$product->id => ['quantity' => 2]]]);

        $response = $this->actingAs($user)->post(route('pos.cart.adjust'), [
            'product_id' => $product->id,
            'quantity' => -1,
        ]);

        $response->assertRedirect();
        $this->assertEquals(1, session('cart.'.$product->id.'.quantity'));
    }

    public function test_transaction_detail_page_shows_invoice_items(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi',
            'sku' => 'KOP-001',
            'stock' => 100,
            'buy_price' => 5000,
            'sell_price' => 15000,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260901001',
            'customer_name' => 'Budi',
            'cashier_name' => 'Admin',
            'subtotal' => 15000,
            'discount' => 0,
            'tax' => 1650,
            'total' => 16650,
            'payment' => 20000,
            'change' => 3350,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $transaction->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Kopi',
            'product_sku' => 'KOP-001',
            'quantity' => 1,
            'unit_price' => 15000,
            'subtotal' => 15000,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.show', $transaction));

        $response->assertStatus(200)
            ->assertSee('INV-20260901001')
            ->assertSee('Kopi')
            ->assertSee('Rp 16.650');
    }

    public function test_receipt_page_loads_for_transaction(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi',
            'sku' => 'KOP-001',
            'stock' => 100,
            'buy_price' => 5000,
            'sell_price' => 15000,
        ]);

        $transaction = Transaction::create([
            'invoice_number' => 'INV-20260901002',
            'customer_name' => 'Ani',
            'cashier_name' => 'Admin',
            'subtotal' => 15000,
            'discount' => 0,
            'tax' => 1650,
            'total' => 16650,
            'payment' => 20000,
            'change' => 3350,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $transaction->items()->create([
            'product_id' => $product->id,
            'product_name' => 'Kopi',
            'product_sku' => 'KOP-001',
            'quantity' => 1,
            'unit_price' => 15000,
            'subtotal' => 15000,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.receipt', $transaction));

        $response->assertStatus(200)
            ->assertSee('Struk Pembayaran')
            ->assertSee('INV-20260901002');
    }

    public function test_transaction_history_can_be_searched_by_invoice_or_customer(): void
    {
        $user = User::factory()->create();
        Transaction::create([
            'invoice_number' => 'INV-SEARCH-001',
            'customer_name' => 'Dewi',
            'cashier_name' => 'Admin',
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 0,
            'total' => 10000,
            'payment' => 10000,
            'change' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
        Transaction::create([
            'invoice_number' => 'INV-SEARCH-002',
            'customer_name' => 'Rudi',
            'cashier_name' => 'Admin',
            'subtotal' => 20000,
            'discount' => 0,
            'tax' => 0,
            'total' => 20000,
            'payment' => 20000,
            'change' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get('/transactions?search=Dewi');

        $response->assertStatus(200)
            ->assertSee('INV-SEARCH-001')
            ->assertDontSee('INV-SEARCH-002');
    }

    public function test_sales_report_page_loads_for_authenticated_admin(): void
    {

        $user = User::factory()->create();
        Transaction::create([
            'invoice_number' => 'INV-001',
            'customer_name' => 'Budi',
            'cashier_name' => 'Admin',
            'subtotal' => 20000,
            'discount' => 0,
            'tax' => 0,
            'total' => 20000,
            'payment' => 20000,
            'change' => 0,
            'payment_method' => 'cash',
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)->get('/reports');

        $response->assertStatus(200)
            ->assertSee('Laporan Penjualan')
            ->assertSee('Rp 20.000');
    }

    public function test_sales_report_can_be_filtered_by_date_range(): void
    {
        $user = User::factory()->create();
        Transaction::create([
            'invoice_number' => 'INV-FILTER-001',
            'customer_name' => 'Budi',
            'cashier_name' => 'Admin',
            'subtotal' => 20000,
            'discount' => 0,
            'tax' => 0,
            'total' => 20000,
            'payment' => 20000,
            'change' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => '2026-09-01 10:00:00',
        ]);
        Transaction::create([
            'invoice_number' => 'INV-FILTER-002',
            'customer_name' => 'Ani',
            'cashier_name' => 'Admin',
            'subtotal' => 50000,
            'discount' => 0,
            'tax' => 0,
            'total' => 50000,
            'payment' => 50000,
            'change' => 0,
            'payment_method' => 'cash',
            'status' => 'completed',
            'created_at' => '2026-08-31 10:00:00',
        ]);
        Transaction::where('invoice_number', 'INV-FILTER-001')
            ->update(['created_at' => '2026-09-01 10:00:00']);
        Transaction::where('invoice_number', 'INV-FILTER-002')
            ->update(['created_at' => '2026-08-31 10:00:00']);

        $response = $this->actingAs($user)->get('/reports?start_date=2026-09-01&end_date=2026-09-01');

        $response->assertStatus(200)
            ->assertSee('Rp 20.000')
            ->assertSee('INV-FILTER-001')
            ->assertDontSee('INV-FILTER-002');
    }

    public function test_pos_page_shows_change_due_preview_for_cash_payment(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Teh Botol',
            'sku' => 'TEH-003',
            'category' => 'Minuman',
            'stock' => 20,
            'buy_price' => 3000,
            'sell_price' => 21000,
        ]);

        $this->withSession(['cart' => [$product->id => ['quantity' => 1]]]);

        $response = $this->actingAs($user)->get('/pos');

        $response->assertStatus(200)
            ->assertSee('changeAmount')
            ->assertSee('paymentAmount')
            ->assertSee('inputmode');
    }

    public function test_dashboard_page_loads_for_authenticated_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200)
            ->assertSee('Kasir');
    }

    public function test_products_page_loads_for_authenticated_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200)
            ->assertSee('Daftar Produk')
            ->assertSee('Tambah Produk')
            ->assertSee('id="productFormContainer" data-is-editing="false" class="hidden', false);
    }

    public function test_products_page_shows_form_when_editing_or_create_requested(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Kopi Arabika',
            'sku' => 'ARB-001',
            'stock' => 10,
            'buy_price' => 5000,
            'sell_price' => 12000,
        ]);

        // When create query param is passed
        $responseCreate = $this->actingAs($user)->get('/products?create=1');
        $responseCreate->assertStatus(200)
            ->assertDontSee('id="productFormContainer" data-is-editing="false" class="hidden', false);

        // When edit query param is passed
        $responseEdit = $this->actingAs($user)->get('/products?edit='.$product->id);
        $responseEdit->assertStatus(200)
            ->assertSee('Edit Produk')
            ->assertSee('Batal edit')
            ->assertDontSee('id="productFormContainer" data-is-editing="true" class="hidden', false);
    }

    public function test_dashboard_period_filters_calculate_correctly(): void
    {
        $user = User::factory()->create();

        // Buat transaksi dengan tanggal hari ini
        $product = Product::create([
            'name' => 'Kopi Latte',
            'sku' => 'LAT-001',
            'category' => 'Minuman',
            'stock' => 10,
            'buy_price' => 5000,
            'sell_price' => 25000,
        ]);
        $tx = Transaction::create([
            'invoice_number' => 'INV-TEST-001',
            'customer_name' => 'Budi Santoso',
            'cashier_name' => 'Kasir 1',
            'subtotal' => 50000,
            'discount' => 0,
            'tax' => 5500,
            'total' => 55500,
            'payment' => 60000,
            'change' => 4500,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
        $tx->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 25000,
            'subtotal' => 50000,
        ]);

        // Cek filter hari-ini
        $responseToday = $this->actingAs($user)->get('/?period=hari-ini');
        $responseToday->assertStatus(200)
            ->assertSee('Rp 55.500')
            ->assertSee('Minuman');

        // Cek filter semua
        $responseAll = $this->actingAs($user)->get('/?period=semua');
        $responseAll->assertStatus(200)
            ->assertSee('Rp 55.500');
    }
}
