<?php

namespace App\Models;

use App\Enums\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => GlobalStatus::class,
    ];
}
