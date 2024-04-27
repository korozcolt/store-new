<?php

namespace App\Models;

use App\Enums\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => GlobalStatus::class,
    ];

    public function siteSetting()
    {
        return $this->belongsTo(SiteSetting::class);
    }

}
