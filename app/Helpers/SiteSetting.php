<?php

namespace App\Helpers;

use App\Models\SiteSetting as SiteSettingModel;

class SiteSetting
{
    public static function get()
    {
        $setting = SiteSettingModel::first();
        return $setting;
    }
}
