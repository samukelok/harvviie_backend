<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price_cents',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_cents' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUnitPriceAttribute()
    {
        return $this->unit_price_cents / 100;
    }

    public function getTotalCentsAttribute()
    {
        return $this->quantity * $this->unit_price_cents;
    }

    public function getTotalAttribute()
    {
        return $this->total_cents / 100;
    }
}