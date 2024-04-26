<?php

namespace App\Models;

use App\Enums\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'options',
        'fee',
    ];

    protected $casts = [
        'is_active' => GlobalStatus::class,
        'options' => 'array',
        'fee' => 'float',
    ];
}
