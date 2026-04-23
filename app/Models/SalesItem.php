<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_name',
        'category',
        'quantity',
        'price',
        'subtotal',
    ];

    //RELATIONSHIP TWN SALESITEM AND SALESTRANSACTION
    public function transaction()
{
    return $this->belongsTo(SalesTransaction::class, 'sales_transaction_id');
}
}
