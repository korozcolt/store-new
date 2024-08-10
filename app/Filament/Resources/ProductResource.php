<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Product Name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('products')
                            ->required()
                            ->placeholder('Product Description'),
                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('images')
                            ->label('Image')
                            ->multiple()
                            ->directory('products')
                            ->reorderable()
                            ->maxFiles(5)
                            ->columnSpan(2),
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('Price')->schema([
                        TextInput::make('price')
                        ->label('Price')
                        ->required()
                        ->prefix('COP $')
                        ->type('number')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->step('0.01')
                        ->placeholder('0.00')
                        ->reactive()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set) {
                            static::updatePriceWithTaxes($state, $set);
                        }),

                    TextInput::make('sale_price')
                        ->label('Sale Price')
                        ->type('number')
                        ->prefix('COP $')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->step('0.01')
                        ->placeholder('0.00')
                        ->reactive()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set) {
                            static::updatePriceWithTaxes($state, $set);
                        }),

                        TextInput::make('price_with_taxes')
                            ->label('Price with Taxes')
                            ->disabled()
                            ->default(fn ($record) => $record ? $record->price_with_taxes : 0)
                            ->prefix('COP $'),
                    ]),

                    Section::make('Associations')->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name'),

                        Select::make('brand_id')
                            ->label('Brand')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name')
                    ]),

                    Section::make('Status')->schema([
                        Toggle::make('in_stock')
                            ->label('Stock')
                            ->default(true)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),

                        Toggle::make('is_featured')
                            ->required(),

                        Toggle::make('on_sale')
                            ->required(),

                        Toggle::make('has_taxes')
                            ->label('Has Taxes')
                            ->required(),
                    ]),
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function updatePriceWithTaxes($state, Set $set)
    {
        $price = $state['price'] ?? 0;
        $salePrice = $state['sale_price'] ?? 0;

        $basePrice = $salePrice > 0 ? $salePrice : $price;

        $taxes = config('site.taxes', 0);
        $isActive = config('site.taxes_active', false);

        $priceWithTaxes = $basePrice;

        if ($isActive && $taxes > 0) {
            $priceWithTaxes += $basePrice * ($taxes / 100);
        }

        $set('price_with_taxes', number_format($priceWithTaxes, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('COP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->money('COP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price_with_taxes')
                    ->label('Price with Taxes')
                    ->money('COP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('in_stock')
                    ->label('Stock')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('on_sale')
                    ->label('Sale')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload(),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->preload(),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                SelectFilter::make('is_featured')
                    ->options([
                        '1' => 'Featured',
                        '0' => 'Not Featured',
                    ]),

                SelectFilter::make('in_stock')
                    ->options([
                        '1' => 'In Stock',
                        '0' => 'Out of Stock',
                    ]),

                SelectFilter::make('on_sale')
                    ->options([
                        '1' => 'On Sale',
                        '0' => 'Not on Sale',
                    ]),
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
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
