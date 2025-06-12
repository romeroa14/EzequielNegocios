<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Models\Country;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Perfiles';
    protected static ?string $pluralNavigationLabel = 'Perfiles';
    protected static ?string $pluralModelLabel = 'Perfiles';
    protected static ?string $modelLabel = 'Perfil';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id())
                    ->required(),

                Forms\Components\Select::make('identification_type')
                    ->label('Tipo de Identificación')
                    ->options([
                        'cedula' => 'Cédula',
                        'rif' => 'RIF',
                        'passport' => 'Pasaporte',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('identification_number')
                    ->label('Número de Identificación')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('first_name')
                    ->label('Nombres')
                    ->required(),

                Forms\Components\TextInput::make('last_name')
                    ->label('Apellidos')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required(),

                Forms\Components\Select::make('role')
                    ->label('Rol')
                    ->options([
                        'buyer' => 'Comprador',
                        'seller' => 'Vendedor (Productor)',
                    ])
                    ->default('buyer')
                    ->required(),

                Forms\Components\Textarea::make('address')
                    ->label('Dirección')
                    ->required(),

                Forms\Components\Select::make('country_id')
                    ->label('País')
                    ->default(296)
                    ->options(fn () => [296 => 'Venezuela'])
                    ->disabled()
                    ->required()
                    ->live(),

                Forms\Components\Select::make('state_id')
                    ->label('Estado')
                    ->options(fn () => State::where('country_id', 296)->pluck('name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('municipality_id', null)),

                Forms\Components\Select::make('municipality_id')
                    ->label('Municipio')
                    ->options(function (Forms\Get $get) {
                        $stateId = $get('state_id');
                        if (!$stateId) {
                            return [];
                        }
                        return Municipality::where('state_id', $stateId)->pluck('name', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('parish_id', null)),

                Forms\Components\Select::make('parish_id')
                    ->label('Parroquia')
                    ->options(function (Forms\Get $get) {
                        $municipalityId = $get('municipality_id');
                        if (!$municipalityId) {
                            return [];
                        }
                        return Parish::where('municipality_id', $municipalityId)->pluck('name', 'id');
                    })
                    ->required(),

                Forms\Components\TextInput::make('sector')
                    ->label('Sector/Urbanización')
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->label('Dirección específica')
                    ->maxLength(255),

                

                Forms\Components\TextInput::make('company_name')
                    ->label('Nombre de la Empresa')
                    ->required()
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'company'),

                Forms\Components\TextInput::make('company_rif')
                    ->label('RIF de la Empresa')
                    ->required()
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'company'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('identification_number')
                    ->label('Número de ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'buyer' => 'Comprador',
                        'seller' => 'Vendedor',
                        'technician' => 'Técnico',
                        'support' => 'Soporte',
                        'admin' => 'Administrador',
                        'company' => 'Empresa',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'buyer' => 'info',
                        'seller' => 'success',
                        'technician' => 'warning',
                        'support' => 'danger',
                        'admin' => 'gray',
                        'company' => 'purple',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->visible(fn ($record): bool => $record?->role === 'company'),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'buyer' => 'Comprador',
                        'seller' => 'Vendedor',
                        'technician' => 'Técnico',
                        'support' => 'Soporte',
                        'admin' => 'Administrador',
                        'company' => 'Empresa',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersons::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
} 