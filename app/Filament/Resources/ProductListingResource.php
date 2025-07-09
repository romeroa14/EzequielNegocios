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
use Illuminate\Support\Facades\Auth;
use App\Models\Person;

class ProductListingResource extends Resource
{
    protected static ?string $model = ProductListing::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Listados';
    protected static ?string $pluralNavigationLabel = 'Listados';
    protected static ?string $pluralModelLabel = 'Listados';
    protected static ?string $modelLabel = 'Listado';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->label('Vendedor')
                    ->relationship('seller', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('unit_price')
                    ->label('Precio Unitario')
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                Forms\Components\TextInput::make('quantity_available')
                    ->label('Cantidad Disponible')
                    ->required()
                    ->numeric()
                    ->minValue(0),

                Forms\Components\Select::make('quality_grade')
                    ->label('Calidad')
                    ->options([
                        'premium' => 'Premium',
                        'standard' => 'Estándar',
                        'economic' => 'Económico',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('harvest_date')
                    ->label('Fecha de Cosecha')
                    ->required(),

                Forms\Components\FileUpload::make('images')
                    ->label('Imágenes')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('listings')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('location_city')
                    ->label('Ciudad')
                    ->required(),

                Forms\Components\TextInput::make('location_state')
                    ->label('Estado')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Imagen')
                    ->circular(),

                Tables\Columns\TextColumn::make('seller.first_name')
                    ->label('Vendedor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity_available')
                    ->label('Disponible')
                    ->numeric(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'inactive',
                        'secondary' => 'sold_out',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                    ]),
                Tables\Filters\SelectFilter::make('quality_grade')
                    ->options([
                        'premium' => 'Premium',
                        'standard' => 'Estándar',
                        'economic' => 'Económico',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'active'])),
                Tables\Actions\Action::make('deactivate')
                    ->label('Desactivar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(fn ($record) => $record->update(['status' => 'inactive'])),
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
        return static::getModel()::where('status', 'pending')->count();
    }
}
