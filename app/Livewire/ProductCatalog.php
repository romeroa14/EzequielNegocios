<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductListing;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Database\Eloquent\Builder;

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

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => ''],
        'selectedSubcategory' => ['except' => ''],
        'selectedQuality' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        if ($propertyName === 'search') {
            $this->resetPage();
        }

        if ($propertyName === 'selectedCategory') {
            $this->selectedSubcategory = '';
            $this->resetPage();
        }

        if (in_array($propertyName, ['minPrice', 'maxPrice'])) {
            if ($this->minPrice && $this->maxPrice && $this->maxPrice < $this->minPrice) {
                $this->addError('maxPrice', 'El precio máximo debe ser mayor o igual al precio mínimo.');
            }
        }
    }

    public function mount()
    {
        $this->producer = request()->query('producer');
        $this->validate();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->selectedSubcategory = '';
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->selectedSubcategory = '';
        $this->selectedQuality = '';
        $this->minPrice = '';
        $this->maxPrice = '';
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

    public function getProductsProperty()
    {
        return ProductListing::query()
            ->with(['product.category', 'product.subcategory', 'person.user'])
            ->where('status', 'active')
            // ->where('expiry_date', '>', now())
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $subQuery) {
                    $subQuery->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('product', function (Builder $productQuery) {
                            $productQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->selectedCategory, function (Builder $query) {
                $query->whereHas('product.category', function (Builder $categoryQuery) {
                    $categoryQuery->where('id', $this->selectedCategory);
                });
            })
            ->when($this->selectedSubcategory, function (Builder $query) {
                $query->whereHas('product.subcategory', function (Builder $subcategoryQuery) {
                    $subcategoryQuery->where('id', $this->selectedSubcategory);
                });
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
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    public function getCategoriesProperty()
    {
        return ProductCategory::where('is_active', true)->orderBy('name')->get();
    }

    public function getSubcategoriesProperty()
    {
        if (!$this->selectedCategory) {
            return collect();
        }

        return ProductSubcategory::where('category_id', $this->selectedCategory)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getSellersProperty()
    {
        return \App\Models\Person::whereHas('productListings', function($q) {
            $q->where('status', 'active');
        })->get();
    }

    public function render()
    {
        return view('livewire.product-catalog', [
            'products' => $this->products,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            // 'producer' => $this->producer,
            'sellers' => $this->sellers,
        ]);
    }
}
