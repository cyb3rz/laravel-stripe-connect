<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['seller_id', 'upc', 'name', 'price', 'image', 'description'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
