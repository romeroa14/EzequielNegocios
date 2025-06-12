<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryMovementResource\Pages;
use App\Models\InventoryMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovementResource extends Resource
{
    protected static ?string $model = InventoryMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationGroup = 'Inventario';

    protected static ?string $navigationLabel = 'Movimientos';
    protected static ?string $pluralNavigationLabel = 'Movimientos de Inventario';
    protected static ?string $modelLabel = 'Movimiento';
    protected static ?string $pluralModelLabel = 'Movimientos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) return;
                        
                        $listing = \App\Models\ProductListing::where('product_id', $state)
                            ->latest()
                            ->first();

                        if ($listing) {
                            $set('quantity', $listing->quantity_available);
                            $set('previous_stock', $listing->quantity_available);
                            $set('current_stock', $listing->quantity_available);
                        }
                    }),
                Forms\Components\Select::make('type')
                    ->label('Tipo de Movimiento')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $quantity = floatval($get('quantity'));
                        $previousStock = floatval($get('previous_stock'));
                        
                        if ($state === 'entrada') {
                            $set('current_stock', $previousStock + $quantity);
                        } elseif ($state === 'salida') {
                            $set('current_stock', $previousStock - $quantity);
                        } elseif ($state === 'ajuste') {
                            $set('current_stock', $quantity);
                        }
                    }),
                Forms\Components\TextInput::make('previous_stock')
                    ->label('Stock Anterior')
                    ->disabled()
                    ->numeric(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $type = $get('type');
                        $previousStock = floatval($get('previous_stock'));
                        
                        if ($type === 'entrada') {
                            $set('current_stock', $previousStock + floatval($state));
                        } elseif ($type === 'salida') {
                            $set('current_stock', $previousStock - floatval($state));
                        } elseif ($type === 'ajuste') {
                            $set('current_stock', floatval($state));
                        }
                    }),
                Forms\Components\TextInput::make('current_stock')
                    ->label('Stock Actual')
                    ->disabled()
                    ->numeric(),
                Forms\Components\TextInput::make('batch_number')
                    ->label('Número de Lote')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Fecha de Vencimiento'),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Número de Referencia')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success',
                        'salida' => 'danger',
                        'ajuste' => 'warning',
                        'merma' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Stock Actual')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Lote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Vence')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'entrada' => 'Entrada',
                        'salida' => 'Salida',
                        'ajuste' => 'Ajuste',
                        'merma' => 'Merma'
                    ]),
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->label('Producto'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListInventoryMovements::route('/'),
            'create' => Pages\CreateInventoryMovement::route('/create'),
            'edit' => Pages\EditInventoryMovement::route('/{record}/edit'),
        ];
    }
} 