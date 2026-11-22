<?php

namespace App\Filament\Resources\Pacientes\Schemas;

use App\Models\Municipio;
use App\Models\Parroquia;
use App\Services\PacienteService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PacienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de Identificación')
                    ->description('Registre la cédula o genere un código interno para el paciente.')
                    ->schema([
                        Toggle::make('sin_identificacion')
                            ->label('Paciente sin cédula (Bebé / Indocumentado)')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    // Solo generar si el campo código interno está vacío
                                    // (evita que se regenere al editar un paciente existente)
                                    if (empty($get('codigo_interno'))) {
                                        $set('codigo_interno', PacienteService::generarCodigoInterno('RN'));
                                    }
                                    $set('identificacion', null);
                                } else {
                                    $set('codigo_interno', null);
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('identificacion')
                            ->label('Cédula / Documento')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->hidden(fn(Get $get) => $get('sin_identificacion'))
                            ->required(fn(Get $get) => !$get('sin_identificacion')),

                        TextInput::make('codigo_interno')
                            ->label('Código Interno')
                            ->maxLength(20)
                            ->readOnly()
                            ->dehydrated()
                            ->hidden(fn(Get $get) => !$get('sin_identificacion')),
                    ])->columns(2),

                Section::make('Datos Personales')
                    ->schema([
                        TextInput::make('nombres')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('apellidos')
                            ->required()
                            ->maxLength(100),
                        DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->required()
                            ->maxDate(now()),
                        Select::make('sexo')
                            ->label('Sexo')
                            ->options([
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                            ])
                            ->required(),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Section::make('Ubicación y Contacto')
                    ->schema([
                        Select::make('estado_id')
                            ->label('Estado')
                            ->relationship('estado', 'nombre')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('municipio_id', null);
                                $set('parroquia_id', null);
                            }),
                        Select::make('municipio_id')
                            ->label('Municipio')
                            ->options(fn(Get $get) => Municipio::where('estado_id', $get('estado_id'))->pluck('nombre', 'id'))
                            ->searchable()
                            ->live()
                            ->disabled(fn(Get $get) => !$get('estado_id'))
                            ->afterStateUpdated(fn(Set $set) => $set('parroquia_id', null)),
                        Select::make('parroquia_id')
                            ->label('Parroquia')
                            ->options(fn(Get $get) => Parroquia::where('municipio_id', $get('municipio_id'))->pluck('nombre', 'id'))
                            ->searchable()
                            ->disabled(fn(Get $get) => !$get('municipio_id')),
                        Textarea::make('direccion')
                            ->label('Dirección Exacta')
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Ubicación de Registro')
                    ->schema([
                        Select::make('sede_id')
                            ->label('Sede de Registro')
                            ->relationship('sede', 'nombre')
                            ->default(1)
                            ->required()
                            ->disabled(fn() => !auth()->user()->isAdmin())
                            ->dehydrated(),
                    ])
            ]);
    }
}
