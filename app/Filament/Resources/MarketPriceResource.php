<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketPriceResource\Pages;
use App\Models\MarketPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketPriceResource extends Resource
{
    protected static ?string $model = MarketPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Gestión de Precios';

    protected static ?string $navigationLabel = 'Precios de Mercado';

    protected static ?string $modelLabel = 'Precio de Mercado';

    protected static ?string $pluralModelLabel = 'Precios de Mercado';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Producto')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Bs.')
                    ->label('Precio')
                    ->columnSpan(1),

                Forms\Components\Select::make('currency')
                    ->options([
                        'VES' => 'Bolívares (VES)',
                        'USD' => 'Dólares (USD)',
                    ])
                    ->default('VES')
                    ->required()
                    ->label('Moneda')
                    ->columnSpan(1),

                Forms\Components\DatePicker::make('price_date')
                    ->required()
                    ->default(now())
                    ->label('Fecha del Precio')
                    ->columnSpan(1),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Precio Activo')
                    ->columnSpan(1),

                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->placeholder('Observaciones sobre el precio, condiciones especiales, etc.')
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('updated_by')
                    ->default(fn () => auth()->guard('person')->id()),
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

                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money('VES')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Moneda')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VES' => 'success',
                        'USD' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price_date')
                    ->label('Última Actualización')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updatedBy.full_name')
                    ->label('Actualizado por')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->label('Producto')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Solo Activos'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit_price')
                    ->label('Editar Precio')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->modalHeading('Editar Precio del Producto')
                    ->modalDescription('Modifica solo el precio del producto existente.')
                    ->form([
                        Forms\Components\TextInput::make('price')
                            ->label('Nuevo Precio')
                            ->required()
                            ->numeric()
                            ->prefix('Bs.')
                            ->rules(['min:0'])
                            ->default(fn (MarketPrice $record) => $record->price),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas de Cambio')
                            ->placeholder('Razón del cambio de precio...')
                            ->rows(3)
                            ->default(fn (MarketPrice $record) => $record->notes),
                    ])
                    ->action(function (MarketPrice $record, array $data): void {
                        // Guardar el precio anterior en el historial
                        \App\Models\MarketPriceHistory::create([
                            'product_id' => $record->product_id,
                            'old_price' => $record->price,
                            'new_price' => $data['price'],
                            'currency' => $record->currency,
                            'change_date' => now(),
                            'notes' => $data['notes'] ?? 'Cambio de precio',
                            'changed_by' => auth()->guard('person')->id(),
                        ]);
                        
                        // Actualizar el precio actual
                        $record->update([
                            'price' => $data['price'],
                            'notes' => $data['notes'],
                            'updated_by' => auth()->guard('person')->id(),
                            'updated_at' => now(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Precio actualizado exitosamente')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('price_date', 'desc');
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
            'index' => Pages\ListMarketPrices::route('/'),
            'create' => Pages\CreateMarketPrice::route('/create'),
            'edit' => Pages\EditMarketPrice::route('/{record}/edit'),
        ];
    }
}
