<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketResource\Pages;
use App\Models\Market;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;

class MarketResource extends Resource
{
    protected static ?string $model = Market::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $navigationLabel = 'Mercados';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
            // Forms\Components\TextInput::make('location')
            //     ->label('Ubicación')->maxLength(255),
            // Forms\Components\TextInput::make('address')
            //     ->label('Dirección')->maxLength(255), 
                
            Forms\Components\Select::make('category')
                ->label('Categoría')->options([
                'wholesale' => 'Mayorista',
                'retail' => 'Minorista',
            ])->default('wholesale')->required(),
            Forms\Components\Select::make('state_id')
                ->label('Estado')
                ->options(State::query()->where('country_id', 296)->pluck('name', 'id'))
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
                    if (!$stateId) return [];
                    return Municipality::query()->where('state_id', $stateId)->pluck('name', 'id');
                })
                ->required()
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => $set('parish_id', null)),
            Forms\Components\Select::make('parish_id')
                ->label('Parroquia')
                ->options(function (callable $get) {
                    $municipalityId = $get('municipality_id');
                    if (!$municipalityId) return [];
                    return Parish::query()->where('municipality_id', $municipalityId)->pluck('name', 'id');
                })
                ->required(),
            Forms\Components\FileUpload::make('photo')->label('Foto')->image()->disk('public')->directory('markets')->nullable(),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('latitude')->label('Latitud')->numeric()->minValue(-90)->maxValue(90)->step(0.0000001),
                Forms\Components\TextInput::make('longitude')->label('Longitud')->numeric()->minValue(-180)->maxValue(180)->step(0.0000001),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
            ->label('Nombre')
            ->searchable()
            ->sortable(),
            // Tables\Columns\TextColumn::make('location')->label('Ubicación')->searchable(),
            Tables\Columns\TextColumn::make('category')
                ->label('Categoría')->colors([
                    'warning' => 'retail',
                    'success' => 'wholesale',
                ])
                ->formatStateUsing(fn($state) => $state === 'wholesale' ? 'Mayorista' : 'Minorista'),
                
            Tables\Columns\ImageColumn::make('photo')->label('Foto'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarkets::route('/'),
            'create' => Pages\CreateMarket::route('/create'),
            'edit' => Pages\EditMarket::route('/{record}/edit'),
        ];
    }
}
