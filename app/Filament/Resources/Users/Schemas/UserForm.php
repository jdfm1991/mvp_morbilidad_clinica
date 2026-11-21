<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos Personales')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Seguridad y Acceso')
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            // Solo requerir contraseña al crear (no al editar si no se desea cambiar)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(255)
                            // Añade el ojito para ver la contraseña
                            ->revealable(),
                            
                        Select::make('roles')
                            ->label('Rol del Sistema')
                            ->relationship('roles', 'name')
                            // Formatear el nombre del rol para que se vea bonito (Ej: Administrador)
                            ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name))
                            ->preload()
                            ->required(),
                            
                        Select::make('sede_id')
                            ->label('Sede Asignada')
                            ->relationship('sede', 'nombre')
                            ->placeholder('Administrador Global (Sin sede específica)')
                            ->nullable(),
                            
                        Toggle::make('activo')
                            ->label('Usuario Activo')
                            ->default(true)
                            ->helperText('Desactive esta opción para bloquear el acceso sin borrar el historial.'),
                    ])->columns(2),
            ]);
    }
}