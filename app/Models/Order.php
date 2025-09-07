<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'items',
        'amount_cents',
        'status',
        'shipping_address',
        'placed_at',
    ];

    protected $casts = [
        'items' => 'array',
        'amount_cents' => 'integer',
        'shipping_address' => 'array',
        'placed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
            if (empty($order->placed_at)) {
                $order->placed_at = now();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'HV-' . now()->format('Ymd') . '-';
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount_cents / 100;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByDateRange($query, $dateFrom = null, $dateTo = null)
    {
        if ($dateFrom) {
            $query->where('placed_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('placed_at', '<=', $dateTo);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}