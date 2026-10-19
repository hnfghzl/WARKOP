<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'category',
        'stock',
        'buy_price',
        'sell_price',
        'description',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'integer',
            'sell_price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
