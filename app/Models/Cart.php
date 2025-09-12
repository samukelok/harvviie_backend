<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_CONVERTED = 'converted';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getSubtotalCentsAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price_cents;
        });
    }

    public function getSubtotalAttribute()
    {
        return $this->subtotal_cents / 100;
    }

    public function getTaxCentsAttribute()
    {
        // 15% VAT for South Africa
        return (int) ($this->subtotal_cents * 0.15);
    }

    public function getTaxAttribute()
    {
        return $this->tax_cents / 100;
    }

    public function getTotalCentsAttribute()
    {
        return $this->subtotal_cents + $this->tax_cents;
    }

    public function getTotalAttribute()
    {
        return $this->total_cents / 100;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'status' => self::STATUS_ACTIVE],
            ['user_id' => $userId]
        );
    }

    public static function getOrCreateForSession($sessionId)
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId, 'status' => self::STATUS_ACTIVE],
            ['session_id' => $sessionId]
        );
    }
    
}