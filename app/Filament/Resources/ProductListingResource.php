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

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $currentUser = Auth::user();
        $person = Person::where('user_id', $currentUser->id)->first();

        if (!$person) {
            throw new \Exception('Debe crear su perfil de vendedor antes de crear listados.');
        }

        return $form
            ->schema([
                Forms\Components\Hidden::make('person_id')
                    ->default($person->id)
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, ?ProductListing $record) {
                        if (!$record || empty($record->images)) {
                            $product = \App\Models\Product::find($state);
                            if ($product) {
                                $set('images', [$product->image]);
                            }
                        }
                    })
                    ->createOptionForm([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('subcategory_id', null)),
                        Forms\Components\Select::make('subcategory_id')
                            ->relationship('subcategory', 'name')
                            ->required()
                            ->options(function (Forms\Get $get) {
                                $categoryId = $get('category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return \App\Models\ProductSubcategory::query()
                                    ->where('category_id', $categoryId)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            }),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                    ]),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('quantity_available')
                    ->label('Cantidad Disponible')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Precio Unitario')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('wholesale_price')
                    ->label('Precio Mayorista')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('min_quantity_order')
                    ->label('Cantidad Mínima de Orden')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('max_quantity_order')
                    ->label('Cantidad Máxima de Orden')
                    ->required()
                    ->numeric()
                    ->minValue(1),
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
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Fecha de Vencimiento')
                    ->default(now()->addDays(30))
                    ->required(),
                Forms\Components\FileUpload::make('images')
                    ->label('Imágenes')
                    ->image()
                    ->multiple()
                    ->required()
                    ->disk('public')
                    ->directory('listings')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->storeFileNamesIn('original_filenames')
                    ->getUploadedFileNameForStorageUsing(
                        fn (Forms\Components\FileUpload $component, string $fileName): string => 
                            'listing-' . str()->random(8) . '-' . time() . '.' . pathinfo($fileName, PATHINFO_EXTENSION)
                    )
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120) // 5MB
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location_city')
                    ->label('Ciudad')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location_state')
                    ->label('Estado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('pickup_available')
                    ->label('Pickup Disponible')
                    ->required(),
                Forms\Components\Toggle::make('delivery_available')
                    ->label('Delivery Disponible')
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('delivery_radius_km')
                    ->label('Radio de Entrega')
                    ->numeric()
                    ->minValue(0)
                    ->required(fn (Forms\Get $get): bool => $get('delivery_available'))
                    ->visible(fn (Forms\Get $get): bool => $get('delivery_available'))
                    ->helperText('Radio de entrega en kilómetros'),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                        'expired' => 'Expirado',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('featured_until')
                    ->label('Destacado Hasta')
                    ->required()
                    ->default(now()->addDays(30)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->label('Imagen')
                    ->square()
                    ->width(200)
                    ->height(200),
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
                Tables\Columns\TextColumn::make('wholesale_price')
                    ->label('Precio Mayorista')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quality_grade')
                    ->label('Calidad')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'premium' => 'Premium',
                        'standard' => 'Estándar',
                        'economic' => 'Económico',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'premium' => 'success',
                        'standard' => 'warning',
                        'economic' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => 'Activo',
                        'sold_out' => 'Agotado',
                        'inactive' => 'Inactivo',
                        'expired' => 'Expirado',
                        default => ucfirst($state),
                    })
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
