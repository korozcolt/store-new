<?php

namespace App\Helpers;

use App\Models\SiteSetting as SiteSettingModel;
use Illuminate\Support\Number;

class SiteSettingHelper
{
    public static function get()
    {
        $setting = SiteSettingModel::first();
        return $setting;
    }

    public static function getLogo()
    {
        $setting = SiteSettingModel::first();
        return $setting->logo;
    }

    public static function getFavicon()
    {
        $setting = SiteSettingModel::first();
        return $setting->favicon;
    }

    public static function getTaxes($value)
    {
        $setting = SiteSettingModel::where('is_active',true)->first();
        if ($setting->taxes == 0) {
            return 0;
        }

        return $value * ($setting->taxes / 100);
    }
}
