<?php

namespace App\Filament\Widgets;

use App\Models\Atencion;
use App\Models\Paciente;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadisticasGenerales extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Estadísticas del mes actual
        $atencionesMes = Atencion::whereMonth('fecha_hora_ingreso', now()->month)
            ->whereYear('fecha_hora_ingreso', now()->year)
            ->where('estado', 'atendido')
            ->count();

        // Pacientes únicos atendidos este mes
        $pacientesUnicosMes = Atencion::whereMonth('fecha_hora_ingreso', now()->month)
            ->whereYear('fecha_hora_ingreso', now()->year)
            ->where('estado', 'atendido')
            ->distinct('paciente_id')
            ->count('paciente_id');

        // Casos nuevos (P) este mes
        $casosNuevosMes = Atencion::whereMonth('fecha_hora_ingreso', now()->month)
            ->whereYear('fecha_hora_ingreso', now()->year)
            ->where('estado', 'atendido')
            ->where('clasificacion_caso', 'P')
            ->count();

        // Total de pacientes en el sistema
        $totalPacientes = Paciente::where('activo', true)->count();

        return [
            Stat::make('Atenciones este mes', $atencionesMes)
                ->description('Pacientes atendidos en ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),
            Stat::make('Pacientes únicos (Mes)', $pacientesUnicosMes)
                ->description('Pacientes distintos atendidos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('Casos Nuevos (P) (Mes)', $casosNuevosMes)
                ->description('Incidencia registrada')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            Stat::make('Total Pacientes', $totalPacientes)
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}