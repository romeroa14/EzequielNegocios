<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductLine;
use App\Models\Brand;
use App\Models\ProductPresentation;

class MobileFilters extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $selectedSubcategory = '';
    public $selectedQuality = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $selectedLine = null;
    public $selectedBrand = null;
    public $selectedPresentation = null;
    public $showFilters = false;

    protected $listeners = ['showMobileFilters' => 'showFilters'];

    public function mount()
    {
        // Inicializar con los valores actuales del catálogo si existen
        $this->search = request()->query('search', '');
        $this->selectedCategory = request()->query('selectedCategory', '');
        $this->selectedSubcategory = request()->query('selectedSubcategory', '');
        $this->selectedQuality = request()->query('selectedQuality', '');
        $this->minPrice = request()->query('minPrice', '');
        $this->maxPrice = request()->query('maxPrice', '');
        $this->selectedLine = request()->query('selectedLine', null);
        $this->selectedBrand = request()->query('selectedBrand', null);
        $this->selectedPresentation = request()->query('selectedPresentation', null);
    }

    public function showFilters()
    {
        $this->showFilters = true;
    }

    public function hideFilters()
    {
        $this->showFilters = false;
    }

    public function applyFilters()
    {
        $filters = [
            'search' => $this->search,
            'selectedCategory' => $this->selectedCategory,
            'selectedSubcategory' => $this->selectedSubcategory,
            'selectedQuality' => $this->selectedQuality,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'selectedLine' => $this->selectedLine,
            'selectedBrand' => $this->selectedBrand,
            'selectedPresentation' => $this->selectedPresentation,
        ];

        $this->dispatch('filtersUpdated', $filters);
        $this->hideFilters();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'selectedCategory',
            'selectedSubcategory',
            'selectedLine',
            'selectedBrand',
            'selectedPresentation',
            'selectedQuality',
            'minPrice',
            'maxPrice'
        ]);

        $this->applyFilters();
    }

    public function render()
    {
        return view('livewire.components.mobile-filters', [
            'categories' => ProductCategory::where('is_active', true)->orderBy('name')->get(),
            'subcategories' => $this->selectedCategory ? 
                ProductSubcategory::where('product_category_id', $this->selectedCategory)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get() : collect(),
            'productLines' => ProductLine::where('is_active', true)
                ->when($this->selectedCategory, function ($query) {
                    $query->where('product_category_id', $this->selectedCategory);
                })
                ->when($this->selectedSubcategory, function ($query) {
                    $query->where('product_subcategory_id', $this->selectedSubcategory);
                })
                ->orderBy('name')
                ->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'presentations' => ProductPresentation::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
} 