<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductLine;
use App\Models\Brand;
use App\Models\ProductPresentation;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $pluralNavigationLabel = 'Productos';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('person_id')
                    ->label('Vendedor')
                    ->options(Person::query()
                        ->where('role', 'seller')
                        ->where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($person) => [$person->id => $person->full_name]))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\Select::make('product_category_id')
                    ->relationship('productCategory', 'name')
                    ->label('Categoría')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('product_subcategory_id', null);
                    })
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la categoría')
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Select::make('product_subcategory_id')
                    ->label('Subcategoría')
                    ->options(function (Forms\Get $get) {
                        $categoryId = $get('product_category_id');
                        
                        if (!$categoryId) {
                            return [];
                        }
                        
                        return \App\Models\ProductSubcategory::query()
                            ->where('product_category_id', $categoryId)
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('product_line_id', null);
                    })
                    ->createOptionForm([
                        Forms\Components\Select::make('category_id')
                            ->relationship('productCategory', 'name')
                            ->required()
                            ->default(function (Forms\Get $get) {
                                return $get('../../product_category_id');
                            }),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),
                Forms\Components\Select::make('product_line_id')
                    ->label('Línea de producto')
                    ->options(function (Forms\Get $get) {
                        $subcategoryId = $get('product_subcategory_id');
                        
                        if (!$subcategoryId) {
                            return ProductLine::query()
                                ->where('is_active', true)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        
                        return ProductLine::query()
                            ->where('product_subcategory_id', $subcategoryId)
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Forms\Components\Grid::make()
                    ->schema([
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->options(Brand::query()->where('is_active', true)->pluck('name', 'id'))
                    ->required(),

                Forms\Components\Select::make('product_presentation_id')
                    ->label('Presentación')
                    ->options(ProductPresentation::query()->where('is_active', true)->pluck('name', 'id'))
                            ->required()

                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('custom_quantity', 1);
                            }),
                        Forms\Components\TextInput::make('custom_quantity')
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
                    ])
                    ->columns(3),
                    
                Forms\Components\Grid::make()
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del producto')
                    ->required()
                    ->maxLength(255),
                
                
                Forms\Components\TextInput::make('sku_base')
                    ->label('SKU base')
                    ->required()
                    ->maxLength(255),
                ])
                ->columns(2),
                
                Forms\Components\FileUpload::make('image')
                    ->label('Imagen del producto')
                    ->image()
                    ->required()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->imagePreviewHeight('250')
                    ->loadingIndicatorPosition('left')
                    ->panelLayout('integrated')
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->preserveFilenames()
                    ->columnSpanFull(),
                    
                    Forms\Components\Textarea::make('description')
                    ->label('Descripción del producto')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Forms\Components\KeyValue::make('seasonal_info')
                    ->label('Información Estacional')
                    ->addButtonLabel('Agregar Información')
                    ->keyLabel('Temporada')
                    ->valueLabel('Descripción')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('productSubcategory.name')
                    ->label('Subcategoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('Categoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('Unidad')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->visibility('public')
                    ->size(100)
                    ->square(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
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
