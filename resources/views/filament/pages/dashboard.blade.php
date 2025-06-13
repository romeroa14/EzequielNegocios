<x-filament-panels::page>
    <x-filament::grid>
        <x-filament::grid.column span="8">
            {{-- Contenido principal --}}
            <x-filament::card>
                <div class="h-12 flex items-center">
                    <h2 class="text-lg font-medium">Bienvenido al Panel de Administración</h2>
                </div>
            </x-filament::card>
        </x-filament::grid.column>

        <x-filament::grid.column span="4">
            {{-- Widgets laterales --}}
            <x-filament::card>
                <div class="h-12 flex items-center">
                    <h2 class="text-lg font-medium">Resumen</h2>
                </div>
            </x-filament::card>
        </x-filament::grid.column>
    </x-filament::grid>

    @if ($this->hasHeaderWidgets)
        <x-filament::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getHeaderWidgetsColumns()"
            :data="$this->getWidgetData()"
        />
    @endif

    @if ($this->hasFooterWidgets)
        <x-filament::widgets
            :widgets="$this->getFooterWidgets()"
            :columns="$this->getFooterWidgetsColumns()"
            :data="$this->getWidgetData()"
        />
    @endif
</x-filament-panels::page> 