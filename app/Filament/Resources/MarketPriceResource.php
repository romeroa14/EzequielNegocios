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
                    ->prefix(function ($get) {
                        return $get('currency') === 'USD' ? '$' : 'Bs.';
                    })
                    ->label('Precio')
                    ->columnSpan(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $currency = $get('currency');
                        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                        
                        if ($usdRate && $state > 0) {
                            if ($currency === 'VES') {
                                $conversion = round($state / $usdRate->rate, 2);
                                $set('conversion_display', "Equivalente: $ " . number_format($conversion, 2, ',', '.'));
                            } elseif ($currency === 'USD') {
                                $conversion = round($state * $usdRate->rate, 2);
                                $set('conversion_display', "Equivalente: Bs. " . number_format($conversion, 2, ',', '.'));
                            }
                        } else {
                            $set('conversion_display', '');
                        }
                    }),

                Forms\Components\Select::make('currency')
                    ->options([
                        'VES' => 'Bolívares (VES)',
                        'USD' => 'Dólares (USD)',
                    ])
                    ->default('VES')
                    ->required()
                    ->label('Moneda')
                    ->columnSpan(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                        if ($usdRate) {
                            $set('exchange_rate_info', "Tasa actual: $1 = Bs. " . number_format($usdRate->rate, 2, ',', '.'));
                        }
                        
                        // Limpiar conversión cuando cambia la moneda
                        $set('conversion_display', '');
                        $set('price', '');
                        
                        // Recalcular conversión si ya hay un precio
                        $price = $get('price');
                        if ($price > 0 && $usdRate) {
                            if ($state === 'VES') {
                                $conversion = round($price / $usdRate->rate, 2);
                                $set('conversion_display', "Equivalente: $ " . number_format($conversion, 2, ',', '.'));
                            } elseif ($state === 'USD') {
                                $conversion = round($price * $usdRate->rate, 2);
                                $set('conversion_display', "Equivalente: Bs. " . number_format($conversion, 2, ',', '.'));
                            }
                        }
                    }),

                Forms\Components\Placeholder::make('conversion_display')
                    ->label('Conversión en Tiempo Real')
                    ->content(function ($get) {
                        $currency = $get('currency');
                        $price = $get('price');
                        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                        
                        if ($usdRate && $price > 0) {
                            if ($currency === 'VES') {
                                $conversion = round($price / $usdRate->rate, 2);
                                return "💱 $ " . number_format($conversion, 2, ',', '.') . " USD";
                            } elseif ($currency === 'USD') {
                                $conversion = round($price * $usdRate->rate, 2);
                                return "💱 Bs. " . number_format($conversion, 2, ',', '.') . " VES";
                            }
                        }
                        return "💱 Ingresa un precio para ver la conversión";
                    })
                    ->columnSpan(1),

                Forms\Components\Placeholder::make('exchange_rate_info')
                    ->label('Información de Tasa')
                    ->content(function () {
                        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                        if ($usdRate) {
                            return "💱 Tasa de cambio actual: $1 = Bs. " . number_format($usdRate->rate, 2, ',', '.') . 
                                   " (Actualizada: " . $usdRate->fetched_at->format('d/m/Y H:i') . ")";
                        }
                        return "⚠️ No hay tasa de cambio disponible";
                    })
                    ->columnSpanFull(),

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
                    ->money(fn (MarketPrice $record): string => $record->currency)
                    ->sortable()
                    ->description('Precio en la moneda original'),

                Tables\Columns\TextColumn::make('conversion_display')
                    ->label('Precio Convertido')
                    ->html()
                    ->description('Equivalente en la otra moneda')
                    ->getStateUsing(function (MarketPrice $record): string {
                        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                        
                        if ($record->currency === 'VES' && $usdRate) {
                            $priceUsd = round($record->price / $usdRate->rate, 2);
                            return "<div class='text-blue-600 font-medium'>$ " . number_format($priceUsd, 2, ',', '.') . "</div>
                                    <div class='text-xs text-gray-500'>Equivalente USD</div>";
                        } elseif ($record->currency === 'USD' && $usdRate) {
                            $priceVes = round($record->price * $usdRate->rate, 2);
                            return "<div class='text-green-600 font-medium'>Bs. " . number_format($priceVes, 2, ',', '.') . "</div>
                                    <div class='text-xs text-gray-500'>Equivalente VES</div>";
                        }
                        
                        return "<span class='text-gray-400'>Sin conversión</span>";
                    })
                    ->sortable(false),

                // Tables\Columns\TextColumn::make('currency')
                //     ->label('Moneda')
                //     ->badge()
                //     ->description('Moneda del precio original')
                //     ->color(fn (string $state): string => match ($state) {
                //         'VES' => 'success',
                //         'USD' => 'warning',
                //         default => 'gray',
                //     }),

                Tables\Columns\TextColumn::make('price_date')
                    ->label('Última Actualización')
                    ->date()
                    ->sortable(),

                // Tables\Columns\TextColumn::make('updatedBy.full_name')
                //     ->label('Actualizado por')
                //     ->sortable(),

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

                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'VES' => 'Bolívares (VES)',
                        'USD' => 'Dólares (USD)',
                    ])
                    ->label('Moneda')
                    ->multiple(),

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
                            ->prefix(fn (MarketPrice $record) => $record->currency === 'VES' ? 'Bs.' : '$')
                            ->rules(['min:0'])
                            ->default(fn (MarketPrice $record) => $record->price)
                            ->afterStateUpdated(function ($state, $set, MarketPrice $record) {
                                $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                                if ($usdRate && $state > 0) {
                                    if ($record->currency === 'VES') {
                                        $conversion = round($state / $usdRate->rate, 2);
                                        $set('conversion_info', "Equivalente: $ " . number_format($conversion, 2, ',', '.'));
                                    } elseif ($record->currency === 'USD') {
                                        $conversion = round($state * $usdRate->rate, 2);
                                        $set('conversion_info', "Equivalente: Bs. " . number_format($conversion, 2, ',', '.'));
                                    }
                                }
                            }),
                        
                        Forms\Components\Placeholder::make('conversion_info')
                            ->label('Conversión')
                            ->content(function (MarketPrice $record) {
                                $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
                                if ($usdRate) {
                                    if ($record->currency === 'VES') {
                                        $conversion = round($record->price / $usdRate->rate, 2);
                                        return "💱 $ " . number_format($conversion, 2, ',', '.') . " USD";
                                    } elseif ($record->currency === 'USD') {
                                        $conversion = round($record->price * $usdRate->rate, 2);
                                        return "💱 Bs. " . number_format($conversion, 2, ',', '.') . " VES";
                                    }
                                }
                                return "⚠️ No hay tasa disponible";
                            }),
                        
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

    public static function getNavigationBadge(): ?string
    {
        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD');
        if ($usdRate) {
            return '$1 = Bs.' . number_format($usdRate->rate, 0);
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
