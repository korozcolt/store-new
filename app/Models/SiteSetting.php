<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'mobile',
        'email',
        'address',
        'facebook',
        'twitter',
        'instagram',
        'whatsapp',
        'taxes',
        'logo',
        'favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'about_us',
        'terms_and_conditions',
        'privacy_policy',
    ];

    protected $casts = [
        'taxes' => 'float',
    ];

    public function currencies()
    {
        return $this->hasOne(Currency::class);
    }

    public function payment_methods()
    {
        return $this->hasOne(PaymentMethod::class);
    }

    public function shipping_methods()
    {
        return $this->hasOne(ShippingMethod::class);
    }
}
