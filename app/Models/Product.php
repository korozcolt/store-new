<?php

namespace App\Models;

use App\Enums\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'images',
        'sku',
        'sale_price',
        'is_active',
        'is_featured',
        'in_stock',
        'on_sale',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => GlobalStatus::class,
        'is_featured' => GlobalStatus::class,
        'in_stock' => GlobalStatus::class,
        'on_sale' => GlobalStatus::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $initials = strtoupper(substr($product->name, 0, 2));
                $randomNumber = random_int(1, 9999);
                $randomNumberString = sprintf('%04d', $randomNumber);
                $product->sku = $initials . $randomNumberString;
            }
        });
    }
}
