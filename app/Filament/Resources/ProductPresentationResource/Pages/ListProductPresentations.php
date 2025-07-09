<?php

namespace App\Filament\Resources\ProductPresentationResource\Pages;

use App\Filament\Resources\ProductPresentationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductPresentations extends ListRecords
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 