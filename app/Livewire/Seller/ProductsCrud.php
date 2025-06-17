<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Support\Facades\Auth;

class ProductsCrud extends Component
{
    public $products;
    public $showModal = false;
    public $editingProduct = null;
    public $form = [
        'category_id' => '',
        'subcategory_id' => '',
        'name' => '',
        'description' => '',
        'sku_base' => '',
        'unit_type' => '',
        'image' => null,
        'seasonal_info' => '',
        'is_active' => true,
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $person = Auth::user()->person;
        if (!$person) {
            $this->products = collect();
            return;
        }

        $this->products = Product::where('is_active', true)
            ->whereHas('listings', function($q) use ($person) {
                $q->where('person_id', $person->id);
            })
            ->with(['category', 'subcategory'])
            ->get();
    }

    public function openModal($productId = null)
    {
        $this->resetForm();
        if ($productId) {
            $this->editingProduct = Product::findOrFail($productId);
            $this->form = [
                'category_id' => $this->editingProduct->category_id,
                'subcategory_id' => $this->editingProduct->subcategory_id,
                'name' => $this->editingProduct->name,
                'description' => $this->editingProduct->description,
                'sku_base' => $this->editingProduct->sku_base,
                'unit_type' => $this->editingProduct->unit_type,
                'image' => $this->editingProduct->image,
                'seasonal_info' => $this->editingProduct->seasonal_info,
                'is_active' => $this->editingProduct->is_active,
            ];
        } else {
            $this->editingProduct = null;
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingProduct = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form = [
            'category_id' => '',
            'subcategory_id' => '',
            'name' => '',
            'description' => '',
            'sku_base' => '',
            'unit_type' => '',
            'image' => null,
            'seasonal_info' => '',
            'is_active' => true,
        ];
    }

    public function categoryChanged($value)
    {
        $this->form['category_id'] = $value;
        $this->form['subcategory_id'] = '';
    }

    public function render()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $subcategories = $this->form['category_id']
            ? ProductSubcategory::where('category_id', (int)$this->form['category_id'])->where('is_active', 't')->get()
            : collect();

        logger('Categoria seleccionada: ' . $this->form['category_id']);
        logger('Subcategorias encontradas: ' . $subcategories->pluck('name')->join(', '));

        // Debug temporal
        // dd($this->form['category_id'], $subcategories);

        return view('livewire.seller.products-crud', [
            'categories' => $categories,
            'subcategories' => $subcategories,
        ]);
    }
}
