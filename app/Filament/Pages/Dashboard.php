<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Panel Principal';
    protected static ?string $title = 'Panel de Administración';
    
    protected static string $view = 'filament.pages.dashboard';
    
    protected function getHeaderWidgets(): array
    {
        return [
            // Aquí irán los widgets del encabezado
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            // Aquí irán los widgets del pie de página
        ];
    }
} 