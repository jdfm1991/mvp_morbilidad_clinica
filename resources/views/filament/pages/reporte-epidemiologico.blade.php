<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Formulario de Filtros --}}
        <x-filament::section>
            <form wire:submit="generarReporte">
                {{ $this->form }}

                <div class="mt-4 flex gap-2">
                    <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                        Generar Reporte
                    </x-filament::button>

                </div>
            </form>
        </x-filament::section>

        {{-- Tarjetas de Totales --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-500">Total Casos</div>
                <div class="text-2xl font-bold">{{ $totales['total_casos'] ?? 0 }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm text-gray-500">Hombres</div>
                <div class="text-2xl font-bold text-blue-600">{{ $totales['total_masculino'] ?? 0 }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm text-gray-500">Mujeres</div>
                <div class="text-2xl font-bold text-pink-600">{{ $totales['total_femenino'] ?? 0 }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm text-gray-500">Pacientes Únicos</div>
                <div class="text-2xl font-bold text-green-600">{{ $totales['pacientes_unicos'] ?? 0 }}</div>
            </x-filament::card>
        </div>

        {{-- Tabla de Resultados --}}
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3">Capítulo / Diagnóstico</th>
                            <th class="px-4 py-3 text-center">Total</th>
                            <th class="px-4 py-3 text-center">M</th>
                            <th class="px-4 py-3 text-center">F</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $capitulo => $diagnosticos)
                            {{-- Fila del Capítulo --}}
                            <tr class="bg-gray-100 font-bold text-gray-900 border-b">
                                <td class="px-4 py-2" colspan="4">
                                    {{ $capitulo ?? 'Sin Capítulo Asignado' }}
                                </td>
                            </tr>
                            {{-- Filas de Diagnósticos --}}
                            @foreach ($diagnosticos as $diag)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 pl-8">
                                        <span class="font-mono text-xs text-gray-500">{{ $diag['cie10_codigo'] }}</span>
                                        {{ $diag['cie10_descripcion'] }}
                                    </td>
                                    <td class="px-4 py-2 text-center font-semibold">{{ $diag['total_casos'] }}</td>
                                    <td class="px-4 py-2 text-center">{{ $diag['total_masculino'] }}</td>
                                    <td class="px-4 py-2 text-center">{{ $diag['total_femenino'] }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No se encontraron atenciones con los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
