<?php

namespace App\Filament\Resources\ProductListingResource\Pages;

use App\Filament\Resources\ProductListingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductListing extends CreateRecord
{
    protected static string $resource = ProductListingResource::class;
}
