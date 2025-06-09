<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductListingResource\Pages;
use App\Filament\Resources\ProductListingResource\RelationManagers;
use App\Models\ProductListing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class ProductListingResource extends Resource
{
    protected static ?string $model = ProductListing::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\Select::make('subcategory_id')
                            ->relationship('subcategory', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                    ]),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('quantity_available')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('min_quantity_order')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('max_quantity_order')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Select::make('quality_grade')
                    ->options([
                        'premium' => 'Premium',
                        'standard' => 'Estándar',
                        'economic' => 'Económico',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('harvest_date')
                    ->required(),
                Forms\Components\DatePicker::make('expiry_date')
                    ->required(),
                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location_city')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location_state')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('pickup_available')
                    ->required(),
                Forms\Components\Toggle::make('delivery_available')
                    ->required(),
                Forms\Components\TextInput::make('delivery_radius_km')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (Forms\Get $get): bool => $get('delivery_available')),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                        'expired' => 'Expirado',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('featured_until'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_available')
                    ->label('Cantidad Disponible')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio Unitario')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quality_grade')
                    ->label('Calidad'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'sold_out' => 'warning',
                        'inactive' => 'danger',
                        'expired' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                        'expired' => 'Expirado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductListings::route('/'),
            'create' => Pages\CreateProductListing::route('/create'),
            'edit' => Pages\EditProductListing::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }
}
