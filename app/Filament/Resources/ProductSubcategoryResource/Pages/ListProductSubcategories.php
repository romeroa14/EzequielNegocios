<?php

namespace App\Filament\Resources\ProductSubcategoryResource\Pages;

use App\Filament\Resources\ProductSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductSubcategories extends ListRecords
{
    protected static string $resource = ProductSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 