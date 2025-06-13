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

    public function render()
    {
        return view('livewire.product-catalog', [
            'products' => $this->products,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }
}
