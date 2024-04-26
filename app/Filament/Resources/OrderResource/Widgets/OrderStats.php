<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status','new')->count() ?? 0),
            Stat::make('Processing Orders', Order::query()->where('status','processing')->count() ?? 0),
            Stat::make('Shipped Orders', Order::query()->where('status','shipped')->count() ?? 0),
            Stat::make('Average Price', Number::currency(Order::query()->avg('grand_total') ?? 0,'COP') ?? 0),
        ];
    }
}
