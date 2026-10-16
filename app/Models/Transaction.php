<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'cashier_name',
        'subtotal',
        'discount',
        'tax',
        'total',
        'payment',
        'change',
        'payment_method',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
