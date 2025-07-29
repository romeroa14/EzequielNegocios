<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class ProductDetailModal extends Component
{
    public $listing = null;

    #[On('showProductDetail')] 
    public function showModal($listingId)
    {
        try {
            $listing = ProductListing::with([
                'product.productCategory',
                'product.productSubcategory',
                'product.productLine',
                'product.brand',
                'product.productPresentation',
                'person',
                'state',
                'municipality',
                'parish'
            ])
            ->where('id', $listingId)
            ->where('status', 'active')
            ->firstOrFail();

            // Asegurarse de que las imágenes estén disponibles
            if (empty($listing->images)) {
                $listing->images = [];
            }

            // Preparar los datos para el modal
            $this->listing = [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'unit_price' => $listing->unit_price,
                'formatted_price' => number_format($listing->unit_price, 2),
                'quantity_available' => $listing->quantity_available,
                'quality_grade' => $listing->quality_grade,
                'status' => $listing->status,
                'formatted_status' => $listing->status === 'active' ? 'Activo' : 'No disponible',
                'formatted_date' => $listing->harvest_date ? $listing->harvest_date->format('d/m/Y') : null,
                'formatted_location' => $listing->location,
                
                // Información del producto
                'product' => [
                    'name' => $listing->product->name,
                    'category_name' => $listing->product->productCategory?->name ?? 'N/A',
                    'subcategory_name' => $listing->product->productSubcategory?->name ?? 'N/A',
                    'brand_name' => $listing->product->brand?->name ?? 'N/A',
                    'line_name' => $listing->product->productLine?->name ?? 'N/A',
                    'presentation_name' => $listing->product->productPresentation?->name ?? 'N/A'
                ],

                // Información del vendedor
                'seller' => [
                    'name' => $listing->person->first_name . ' ' . $listing->person->last_name,
                    'location' => $listing->location
                ],

                // Imágenes
                'images' => collect($listing->images)->map(function($image) {
                    return asset('storage/' . $image);
                })->values()->all()
            ];

            // Asegurarse de que haya al menos una imagen
            if (empty($this->listing['images'])) {
                $this->listing['images'] = [asset('images/placeholder.png')];
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
