<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    protected $fillable = [
        'receipt_no',
        'total_amount',
        'payment_method',
        'cashier_id',
    ];




    //RELATIONSHIP TWN SALESITEM AND SALESTRANSACTION
    public function items()
{
    return $this->hasMany(SalesItem::class, 'transaction_id');
}
}
