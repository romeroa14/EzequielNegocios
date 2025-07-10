<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductLineResource\Pages;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductLine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductLineResource extends Resource
{
    protected static ?string $model = ProductLine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Líneas de Producto';

    protected static ?string $modelLabel = 'Línea de Producto';

    protected static ?string $pluralModelLabel = 'Líneas de Producto';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('product_category_id')
                            ->label('Categoría')
                            ->options(ProductCategory::query()->where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('product_subcategory_id')
                            ->label('Subcategoría')
                            ->options(function (Forms\Get $get) {
                                $categoryId = $get('product_category_id');
                                if (!$categoryId) {
                                    return [];
                                }
                                return ProductSubcategory::query()
                                    ->where('product_category_id', $categoryId)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('description')
                            ->label('Descripción')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label('Subcategoría')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueLabel('Líneas activas')
                    ->falseLabel('Líneas inactivas')
                    ->native(false),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductLines::route('/'),
            'create' => Pages\CreateProductLine::route('/create'),
            'edit' => Pages\EditProductLine::route('/{record}/edit'),
        ];
    }
} 