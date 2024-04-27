<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Resources\SiteSettingResource\RelationManagers;
use App\Filament\Resources\SiteSettingResource\RelationManagers\CurrenciesRelationManager;
use App\Filament\Resources\SiteSettingResource\RelationManagers\PaymentsMethodsRelationManager;
use App\Filament\Resources\SiteSettingResource\RelationManagers\ShippingMethodsRelationManager;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?string $navigationGroup = 'Site Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Site Information')->schema([
                Section::make('General Information')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Site Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('mobile')
                        ->label('Mobile')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('address')
                        ->label('Address')
                        ->required()
                        ->maxLength(255),
                ])->columnSpan(2),
                Section::make('Social Information')->schema([
                    Forms\Components\TextInput::make('facebook')
                        ->label('Facebook')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('twitter')
                        ->label('Twitter')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('instagram')
                        ->label('Instagram')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('Whatsapp')
                        ->maxLength(255),
                ])->columnSpan(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('phone')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('mobile')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('facebook')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('twitter')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('instagram')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('whatsapp')
                ->searchable()
                ->sortable(),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            CurrenciesRelationManager::class,
            PaymentsMethodsRelationManager::class,
            ShippingMethodsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'create' => Pages\CreateSiteSetting::route('/create'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
