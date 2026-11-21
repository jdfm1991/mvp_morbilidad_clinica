<?php

namespace App\Filament\Resources\Atencions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\Cie10;
use App\Models\CriterioResolutivo;

class AtencionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_hora_ingreso', 'desc')
            ->columns([
                TextColumn::make('fecha_hora_ingreso')
                    ->label('Fecha/Hora Ingreso')
                    ->dateTime('d/m/Y H:i A')
                    ->sortable(),
                TextColumn::make('paciente.apellidos')
                    ->label('Paciente')
                    ->formatStateUsing(fn($record) => "{$record->paciente->apellidos}, {$record->paciente->nombres}")
                    ->searchable(['apellidos', 'nombres']),
                TextColumn::make('paciente.identificacion')
                    ->label('Cédula/Código')
                    ->formatStateUsing(fn($record) => $record->paciente->identificacion ?? $record->paciente->codigo_interno)
                    ->searchable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'registrado' => 'gray',
                        'en_espera' => 'warning',
                        'en_atencion' => 'info',
                        'atendido' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('motivo_consulta')
                    ->label('Motivo')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('medico.name')
                    ->label('Médico')
                    ->placeholder('No asignado')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'registrado' => 'Registrado',
                        'en_espera' => 'En Espera',
                        'en_atencion' => 'En Atención',
                        'atendido' => 'Atendido',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('en_espera'), // Por defecto muestra la lista de espera
            ])
            ->recordActions([
                // Nueva acción para la atención médica
                Action::make('atender')
                    ->label('Atender')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn($record) => $record->estado === 'en_espera') // Solo aparece si está en espera
                    ->form([
                        Select::make('cie10_principal_id')
                            ->label('Diagnóstico Principal (CIE-10)')
                            ->searchable()
                            ->getSearchResultsUsing(fn(string $search) => Cie10::where('codigo', 'ilike', "%{$search}%")
                                ->orWhere('descripcion', 'ilike', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn($cie) => [$cie->id => "{$cie->codigo} - {$cie->descripcion}"]))
                            ->getOptionLabelUsing(fn($value) => Cie10::find($value)?->codigo . ' - ' . Cie10::find($value)?->descripcion)
                            ->required(),
                        Select::make('diagnosticos_secundarios')
                            ->label('Diagnósticos Secundarios / Comorbilidades')
                            ->multiple()
                            ->options(Cie10::where('activo', true)->pluck('descripcion', 'id'))
                            ->searchable()
                            ->preload(),
                        Select::make('clasificacion_caso')
                            ->label('Clasificación Epidemiológica')
                            ->options([
                                'P' => 'P - Primera vez / Incidencia',
                                'S' => 'S - Sucesiva / Prevalencia',
                                'X' => 'X - Asociada',
                            ])
                            ->required(),
                        Select::make('criterio_resolutivo_id')
                            ->label('Criterio Resolutivo')
                            ->options(CriterioResolutivo::where('activo', true)->pluck('nombre', 'id'))
                            ->required(),
                        Textarea::make('observaciones')
                            ->label('Observaciones / Tratamiento')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, $record): void {
                        // 1. Actualizar la atención
                        $record->update([
                            'cie10_principal_id' => $data['cie10_principal_id'],
                            'clasificacion_caso' => $data['clasificacion_caso'],
                            'criterio_resolutivo_id' => $data['criterio_resolutivo_id'],
                            'observaciones' => $data['observaciones'],
                            'estado' => 'atendido',
                            'user_medico_id' => auth()->id(),
                            'fecha_hora_atencion' => now(),
                            'fecha_hora_egreso' => now(),
                        ]);

                        // 2. Guardar diagnósticos secundarios
                        if (!empty($data['diagnosticos_secundarios'])) {
                            // Validar que el secundario no sea igual al principal
                            $secundarios = array_diff($data['diagnosticos_secundarios'], [$data['cie10_principal_id']]);

                            foreach ($secundarios as $cie10_id) {
                                $record->diagnosticosSecundarios()->create([
                                    'cie10_id' => $cie10_id,
                                ]);
                            }
                        }

                        Notification::make()
                            ->title('Atención finalizada correctamente')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
