<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\ProductLine;
use App\Models\ProductPresentation;
use App\Models\ProductSubcategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UniversalProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'universalProducts';

    protected static ?string $title = 'Productos Universales';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
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
                    ])->columns(3),

                Forms\Components\Section::make('Detalles del Producto')
                    ->schema([
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
                            ->disk(app()->environment('production') ? 'r2' : 'public')
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
                    ]),

                Forms\Components\Hidden::make('is_universal')
                    ->default(true),
                Forms\Components\Hidden::make('creator_user_id')
                    ->default(fn () => Auth::id()),
                Forms\Components\Hidden::make('person_id')
                    ->default(null)
                    ->dehydrated(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productSubcategory.name')
                    ->label('Subcategoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productLine.name')
                    ->label('Línea')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productPresentation.name')
                    ->label('Unidad')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk(app()->environment('production') ? 'r2' : 'public')
                    ->visibility('public')
                    ->size(100)
                    ->square(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_universal')
                    ->label('Universal')
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn (RelationManager $livewire): bool => 
                        $livewire->getOwnerRecord()->isUniversalProducer()
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (RelationManager $livewire): bool => 
                        $livewire->getOwnerRecord()->isUniversalProducer()
                    ),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (RelationManager $livewire): bool => 
                        $livewire->getOwnerRecord()->isUniversalProducer()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (RelationManager $livewire): bool => 
                            $livewire->getOwnerRecord()->isUniversalProducer()
                        ),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->role === 'producer';
    }
} 