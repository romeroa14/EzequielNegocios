<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ProductDetailModal extends Component
{
    public $listing = null;
    public $selectedImageIndex = 0;

    #[On('showProductDetail')] 
    public function showModal($data)
    {
        try {
            // Log para debug
            Log::info('Modal data received:', ['data' => $data]);

            $this->listing = $data;
            $this->selectedImageIndex = 0;
            
            // Asegurarse de que los datos necesarios estén presentes
            if (!isset($this->listing['presentation_quantity'])) {
                $this->listing['presentation_quantity'] = 0;
            }
            
            if (!isset($this->listing['product']['presentation_name'])) {
                $this->listing['product']['presentation_name'] = 'unidad';
            }
            
            if (!isset($this->listing['product']['presentation_unit'])) {
                $this->listing['product']['presentation_unit'] = 'unidades';
            }

            $this->dispatch('modal-ready');

        } catch (\Exception $e) {
            Log::error('Error al cargar detalle del producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('error', 'Error al cargar el detalle del producto');
        }
    }

    public function closeModal()
    {
        $this->listing = null;
        $this->selectedImageIndex = 0;
        $this->dispatch('modal-closed');
    }

    public function contactSeller($listingId)
    {
        // Redirigir a la página del productor
        return redirect()->route('productor.show', ['listing' => $listingId]);
    }

    public function render()
    {
        return view('livewire.components.product-detail-modal');
    }
}
