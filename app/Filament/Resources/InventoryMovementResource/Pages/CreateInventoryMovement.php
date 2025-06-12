<?php

namespace App\Filament\Resources\InventoryMovementResource\Pages;

use App\Filament\Resources\InventoryMovementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateInventoryMovement extends CreateRecord
{
    protected static string $resource = InventoryMovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = \App\Models\Product::find($data['product_id']);
        $currentStock = \App\Models\InventoryMovement::where('product_id', $data['product_id'])
            ->latest()
            ->value('current_stock') ?? 0;

        $user = Auth::user();
        $person = $user->person;

        if (!$person) {
            throw new \Exception('Debe crear su perfil antes de realizar movimientos de inventario.');
        }

        $data['person_id'] = $person->id;
        $data['previous_stock'] = $currentStock;

        switch ($data['type']) {
            case 'entrada':
                $data['current_stock'] = $currentStock + $data['quantity'];
                break;
            case 'salida':
                $data['current_stock'] = $currentStock - $data['quantity'];
                break;
            case 'ajuste':
                $data['current_stock'] = $data['quantity'];
                break;
            case 'merma':
                $data['current_stock'] = $currentStock - $data['quantity'];
                break;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 