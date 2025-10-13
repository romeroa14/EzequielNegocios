<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductListing;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ProductLine;
use App\Models\Brand;
use App\Models\ProductPresentation;
use App\Models\Market;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $selectedSubcategory = '';
    public $selectedQuality = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showFilters = false;
    public $producer = null;
    public $selectedLine = '';
    public $selectedBrand = '';
    public $selectedPresentation = '';
    public $selectedMarket = '';
    public $perPage = 12;
    public $currentPage = 1;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedSubcategory' => ['except' => ''],
        'selectedLine' => ['except' => ''],
        'selectedBrand' => ['except' => ''],
        'selectedPresentation' => ['except' => ''],
        'selectedMarket' => ['except' => ''],
        'selectedQuality' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'producer' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'selectedCategory' => 'nullable|exists:product_categories,id',
            'selectedSubcategory' => 'nullable|exists:product_subcategories,id',
            'selectedQuality' => 'nullable|in:premium,standard,economic',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0|gte:minPrice',
            'sortBy' => 'required|in:created_at,unit_price,title,harvest_date',
            'sortDirection' => 'required|in:asc,desc',
            'producer' => 'nullable|exists:people,id',
            'selectedMarket' => 'nullable|exists:markets,id',
        ];
    }

    protected function messages()
    {
        return [
            'search.max' => 'La búsqueda no puede exceder los 255 caracteres.',
            'selectedCategory.exists' => 'La categoría seleccionada no existe.',
            'selectedSubcategory.exists' => 'La subcategoría seleccionada no existe.',
            'selectedQuality.in' => 'La calidad seleccionada no es válida.',
            'minPrice.numeric' => 'El precio mínimo debe ser un número.',
            'minPrice.min' => 'El precio mínimo no puede ser negativo.',
            'maxPrice.numeric' => 'El precio máximo debe ser un número.',
            'maxPrice.min' => 'El precio máximo no puede ser negativo.',
            'maxPrice.gte' => 'El precio máximo debe ser mayor o igual al precio mínimo.',
            'sortBy.required' => 'El campo de ordenamiento es obligatorio.',
            'sortBy.in' => 'El campo de ordenamiento no es válido.',
            'sortDirection.required' => 'La dirección de ordenamiento es obligatoria.',
            'sortDirection.in' => 'La dirección de ordenamiento no es válida.',
            'producer.exists' => 'El productor seleccionado no existe.',
        ];
    }

    public function mount($productId = null)
    {
        $this->producer = request()->query('producer');
        $this->validate();
        
        // Verificar si hay un producto específico para mostrar (desde parámetro o query)
        $urlProductId = $productId ?? request()->query('product');
        if ($urlProductId) {
            Log::info('Product ID detected in mount:', ['product_id' => $urlProductId, 'source' => $productId ? 'parameter' : 'query']);
            // Obtener los datos completos del producto y abrir el modal
            $this->showProductDetail($urlProductId);
        }
        
        // Debug: Log current filter values
        Log::info('ProductCatalog mounted with filters:', [
            'search' => $this->search,
            'selectedCategory' => $this->selectedCategory,
            'selectedSubcategory' => $this->selectedSubcategory,
            'selectedLine' => $this->selectedLine,
            'selectedBrand' => $this->selectedBrand,
            'selectedPresentation' => $this->selectedPresentation,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sortBy' => $this->sortBy,
            'product_id' => $productId
        ]);
    }

    public function getExchangeRatesProperty()
    {
        return [
            'usd' => ProductListing::getUsdRate(),
            'eur' => ProductListing::getEurRate()
        ];
    }

    public function getBcvRatesProperty()
    {
        $usdRate = \App\Models\ExchangeRate::getLatestRate('USD')?->rate ?? 0;
        $eurRate = \App\Models\ExchangeRate::getLatestRate('EUR')?->rate ?? 0;
        
        Log::info('BCV Rates:', [
            'usd' => $usdRate,
            'eur' => $eurRate
        ]);
        
        return [
            'usd' => $usdRate,
            'eur' => $eurRate
        ];
    }

    public function updated($propertyName)
    {
        // Solo loggear si la propiedad existe
        if (property_exists($this, $propertyName)) {
            Log::info('Property updated:', [
                'property' => $propertyName,
                'value' => $this->$propertyName,
                'all_properties' => [
                    'search' => $this->search,
                    'selectedCategory' => $this->selectedCategory,
                    'selectedBrand' => $this->selectedBrand,
                    'selectedPresentation' => $this->selectedPresentation,
                ]
            ]);
        }
        
        $this->validateOnly($propertyName);
        
        if ($propertyName === 'search') {
            $this->resetPage();
        }

        if ($propertyName === 'selectedCategory') {
            $this->selectedSubcategory = '';
            $this->selectedLine = null;
            $this->selectedBrand = null;
            $this->resetPage();
        }

        if ($propertyName === 'selectedMarket') {
            $this->resetPage();
        }


        if (in_array($propertyName, ['minPrice', 'maxPrice'])) {
            if ($this->minPrice && $this->maxPrice && $this->maxPrice < $this->minPrice) {
                $this->addError('maxPrice', 'El precio máximo debe ser mayor o igual al precio mínimo.');
            }
        }
    }

    // Removed updatedSearch() - handled by updated($propertyName)

    // Removed updatedSelectedCategory() - handled by updated($propertyName)

    // Removed updatedSelectedSubcategory() - handled by updated($propertyName)

    // Removed updatedSelectedLine() - handled by updated($propertyName)

    // Removed updatedSelectedBrand() - handled by updated($propertyName)

    // Removed updatedSelectedPresentation() - handled by updated($propertyName)

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function testFilters()
    {
        $this->search = 'test search';
        $this->selectedCategory = '1';
        $this->selectedBrand = '1';
        $this->selectedPresentation = '1';
        $this->minPrice = '100';
        $this->maxPrice = '1000';
        $this->sortBy = 'title';
        $this->resetPage();
        
        Log::info('Test filters set:', [
            'search' => $this->search,
            'selectedCategory' => $this->selectedCategory,
            'selectedBrand' => $this->selectedBrand,
            'selectedPresentation' => $this->selectedPresentation,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sortBy' => $this->sortBy
        ]);
    }

    public function debugFilters()
    {
        Log::info('Debug current filter values:', [
            'search' => $this->search,
            'selectedCategory' => $this->selectedCategory,
            'selectedSubcategory' => $this->selectedSubcategory,
            'selectedLine' => $this->selectedLine,
            'selectedBrand' => $this->selectedBrand,
            'selectedPresentation' => $this->selectedPresentation,
            'selectedQuality' => $this->selectedQuality,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection
        ]);
        
        $this->dispatch('debug-message', [
            'message' => 'Debug values logged to console. Check logs.',
            'values' => [
                'search' => $this->search,
                'selectedCategory' => $this->selectedCategory,
                'selectedBrand' => $this->selectedBrand,
                'selectedPresentation' => $this->selectedPresentation,
            ]
        ]);
    }

    public function testSearchFilter()
    {
        Log::info('🧪 Testing search filter:', [
            'current_search' => $this->search,
            'search_length' => strlen($this->search),
            'search_empty' => empty($this->search)
        ]);
        
        // Force refresh of products
        $this->resetPage();
        
        $this->dispatch('test-message', [
            'message' => 'Search filter test completed. Check logs.',
            'search' => $this->search
        ]);
    }

    public function testConnection()
    {
        Log::info('🔥 TEST CONNECTION: Livewire is working!');
        
        $this->dispatch('test-message', [
            'message' => '✅ Livewire connection is working!',
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedCategory', 'selectedSubcategory', 'selectedLine', 'selectedBrand', 'selectedPresentation', 'selectedQuality', 'minPrice', 'maxPrice', 'producer']);
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Obtiene la URL de la primera imagen de una publicación
     */
    public function getFirstImageUrl($listing)
    {
        // Usar el método del trait que ya maneja entornos
        return $listing->main_image_url;
    }

    public function getProductsProperty()
    {
        Log::info('🔥 Getting products with filters:', [
            'search' => $this->search,
            'search_length' => strlen($this->search),
            'search_empty' => empty($this->search),
            'selectedCategory' => $this->selectedCategory,
            'selectedSubcategory' => $this->selectedSubcategory,
            'selectedLine' => $this->selectedLine,
            'selectedBrand' => $this->selectedBrand,
            'selectedPresentation' => $this->selectedPresentation,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'sortBy' => $this->sortBy
        ]);
        
        return ProductListing::query()
            ->with([
                'product.productCategory',
                'product.productSubcategory',
                'product.productLine',
                'product.brand',
                'productPresentation',
                'person.user',
                'state',
                'municipality',
                'parish'
            ])
            ->where('status', 'active')
            ->when($this->search, function (Builder $query) {
                Log::info('🔍 Applying search filter:', [
                    'search_term' => $this->search,
                    'search_length' => strlen($this->search)
                ]);
                
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('product', function (Builder $productQuery) {
                            $productQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->selectedCategory, function (Builder $query) {
                $query->whereHas('product', function (Builder $productQuery) {
                    $productQuery->where('product_category_id', $this->selectedCategory);
                });
            })
            ->when($this->selectedSubcategory, function (Builder $query) {
                $query->whereHas('product', function (Builder $productQuery) {
                    $productQuery->where('product_subcategory_id', $this->selectedSubcategory);
                });
            })
            ->when($this->selectedLine, function (Builder $query) {
                $query->whereHas('product', function (Builder $productQuery) {
                    $productQuery->where('product_line_id', $this->selectedLine);
                });
            })
            ->when($this->selectedBrand, function (Builder $query) {
                $query->whereHas('product', function (Builder $productQuery) {
                    $productQuery->where('brand_id', $this->selectedBrand);
                });
            })
            ->when($this->selectedPresentation, function (Builder $query) {
                $query->where('product_presentation_id', $this->selectedPresentation);
            })
            ->when($this->selectedMarket, function (Builder $query) {
                $query->where('market_id', $this->selectedMarket);
            })
            ->when($this->selectedQuality, function (Builder $query) {
                $query->where('quality_grade', $this->selectedQuality);
            })
            ->when($this->minPrice, function (Builder $query) {
                $query->where('unit_price', '>=', $this->minPrice);
            })
            ->when($this->maxPrice, function (Builder $query) {
                $query->where('unit_price', '<=', $this->maxPrice);
            })
            ->when($this->producer, function (Builder $query) {
                $query->where('person_id', $this->producer);
            })
            ->when($this->sortBy === 'title', function (Builder $query) {
                $query->orderBy('title', 'asc');
            })
            ->when($this->sortBy === 'title_desc', function (Builder $query) {
                $query->orderBy('title', 'desc');
            })
            ->when($this->sortBy === 'unit_price', function (Builder $query) {
                $query->orderBy('unit_price', 'asc');
            })
            ->when($this->sortBy === 'unit_price_desc', function (Builder $query) {
                $query->orderBy('unit_price', 'desc');
            })
            ->when($this->sortBy === 'created_at', function (Builder $query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return ProductCategory::where('is_active', true)->orderBy('name')->get();
    }

    public function getSubcategoriesProperty()
    {
        try {
            if (!$this->selectedCategory) {
                return collect();
            }

            return ProductSubcategory::where('product_category_id', $this->selectedCategory)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching subcategories: ' . $e->getMessage());
            return collect();
        }
    }

    public function getSellersProperty()
    {
        return \App\Models\Person::whereHas('productListings', function($q) {
            $q->where('status', 'active');
        })->get();
    }

    public function getProductLinesProperty()
    {
        return ProductLine::where('is_active', true)
            ->when($this->selectedCategory, function ($query) {
                $query->where('product_category_id', $this->selectedCategory);
            })
            ->when($this->selectedSubcategory, function ($query) {
                $query->where('product_subcategory_id', $this->selectedSubcategory);
            })
            ->orderBy('name')
            ->get();
    }

    public function getBrandsProperty()
    {
        return Brand::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getPresentationsProperty()
    {
        return ProductPresentation::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getMarketsProperty()
    {
        return Market::query()->orderBy('name')->get();
    }

    /**
     * Obtiene los detalles completos de un producto para el modal
     */
    public function getProductDetails($productId)
    {
        return ProductListing::with([
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
        ->where('id', $productId)
        ->where('status', 'active')
        ->first();
    }

    /**
     * Obtiene los detalles del producto como JSON para el modal
     */
    public function getProductDetailsJson($productId)
    {
        $product = $this->getProductDetails($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        
        return response()->json($product);
    }

    public function showProductDetail($listingId)
    {
        try {
            Log::info('showProductDetail called with ID:', ['listing_id' => $listingId]);
            
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
            ->where('id', $listingId)
            ->where('status', 'active')
            ->firstOrFail();

            Log::info('Listing found:', ['listing_id' => $listing->id, 'title' => $listing->title]);

            // Preparar los datos para el modal
            $eventData = [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'unit_price' => $listing->unit_price,
                'formatted_price' => $listing->formatted_price,
                'bs_price' => $listing->bs_price,
                'current_rate' => $listing->current_rate,
                'presentation_quantity' => $listing->presentation_quantity,
                'product' => [
                    'name' => $listing->product->name,
                    'category_name' => $listing->product->productCategory?->name ?? 'N/A',
                    'subcategory_name' => $listing->product->productSubcategory?->name ?? 'N/A',
                    'brand_name' => $listing->product->brand?->name ?? 'N/A',
                    'presentation_name' => $listing->productPresentation?->name ?? 'N/A',
                    'presentation_unit' => $listing->productPresentation?->unit_type ?? 'unidades'
                ],
                'images' => $listing->images_url, // Usar el accessor del trait HasListingImages
                'seller' => [
                    'id' => $listing->person->id,
                    'name' => $listing->person->first_name . ' ' . $listing->person->last_name,
                ],
                'location' => $listing->location,
                'formatted_location' => $listing->formatted_location,
                'formatted_date' => $listing->harvest_date ? $listing->harvest_date->format('d/m/Y') : null,
                'status' => $listing->status,
                'formatted_status' => ucfirst($listing->status),
                'share_url' => route('catalogo.product', ['productId' => $listing->id]) // URL directa al producto
            ];

            Log::info('Dispatching showProductDetail event:', ['event_data' => $eventData]);
            
            // Disparar evento con todos los datos
            $this->dispatch('showProductDetail', $eventData);

        } catch (\Exception $e) {
            Log::error('Error showing product detail:', [
                'error' => $e->getMessage(),
                'listing_id' => $listingId
            ]);
        }
    }

    public function contactSeller($listingId)
    {
        try {
            $listing = ProductListing::with('person')->findOrFail($listingId);
            $person = $listing->person;

            // Intentar obtener número de WhatsApp/telefono del productor
            $rawPhone = $person->whatsapp ?? $person->phone ?? $person->mobile ?? null;
            $phone = $rawPhone ? preg_replace('/\D+/', '', $rawPhone) : '';

            if (!$phone) {
                $this->dispatch('error', 'El productor no tiene un número de WhatsApp configurado.');
                return;
            }

            // Mensaje inicial
            $message = "Hola {$person->first_name}, vi tu publicación \"{$listing->title}\" en EzequielNegocios. ¿Sigue disponible?";
            $url = 'https://wa.me/+58' . $phone . '?text=' . urlencode($message);

            // Redirección directa a WhatsApp
            return redirect()->away($url);
        } catch (\Exception $e) {
            Log::error('Error al preparar contacto por WhatsApp', [
                'listing_id' => $listingId,
                'error' => $e->getMessage(),
            ]);
            $this->dispatch('error', 'No se pudo abrir WhatsApp para contactar al productor.');
        }
    }

    public function loadMore()
    {
        $this->perPage += 12;
        Log::info('Loading more products:', [
            'new_per_page' => $this->perPage,
            'current_page' => $this->currentPage
        ]);
    }

    public function generateShareLink($listingId)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($listingId);
            
            // Crear URL con parámetro para abrir modal
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
                                    <input type='text' id='shareLink' value='" . $shareUrl . "' readonly style='flex: 1; padding: 8px; border: 1px solid #d1d5db; border-radius: 5px; font-size: 14px; background: #f9fafb;' onclick='this.select()' >
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
        return view('livewire.product-catalog', [
            'products' => $this->products,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'sellers' => $this->sellers,
            'productLines' => $this->productLines,
            'brands' => $this->brands,
            'presentations' => $this->presentations,
            'markets' => $this->markets,
            'exchangeRates' => $this->exchangeRates,
        ]);
    }
}
