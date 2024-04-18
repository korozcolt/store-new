<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Faker\Provider\ar_EG\Text;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User name')
                    ->searchable(),

                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('COP')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => match ($record->status) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'canceled' => 'danger',
                    })
                    ->icon(fn ($record) => match ($record->status) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'canceled' => 'heroicon-m-x',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
