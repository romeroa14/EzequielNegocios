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
use App\Models\Product;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use App\Models\ProductPresentation;

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
        $user = Auth::user();
        $person = Person::where('email', $user->email)->first();

        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->label('Vendedor')
                    ->options(function () {
                        return Person::where('role', 'seller')
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(function ($person) {
                                return [$person->id => $person->full_name . ' - ' . $person->identification_number];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn () => Auth::user()->role === 'admin'),

                Forms\Components\Hidden::make('person_id')
                    ->default($person?->id)
                    ->visible(fn () => Auth::user()->role !== 'admin'),

                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->options(function () use ($person) {
                        if (!$person && !Auth::user()->role === 'admin') return [];
                        
                        // Si es admin, mostrar todos los productos
                        if (Auth::user()->role === 'admin') {
                            return Product::with('creator')
                                ->get()
                                ->mapWithKeys(function ($product) {
                                    $creator = $product->creator ? (' - Por: ' . $product->creator->full_name) : '';
                                    $prefix = $product->is_universal ? '🌎 ' : '📦 ';
                                    return [$product->id => $prefix . $product->name ];
                                });
                        }

                        // Obtener productos universales de todos los productores universales
                        $universalProducts = Product::whereHas('creator', function ($query) {
                            $query->where('is_universal', true);
                        })->where('is_universal', true)->get();

                        // Obtener productos del vendedor actual
                        $sellerProducts = Product::where('person_id', $person->id)
                            ->where('is_universal', false)
                            ->get();

                        // Combinar y formatear los productos
                        $allProducts = $universalProducts->concat($sellerProducts)
                            ->mapWithKeys(function ($product) {
                                $prefix = $product->is_universal ? '🌎 ' : '📦 ';
                                return [$product->id => $prefix . $product->name];
                            });

                        return $allProducts;
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->disabled(fn () => !$person && Auth::user()->role !== 'admin')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product && $product->image) {
                                $set('images', [$product->image]);
                            }
                        }
                    }),

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

                    Forms\Components\Select::make('state_id')
                    ->label('Estado')
                    ->options(function () {
                        return State::query()
                            ->where('country_id', 296) // Venezuela
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('municipality_id', null);
                        $set('parish_id', null);
                    }),
                Forms\Components\Select::make('municipality_id')
                    ->label('Municipio')
                    ->options(function (callable $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return Municipality::query()
                            ->where('state_id', $stateId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('parish_id', null)),
                Forms\Components\Select::make('parish_id')
                    ->label('Parroquia')
                    ->options(function (callable $get) {
                        $municipalityId = $get('municipality_id');
                        if (!$municipalityId) {
                            return [];
                        }   
                        return Parish::query()
                            ->where('municipality_id', $municipalityId)
                            ->pluck('name', 'id');
                    })
                    ->required(),

                Forms\Components\Select::make('product_presentation_id')
                    ->label('Presentación')
                    ->options(ProductPresentation::query()
                        ->where('is_active', true)
                        ->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        $set('presentation_quantity', 1);
                    }),

                Forms\Components\TextInput::make('presentation_quantity')
                    ->label(function (Forms\Get $get) {
                        $presentationId = $get('product_presentation_id');
                        if (!$presentationId) return 'Cantidad';
                        
                        $presentation = ProductPresentation::find($presentationId);
                        return $presentation ? "Cantidad en {$presentation->unit_type}" : 'Cantidad';
                    })
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(0.01)
                    ->step(0.01),

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

                Tables\Columns\TextColumn::make('productPresentation.name')
                    ->label('Presentación')
                    ->searchable(),

                Tables\Columns\TextColumn::make('formatted_presentation')
                    ->label('Cantidad')
                    ->searchable(),

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
