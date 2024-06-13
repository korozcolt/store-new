<?php

namespace App\Helpers;

use Illuminate\Support\Number;

class SiteSetting
{

    public static function getLogo()
    {
        $logo = config('site.logo');
        return $logo;
    }

    public static function getFavicon()
    {
        $favicon = config('site.favicon');
        return $favicon;
    }

    public static function getTitle()
    {
        $title = config('site.name');
        return $title;
    }

    //email
    public static function getEmail()
    {
        $email = config('site.email');
        return $email;
    }

    //phone
    public static function getPhone()
    {
        $phone = config('site.phone');
        return $phone;
    }

    public static function getTaxes($value)
    {
        $taxes = Number::format(config('site.taxes'), 2);
        $is_active = config('site.taxes_active');

        if (!$is_active) {
            return 0;
        }

        if ($taxes == 0) {
            return 0;
        }

        return $value * ($taxes / 100);
    }
}
