<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Kopi Susu',
                'sku' => 'KOP-001',
                'category' => 'Minuman',
                'stock' => 25,
                'buy_price' => 8000,
                'sell_price' => 15000,
                'description' => 'Kopi susu kekinian dengan rasa lembut.',
            ],
            [
                'name' => 'Mie Goreng',
                'sku' => 'MIE-001',
                'category' => 'Makanan',
                'stock' => 18,
                'buy_price' => 12000,
                'sell_price' => 22000,
                'description' => 'Mie goreng dengan bumbu khas.',
            ],
            [
                'name' => 'Roti Coklat',
                'sku' => 'ROT-001',
                'category' => 'Cemilan',
                'stock' => 30,
                'buy_price' => 5000,
                'sell_price' => 10000,
                'description' => 'Roti lembut dengan isian coklat.',
            ],
            [
                'name' => 'Teh Lemon',
                'sku' => 'TEH-001',
                'category' => 'Minuman',
                'stock' => 40,
                'buy_price' => 7000,
                'sell_price' => 13000,
                'description' => 'Minuman teh dingin dengan lemon.',
            ],
            [
                'name' => 'Nasi Goreng',
                'sku' => 'NAS-001',
                'category' => 'Makanan',
                'stock' => 16,
                'buy_price' => 15000,
                'sell_price' => 28000,
                'description' => 'Nasi goreng spesial dengan telur.',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }
    }
}
