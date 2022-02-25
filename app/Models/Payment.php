<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'product_id', 'stripe_charge_id', 'paid_out', 'fees_collected', 'refunded'];

    protected $casts = ['refunded' => 'boolean'];

    public function customer()
    {
        return $this->hasOne(User::class, 'customer_id');
    }

    public function product()
    {
        return $this->hasOne(User::class, 'product_id');
    }
}
