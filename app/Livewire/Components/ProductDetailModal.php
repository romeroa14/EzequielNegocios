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

    #[On('modal-ready')]
    public function openModalFromUrl($data = null)
    {
        try {
            // Si se pasa data con productId, cargar el producto
            if ($data && isset($data['productId'])) {
                $productId = $data['productId'];
                Log::info('Opening modal from URL with product ID:', ['product_id' => $productId]);
                
                // Cargar los datos del producto desde la base de datos
                $listing = ProductListing::with([
                    'product.productCategory',
                    'product.productSubcategory', 
                    'product.productLine',
                    'product.brand',
                    'productPresentation',
                    'person',
                    'state',
                    'municipality',
                    'parish'
                ])
                ->where('id', $productId)
                ->where('status', 'active')
                ->firstOrFail();

                // Preparar los datos para el modal
                $this->listing = [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'description' => $listing->description,
                    'unit_price' => $listing->unit_price,
                    'formatted_price' => $listing->formatted_price,
                    'presentation_quantity' => $listing->presentation_quantity ?? 0,
                    'product' => [
                        'id' => $listing->product->id,
                        'name' => $listing->product->name,
                        'presentation_name' => $listing->productPresentation->name ?? 'unidad',
                        'presentation_unit' => $listing->productPresentation->unit ?? 'unidades',
                        'category' => $listing->product->productCategory->name ?? 'Sin categoría',
                        'subcategory' => $listing->product->productSubcategory->name ?? 'Sin subcategoría',
                        'line' => $listing->product->productLine->name ?? 'Sin línea',
                        'brand' => $listing->product->brand->name ?? 'Sin marca',
                    ],
                    'person' => [
                        'id' => $listing->person->id,
                        'first_name' => $listing->person->first_name,
                        'last_name' => $listing->person->last_name,
                        'whatsapp' => $listing->person->whatsapp,
                        'phone' => $listing->person->phone,
                        'mobile' => $listing->person->mobile,
                    ],
                    'images' => $listing->images ?? [],
                    'location' => $listing->location,
                    'formatted_location' => $listing->formatted_location,
                    'formatted_date' => $listing->harvest_date ? $listing->harvest_date->format('d/m/Y') : null,
                    'status' => $listing->status,
                    'formatted_status' => ucfirst($listing->status),
                    'share_url' => route('catalogo.product', ['productId' => $listing->id])
                ];
                
                $this->selectedImageIndex = 0;
                Log::info('Product loaded for modal:', ['listing_id' => $this->listing['id']]);
                
                // Disparar evento para mostrar el modal después de cargar los datos
                $this->dispatch('modal-ready');
            }
            
        } catch (\Exception $e) {
            Log::error('Error al abrir modal desde URL:', [
                'error' => $e->getMessage(),
                'product_id' => $data['productId'] ?? null
            ]);
            
            $this->dispatch('error', 'Error al cargar el producto');
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

    public function generateShareLink($listingId)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($listingId);
            
            // Crear URL directa al producto
            $shareUrl = route('catalogo.product', ['productId' => $listingId]);
            
            // Mostrar modal simple con link y botón copiar
            $this->js("
                const modal = document.createElement('div');
                modal.innerHTML = `
                    <div style='position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;'>
                        <div style='background: white; padding: 20px; border-radius: 10px; max-width: 500px; width: 90%;'>
                            <h3 style='margin-bottom: 20px;'>Compartir Producto</h3>
                            <div style='margin-bottom: 15px;'>
                                <label style='display: block; font-size: 14px; color: #374151; margin-bottom: 8px; font-weight: 500;'>Link del producto:</label>
                                <div style='display: flex; gap: 8px;'>
                                    <input type='text' id='shareLink' value='" . $shareUrl . "' readonly style='flex: 1; padding: 8px; border: 1px solid #d1d5db; border-radius: 5px; font-size: 14px; background: #f9fafb;' onclick='this.select()'>
                                    <button onclick='copyToClipboard()' style='padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;'>Copiar</button>
                                </div>
                            </div>
                            <button onclick='this.parentElement.parentElement.remove()' style='padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer;'>Cerrar</button>
                        </div>
                    </div>
                `;
                
                // Función para copiar al portapapeles
                window.copyToClipboard = function() {
                    const input = document.getElementById('shareLink');
                    input.select();
                    input.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    
                    // Mostrar mensaje de confirmación
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = '¡Copiado!';
                    button.style.background = '#059669';
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.style.background = '#10b981';
                    }, 2000);
                };
                
                document.body.appendChild(modal);
            ");
            
        } catch (\Exception $e) {
            Log::error('Error al generar link de compartir', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo generar el link de compartir.');
        }
    }

    public function render()
    {
        return view('livewire.components.product-detail-modal');
    }
}
