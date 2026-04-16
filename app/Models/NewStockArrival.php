<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewStockArrival extends Model
{
    
        protected $fillable = [
        'product_name',
        'category',
        'quantity',
        'cost_price',
        'purchase_date',
        'description',
        'image',
    ];
}
