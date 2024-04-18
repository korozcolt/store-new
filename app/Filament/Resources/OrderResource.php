<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'paypal' => 'PayPal',
                                'cod' => 'Cash on Delivery',
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'on_hold' => 'On Hold',
                            ])
                            ->default('pending')
                            ->required(),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->inline()
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'canceled' => 'Canceled',
                            ])
                            ->default('new')
                            ->required()
                            ->colors([
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'success',
                                'delivered' => 'success',
                                'canceled' => 'danger',
                            ])
                            ->icons([
                                'new' => 'heroicon-o-sparkles',
                                'processing' => 'heroicon-o-arrow-path',
                                'shipped' => 'heroicon-o-truck',
                                'delivered' => 'heroicon-o-check',
                                'canceled' => 'heroicon-o-x-circle',
                            ]),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'cop' => 'COP',
                                'usd' => 'USD',
                                'eur' => 'EUR',
                                'gbp' => 'GBP',
                            ])
                            ->default('cop')
                            ->required(),

                        select::make('shipping_method')
                            ->label('Shipping Method')
                            ->options([
                                'standard' => 'Standard',
                                'express' => 'Express',
                                'next_day' => 'Next Day',
                                'same_day' => 'Same Day',
                            ])
                            ->default('standard')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
                    /////////////////////////////////// Items ///////////////////////////////////
                    Section::make()->schema([
                        Repeater::make('items')
                        ->label('Items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Product')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpan(4)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Set $set) => $set('unit_amount', Product::find($state)->sale_price ?? Product::find($state)->price ?? 0))
                                ->afterStateUpdated(fn ($state, Set $set) => $set('total_amount', Product::find($state)->sale_price ?? Product::find($state)->price ?? 0)),

                            TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required()
                                ->columnSpan(2)
                                ->reactive()
                                ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount'))),

                            TextInput::make('unit_amount')
                                ->label('Unit Amount')
                                ->numeric()
                                ->prefix('COP $')
                                ->step('0.01')
                                ->placeholder('0.00')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(3),

                            TextInput::make('total_amount')
                                ->label('Total Amount')
                                ->numeric()
                                ->prefix('COP $')
                                ->step('0.01')
                                ->placeholder('0.00')
                                ->required()
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(3),
                        ])->columns(12),

                        Placeholder::make('grand_total_placeholder')
                            ->label('Total')
                            ->content(function (Get $get, Set $set) {
                                $total = 0;
                                if(!$repeaters = $get('items')) {
                                    return $total;
                                }

                                foreach ($repeaters as $key => $repeater) {
                                    $total += $get("items.{$key}.total_amount");
                                }
                                $set('grand_total', $total);

                                return Number::currency($total, 'COP');
                            }),


                        Hidden::make('grand_total')
                            ->default(0),
                    ])
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->numeric()
                    ->money('COP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('shipping_method')
                    ->label('Shipping Method')
                    ->searchable()
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'canceled' => 'Canceled',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'danger' : 'success';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
