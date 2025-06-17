<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProductsCrud extends Component
{
    use WithFileUploads;

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
    public $changeImage = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        // $person = Auth::user()->person;
        // if (!$person) {
            $this->products = Product::where('is_active', true)
            ->with(['category', 'subcategory'])
            ->orderBy('id', 'asc')
            ->get();
            // return;
        // }

        // $this->products = Product::where('is_active', true)
        //     ->whereHas('listings', function($q) use ($person) {
        //         $q->where('person_id', $person->id);
        //     })
        //     ->with(['category', 'subcategory'])
        //     ->get();

        // $this->products = Product::where('is_active', true)
        //     ->with(['category', 'subcategory'])
        //     ->get();

        // dd($this->products);
    }

    public function openModal($productId = null)
    {
        $this->resetForm();
        $this->changeImage = false;
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

    public function saveProduct()
    {
        $formImageRule = is_string($this->form['image']) ? 'nullable|string' : 'nullable|image|max:2048';

        $this->validate([
            'form.category_id' => 'required|exists:product_categories,id',
            'form.subcategory_id' => 'required|exists:product_subcategories,id',
            'form.name' => 'required|string|max:255',
            'form.description' => 'nullable|string',
            'form.sku_base' => 'nullable|string|max:255',
            'form.unit_type' => 'required|string',
            'form.image' => $formImageRule,
            'form.seasonal_info' => 'nullable|string',
            'form.is_active' => 'boolean',
        ]);

        $imagePath = null;

        // Si es edición y no se sube nueva imagen, conserva la anterior
        if ($this->editingProduct) {
            $product = $this->editingProduct;
            if ($this->form['image'] instanceof TemporaryUploadedFile) {
                $imagePath = $this->form['image']->store('products', 'public');
            } else {
                $imagePath = $product->image;
            }
            $product->update([
                'category_id' => $this->form['category_id'],
                'subcategory_id' => $this->form['subcategory_id'],
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'sku_base' => $this->form['sku_base'],
                'unit_type' => $this->form['unit_type'],
                'image' => $imagePath,
                'seasonal_info' => $this->form['seasonal_info'],
                'is_active' => $this->form['is_active'],
            ]);
        } else {
            // Crear nuevo producto
            if ($this->form['image'] instanceof TemporaryUploadedFile) {
                $imagePath = $this->form['image']->store('products', 'public');
            }
            Product::create([
                'category_id' => $this->form['category_id'],
                'subcategory_id' => $this->form['subcategory_id'],
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'sku_base' => $this->form['sku_base'],
                'unit_type' => $this->form['unit_type'],
                'image' => $imagePath,
                'seasonal_info' => $this->form['seasonal_info'],
                'is_active' => $this->form['is_active'],
            ]);
        }

        $this->closeModal();
        $this->loadProducts();
        session()->flash('success', $this->editingProduct ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');
        $this->dispatch('product-added');
        $this->dispatch('product-updated');
        // $this->dispatch('product-deleted');
    }

    public function enableImageChange()
    {
        $this->changeImage = true;
        $this->form['image'] = null;
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
            'products' => $this->products,
        ]);
    }
}
