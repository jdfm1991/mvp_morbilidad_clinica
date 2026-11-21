<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i'),
                TextColumn::make('description')->label('Acción'),
                TextColumn::make('subject_type')->label('Modelo'),
                TextColumn::make('causer.name')->label('Usuario'),
                TextColumn::make('properties')->label('Cambios')->limit(50),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                //EditAction::make(),
            ])
            ->toolbarActions([
                /* BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]), */
            ]);
    }
}
