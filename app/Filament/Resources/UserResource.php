<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\UniversalProductsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administración';

    protected static ?string $navigationLabel = 'Usuarios Admin';

    protected static ?string $modelLabel = 'Usuario Admin';

    protected static ?string $pluralModelLabel = 'Usuarios Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->required()
                            ->options([
                                User::ROLE_ADMIN => 'Administrador',
                                User::ROLE_PRODUCER => 'Productor',
                                User::ROLE_TECHNICIAN => 'Técnico',
                                User::ROLE_SUPPORT => 'Soporte',
                            ])
                            ->default(User::ROLE_PRODUCER)
                            ->reactive(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
                
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

                // Sección de Productos Universales (solo visible para el productor Tierra)
                // Forms\Components\Section::make('Productos Universales')
                //     ->schema([
                //         Forms\Components\Repeater::make('universalProducts')
                //             ->relationship()
                //             ->schema([
                //                 Forms\Components\TextInput::make('name')
                //                     ->label('Nombre del Producto')
                //                     ->required()
                //                     ->maxLength(255),
                //                 Forms\Components\Select::make('category_id')
                //                     ->label('Categoría')
                //                     ->relationship('category', 'name')
                //                     ->required(),
                //                 Forms\Components\Select::make('subcategory_id')
                //                     ->label('Subcategoría')
                //                     ->relationship('subcategory', 'name')
                //                     ->required(),
                //                 Forms\Components\TextInput::make('unit_type')
                //                     ->label('Unidad de Medida')
                //                     ->required(),
                //                 Forms\Components\FileUpload::make('image')
                //                     ->label('Imagen')
                //                     ->image()
                //                     ->directory('products'),
                //                 Forms\Components\Toggle::make('is_universal')
                //                     ->label('Producto Universal')
                //                     ->default(true)
                //                     ->disabled(),
                //             ])
                //             ->defaultItems(0)
                //             ->createItemButtonLabel('Agregar Producto Universal')
                //     ])
                //     ->visible(fn (Forms\Get $get): bool => 
                //         $get('role') === User::ROLE_PRODUCER && 
                //         $get('name') === User::TIERRA_PRODUCER_NAME
                //     ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'danger',
                        User::ROLE_PRODUCER => 'success',
                        User::ROLE_TECHNICIAN => 'warning',
                        User::ROLE_SUPPORT => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_PRODUCER => 'Productor',
                        User::ROLE_TECHNICIAN => 'Técnico',
                        User::ROLE_SUPPORT => 'Soporte',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        User::ROLE_ADMIN => 'Administrador',
                        User::ROLE_PRODUCER => 'Productor',
                        User::ROLE_TECHNICIAN => 'Técnico',
                        User::ROLE_SUPPORT => 'Soporte',
                    ]),
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
            UniversalProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
} 