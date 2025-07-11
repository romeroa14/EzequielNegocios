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
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('product_category_id')
                            ->label('Categoría')
                            ->options(ProductCategory::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('product_subcategory_id', null)),
                        Forms\Components\Select::make('product_subcategory_id')
                            ->label('Subcategoría')
                            ->options(function (callable $get) {
                                $categoryId = $get('product_category_id');
                                if (!$categoryId) return [];
                                return ProductSubcategory::where('product_category_id', $categoryId)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('product_line_id', null)),
                        Forms\Components\Select::make('product_line_id')
                            ->label('Línea de Producto')
                            ->options(function (callable $get) {
                                $subcategoryId = $get('product_subcategory_id');
                                $categoryId = $get('product_category_id');
                                if (!$subcategoryId || !$categoryId) return [];
                                return ProductLine::where('product_subcategory_id', $subcategoryId)
                                    ->where('product_category_id', $categoryId)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                        Forms\Components\Select::make('brand_id')
                            ->label('Marca')
                            ->options(Brand::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\Select::make('product_presentation_id')
                            ->label('Presentación')
                            ->options(ProductPresentation::where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles del Producto')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(1000),
                        Forms\Components\TextInput::make('sku_base')
                            ->label('SKU Base')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('custom_quantity')
                            ->label('Cantidad Personalizada')
                            ->numeric()
                            ->minValue(0.01)
                            ->default(1),
                        Forms\Components\FileUpload::make('image')
                            ->label('Imagen')
                            ->image()
                            ->directory('products'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Hidden::make('is_universal')
                    ->default(true),
                Forms\Components\Hidden::make('creator_user_id')
                    ->default(fn () => Auth::id()),
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
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productSubcategory.name')
                    ->label('Subcategoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productPresentation.name')
                    ->label('Presentación'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Imagen'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
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