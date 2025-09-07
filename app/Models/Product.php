<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'price_cents',
        'discount_percent',
        'stock',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'discount_percent' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_product')
                    ->withPivot('position')
                    ->withTimestamps();
    }

    public function getPriceAttribute()
    {
        return $this->price_cents / 100;
    }

    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percent > 0) {
            return $this->price_cents * (100 - $this->discount_percent) / 10000;
        }
        return $this->price;
    }

    public function getDiscountedPriceCentsAttribute()
    {
        if ($this->discount_percent > 0) {
            return (int) ($this->price_cents * (100 - $this->discount_percent) / 100);
        }
        return $this->price_cents;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}