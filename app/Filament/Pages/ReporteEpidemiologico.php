<?php

namespace App\Filament\Pages;

use App\Models\Atencion;
use App\Models\Cie10;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use App\Exports\ReporteMorbilidadExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use UnitEnum;

class ReporteEpidemiologico extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.reporte-epidemiologico';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Epidemiología';
    protected static ?string $title = 'Reporte de Morbilidad';
    protected static ?string $navigationLabel = 'Reporte de Morbilidad';



    public ?array $data = [];

    // Variables para pasar a la vista
    public $resultados = [];
    public $totales = [];
    public $fechaInicio;
    public $fechaFin;

    public function mount(): void
    {
        // Valores por defecto: mes actual
        $this->form->fill([
            'fecha_inicio' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin' => now()->endOfMonth()->format('Y-m-d'),
        ]);

        $this->generarReporte();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->live(),
                DatePicker::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->required()
                    ->live(),
                Select::make('capitulo')
                    ->label('Filtrar por Capítulo CIE-10')
                    ->options(fn() => Cie10::whereNotNull('capitulo')->distinct()->pluck('capitulo', 'capitulo'))
                    ->placeholder('Todos los capítulos')
                    ->live(),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function generarReporte(): void
    {
        $data = $this->form->getState();

        if (empty($data['fecha_inicio']) || empty($data['fecha_fin'])) {
            return;
        }

        $this->fechaInicio = $data['fecha_inicio'];
        $this->fechaFin = $data['fecha_fin'];

        $query = Atencion::query()
            ->join('cie10', 'atenciones.cie10_principal_id', '=', 'cie10.id')
            ->join('pacientes', 'atenciones.paciente_id', '=', 'pacientes.id')
            ->where('atenciones.estado', 'atendido')
            ->whereBetween(DB::raw('DATE(atenciones.fecha_hora_ingreso)'), [$this->fechaInicio, $this->fechaFin]);

        // Filtro por capítulo si se seleccionó
        if (!empty($data['capitulo'])) {
            $query->where('cie10.capitulo', $data['capitulo']);
        }

        // Agrupación por Capítulo y Diagnóstico Específico
        $resultados = $query->select(
            'cie10.capitulo',
            'cie10.codigo as cie10_codigo',
            'cie10.descripcion as cie10_descripcion',
            DB::raw('COUNT(atenciones.id) as total_casos'),
            DB::raw("SUM(CASE WHEN pacientes.sexo = 'M' THEN 1 ELSE 0 END) as total_masculino"),
            DB::raw("SUM(CASE WHEN pacientes.sexo = 'F' THEN 1 ELSE 0 END) as total_femenino"),
            DB::raw("COUNT(DISTINCT atenciones.paciente_id) as pacientes_unicos")
        )
            ->groupBy('cie10.capitulo', 'cie10.codigo', 'cie10.descripcion')
            ->orderBy('cie10.capitulo')
            ->orderByDesc('total_casos')
            ->get()
            ->groupBy('capitulo'); // Agrupamos la colección por capítulo para la vista

        //$this->resultados = $resultados;
        $this->resultados = $resultados->toArray();

        // Totales generales para el encabezado
        $this->totales = [
            'total_casos' => $resultados->flatten()->sum('total_casos'),
            'total_masculino' => $resultados->flatten()->sum('total_masculino'),
            'total_femenino' => $resultados->flatten()->sum('total_femenino'),
            'pacientes_unicos' => $resultados->flatten()->sum('pacientes_unicos'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportar_excel')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Validar que haya datos
                    if (empty($this->resultados) || $this->totales['total_casos'] === 0) {
                        Notification::make()
                            ->title('No hay datos para exportar')
                            ->warning()
                            ->send();
                        return;
                    }

                    return Excel::download(
                        new ReporteMorbilidadExport($this->resultados, $this->fechaInicio, $this->fechaFin, $this->totales),
                        'Reporte_Morbilidad_' . $this->fechaInicio . '_' . $this->fechaFin . '.xlsx'
                    );
                }),
        ];
    }

    // Este método se ejecuta automáticamente cuando cambian los filtros (por el ->live())
    public function updatedData(): void
    {
        $this->generarReporte();
    }
}
