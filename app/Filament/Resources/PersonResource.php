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
use Illuminate\Support\Facades\Hash;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Usuarios';

    protected static ?string $navigationLabel = 'Compradores/Vendedores';
    protected static ?string $pluralNavigationLabel = 'Compradores/Vendedores';
    protected static ?string $pluralModelLabel = 'Compradores/Vendedores';
    protected static ?string $modelLabel = 'Comprador/Vendedor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Identificación')
                    ->schema([
                        Forms\Components\Select::make('identification_type')
                            ->label('Tipo de Identificación')
                            ->options([
                                'V' => 'V',
                                'E' => 'E',
                                'J' => 'J',
                                'G' => 'G',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('identification_number')
                            ->label('Número de Identificación')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sector')
                            ->label('Sector')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Información de Cuenta')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                'buyer' => 'Comprador',
                                'seller' => 'Vendedor',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verificado'),
                    ])->columns(3),

                Forms\Components\Section::make('Información de Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nombre de la Empresa')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('role') === 'seller'),
                        Forms\Components\TextInput::make('company_rif')
                            ->label('RIF de la Empresa')
                            ->maxLength(20)
                            ->visible(fn (Forms\Get $get): bool => $get('role') === 'seller'),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get): bool => $get('role') === 'seller'),

                Forms\Components\Section::make('Seguridad')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->same('password'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('identification_type')
                    ->label('Tipo Doc.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('identification_number')
                    ->label('Número Doc.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'buyer' => 'info',
                        'seller' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'buyer' => 'Comprador',
                        'seller' => 'Vendedor',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verificado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'buyer' => 'Comprador',
                        'seller' => 'Vendedor',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verificado'),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
} 