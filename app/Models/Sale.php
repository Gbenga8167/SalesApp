<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'product_name',
        'category',
        'quantity',
        'selling_price',
        'amount',
    ];
}
