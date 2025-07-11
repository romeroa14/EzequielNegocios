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
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

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
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Select::make('role')
                            ->label('Rol')
                            ->options([
                                'admin' => 'Administrador',
                                'producer' => 'Productor',
                                'technician' => 'Técnico',
                                'support' => 'Soporte',
                            ])
                            ->required()
                            ->live(),
                        Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                        Toggle::make('is_universal')
                            ->label('Productor Universal')
                            ->helperText('Indica si este productor puede crear productos universales')
                            ->hidden(fn (Forms\Get $get): bool => $get('role') !== 'producer')
                            ->default(false)
                            ->dehydrated(fn (Forms\Get $get): bool => $get('role') === 'producer'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'producer' => 'success',
                        'technician' => 'warning',
                        'support' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'producer' => 'Productor',
                        'technician' => 'Técnico',
                        'support' => 'Soporte',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_universal')
                    ->label('Universal')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'producer' => 'Productor',
                        'technician' => 'Técnico',
                        'support' => 'Soporte',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
                Tables\Filters\TernaryFilter::make('is_universal')
                    ->label('Productor Universal')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_universal', true)->where('role', 'producer'),
                        false: fn (Builder $query) => $query->where('is_universal', false)->where('role', 'producer'),
                        blank: fn (Builder $query) => $query
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_universal')
                    ->label(fn (User $record): string => $record->is_universal ? 'Quitar Universal' : 'Hacer Universal')
                    ->icon(fn (User $record): string => $record->is_universal ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (User $record): string => $record->is_universal ? 'danger' : 'success')
                    ->visible(fn (User $record): bool => $record->role === 'producer')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update(['is_universal' => !$record->is_universal]);
                    }),
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