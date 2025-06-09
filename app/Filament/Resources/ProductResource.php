<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
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

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Categoría')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('subcategory_id', null);
                    })
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('subcategory_id')
                    ->label('Subcategoría')
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
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->default(function (Forms\Get $get) {
                                return $get('../../category_id');
                            }),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sku_base')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('unit_type')
                    ->options([
                        'kg' => 'Kilogramos',
                        'ton' => 'Toneladas',
                        'saco' => 'Sacos',
                        'caja' => 'Cajas',
                        'unidad' => 'Unidades',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('image')
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
                    ->preserveFilenames()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120) // 5MB
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
                Tables\Columns\TextColumn::make('subcategory.name')
                    ->label('Subcategoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subcategory.category.name')
                    ->label('Categoría')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('Unidad')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Imagen'),
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
                Tables\Filters\SelectFilter::make('subcategory')
                    ->relationship('subcategory', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
