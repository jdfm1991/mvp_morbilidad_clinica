<?php

namespace App\Filament\Resources\Atencions\Schemas;

use App\Models\Paciente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AtencionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Paciente')
                    ->schema([
                        Select::make('paciente_id')
                            ->label('Paciente')
                            ->relationship('paciente', 'nombres')
                            ->getOptionLabelFromRecordUsing(function (Paciente $record) {
                                $documento = $record->identificacion ?? $record->codigo_interno;
                                return "{$record->apellidos}, {$record->nombres} ({$documento})";
                            })
                            ->searchable(['nombres', 'apellidos', 'identificacion', 'codigo_interno'])
                            ->required()
                            ->columnSpan(2),

                        Select::make('sede_id')
                            ->label('Sede')
                            ->relationship('sede', 'nombre')
                            ->default(1)
                            ->required(),
                    ])->columns(3),

                Section::make('Triaje / Signos Vitales')
                    ->description('Registrado por el personal de admisión.')
                    ->schema([
                        TextInput::make('presion_arterial_sistolica')
                            ->label('PA Sistólica (mmHg)')
                            ->numeric(),
                        TextInput::make('presion_arterial_diastolica')
                            ->label('PA Diastólica (mmHg)')
                            ->numeric(),
                        TextInput::make('temperatura')
                            ->label('Temperatura (°C)')
                            ->numeric()
                            ->step('0.1'),
                        TextInput::make('frecuencia_cardiaca')
                            ->label('Frec. Cardíaca (lpm)')
                            ->numeric(),
                        TextInput::make('frecuencia_respiratoria')
                            ->label('Frec. Respiratoria (rpm)')
                            ->numeric(),
                        TextInput::make('saturacion_oxigeno')
                            ->label('Sat. Oxígeno (%)')
                            ->numeric(),
                        TextInput::make('peso')
                            ->label('Peso (kg)')
                            ->numeric()
                            ->step('0.01'),
                        TextInput::make('talla')
                            ->label('Talla (cm)')
                            ->numeric()
                            ->step('0.01'),
                    ])->columns(4),

                Section::make('Detalles de la Admisión')
                    ->schema([
                        Textarea::make('motivo_consulta')
                            ->label('Motivo de Consulta')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('tipo_atencion_id')
                            ->label('Tipo de Atención')
                            ->relationship('tipoAtencion', 'nombre')
                            ->required(),
                        DateTimePicker::make('fecha_hora_ingreso')
                            ->label('Fecha y Hora de Ingreso')
                            ->default(now())
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
