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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedSubcategory' => ['except' => ''],
        'selectedLine' => ['except' => ''],
        'selectedBrand' => ['except' => ''],
        'selectedPresentation' => ['except' => ''],
        'selectedQuality' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'producer' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc']
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

    public function mount()
    {
        $this->producer = request()->query('producer');
        $this->validate();
        
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
            'sortBy' => $this->sortBy
        ]);
    }

    public function getExchangeRatesProperty()
    {
        return [
            'usd' => ProductListing::getUsdRate(),
            'eur' => ProductListing::getEurRate()
        ];
    }

    public function updated($propertyName)
    {
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
                $query->orderBy('title', $this->sortDirection);
            })
            ->when($this->sortBy === 'title_desc', function (Builder $query) {
                $query->orderBy('title', 'desc');
            })
            ->when($this->sortBy === 'unit_price', function (Builder $query) {
                $query->orderBy('unit_price', $this->sortDirection);
            })
            ->when($this->sortBy === 'unit_price_desc', function (Builder $query) {
                $query->orderBy('unit_price', 'desc');
            })
            ->when($this->sortBy === 'created_at', function (Builder $query) {
                $query->orderBy('created_at', $this->sortDirection);
            })
            ->paginate(12);
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

            // Preparar los datos para el modal
            $this->dispatch('showProductDetail', [
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
                'formatted_status' => ucfirst($listing->status)
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing product detail:', [
                'error' => $e->getMessage(),
                'listing_id' => $listingId
            ]);
        }
    }

    public function contactSeller($productId)
    {
        // Aquí puedes implementar la lógica para contactar al vendedor
        $this->dispatch('contactProducer', ['productId' => $productId]);
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
            'exchangeRates' => $this->exchangeRates,
        ]);
    }
}
