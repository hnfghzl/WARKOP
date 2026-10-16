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

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
