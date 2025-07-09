<?php

namespace App\Filament\Resources\ProductLineResource\Pages;

use App\Filament\Resources\ProductLineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductLine extends CreateRecord
{
    protected static string $resource = ProductLineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 