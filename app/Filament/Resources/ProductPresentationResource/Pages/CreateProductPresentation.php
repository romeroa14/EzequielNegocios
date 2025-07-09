<?php

namespace App\Filament\Resources\ProductPresentationResource\Pages;

use App\Filament\Resources\ProductPresentationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductPresentation extends CreateRecord
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 