<?php

namespace App\Filament\Resources\ProductPresentationResource\Pages;

use App\Filament\Resources\ProductPresentationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductPresentation extends EditRecord
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 