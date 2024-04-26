<?php

namespace App\Models;

use App\Enums\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active'];

    protected $casts = [
        'is_active' => GlobalStatus::class,
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
