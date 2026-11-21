<?php

namespace App\Filament\Resources\Pacientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PacientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('identificacion')
                    ->label('Cédula')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin cédula'),
                TextColumn::make('codigo_interno')
                    ->label('Código Interno')
                    ->searchable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
                TextColumn::make('nombres')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('apellidos')
                    ->label('Apellidos')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('edad')
                    ->label('Edad')
                    ->suffix(' años')
                    ->sortable(['fecha_nacimiento']),
                TextColumn::make('sexo')
                    ->label('Sexo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'M' => 'info',
                        'F' => 'danger',
                    }),
                TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parroquia.nombre')
                    ->label('Parroquia')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sexo')
                    ->options([
                        'M' => 'Masculino',
                        'F' => 'Femenino',
                    ]),
            ])
            ->recordActions([
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