<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use App\Models\ProductLine;
use App\Models\Brand;
use App\Models\ProductPresentation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use App\Models\ProductListing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductsCrud extends Component
{
    use WithFileUploads;

    public $products;
    public $showModal = false;
    public $editingProduct = null;
    public $selectedPresentation = null;
    public $lines;
    public $form = [
        'product_category_id' => '',
        'product_subcategory_id' => '',
        'product_line_id' => '',
        'brand_id' => '',
        'product_presentation_id' => '',
        'name' => '',
        'description' => '',
        'sku_base' => '',
        'custom_quantity' => 1,
        'image' => null,
        'seasonal_info' => '',
        'is_active' => true,
    ];
    public $changeImage = false;
    public $productIdToDelete = null;
    public $subcategories;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'deleteProduct'
    ];

    protected function rules()
    {
        $productId = $this->editingProduct ? $this->editingProduct->id : null;
        
        $rules = [
            'form.product_category_id' => 'required|exists:product_categories,id',
            'form.product_subcategory_id' => 'required|exists:product_subcategories,id',
            'form.product_line_id' => 'required|exists:product_lines,id',
            'form.brand_id' => 'required|exists:brands,id',
            'form.product_presentation_id' => 'required|exists:product_presentations,id',
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.custom_quantity' => 'required|numeric|min:0.01',
            'form.seasonal_info' => 'nullable|string',
            'form.is_active' => 'boolean',
            'form.sku_base' => 'required|string|max:50|unique:products,sku_base',
        ];

        
        // Agregar regla de imagen con validación condicional
        if ($productId) {
            $rules['form.image'] = 'nullable|image|max:2048';
        } else {
            $rules['form.image'] = 'required|image|max:2048';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'form.product_category_id.required' => 'La categoría es obligatoria.',
            'form.product_category_id.exists' => 'La categoría seleccionada no existe.',
            'form.product_subcategory_id.required' => 'La subcategoría es obligatoria.',
            'form.product_subcategory_id.exists' => 'La subcategoría seleccionada no existe.',
            'form.product_line_id.required' => 'La línea de producto es obligatoria.',
            'form.product_line_id.exists' => 'La línea de producto seleccionada no existe.',
            'form.brand_id.required' => 'La marca es obligatoria.',
            'form.brand_id.exists' => 'La marca seleccionada no existe.',
            'form.product_presentation_id.required' => 'La presentación es obligatoria.',
            'form.product_presentation_id.exists' => 'La presentación seleccionada no existe.',
            'form.name.required' => 'El nombre es obligatorio.',
            'form.name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'form.description.required' => 'La descripción es obligatoria.',
            'form.sku_base.required' => 'El SKU base es obligatorio.',
            'form.sku_base.max' => 'El SKU base no puede tener más de 50 caracteres.',
            'form.sku_base.unique' => 'Este SKU base ya está en uso.',
            'form.custom_quantity.required' => 'La cantidad es obligatoria.',
            'form.custom_quantity.numeric' => 'La cantidad debe ser un número.',
            'form.custom_quantity.min' => 'La cantidad debe ser mayor a 0.',
            'form.image.required' => 'La imagen es obligatoria.',
            'form.image.image' => 'El archivo debe ser una imagen.',
            'form.image.max' => 'La imagen no puede ser mayor a 2MB.',
            'form.seasonal_info.string' => 'La información estacional debe ser texto.',
            'form.is_active.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function presentationChanged()
    {
        if ($this->form['product_presentation_id']) {
            $this->selectedPresentation = ProductPresentation::find($this->form['product_presentation_id']);
            $this->form['custom_quantity'] = 1;
        } else {
            $this->selectedPresentation = null;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if ($propertyName === 'form.product_category_id') {
            // Al cambiar la categoría, reseteamos subcategoría y línea
            $this->form['product_subcategory_id'] = '';
            $this->form['product_line_id'] = '';
            $this->lines = collect();
            
            // Cargamos las subcategorías de la categoría seleccionada
            if (!empty($this->form['product_category_id'])) {
                $this->subcategories = ProductSubcategory::where('product_category_id', $this->form['product_category_id'])
                    ->where('is_active', true)
                    ->get();
                Log::info('Subcategorías cargadas para categoría', [
                    'category_id' => $this->form['product_category_id'],
                    'subcategories' => $this->subcategories->pluck('name', 'id')
                ]);
            }
        }

        if ($propertyName === 'form.product_subcategory_id') {
            // Al cambiar la subcategoría, reseteamos la línea
            $this->form['product_line_id'] = '';
            
            if (!empty($this->form['product_subcategory_id'])) {
                Log::info('Buscando líneas para subcategoría', [
                    'category_id' => $this->form['product_category_id'],
                    'subcategory_id' => $this->form['product_subcategory_id']
                ]);

                // Cargamos las líneas que coincidan con la categoría y subcategoría
                $this->lines = ProductLine::where('product_subcategory_id', $this->form['product_subcategory_id'])
                    ->where('product_category_id', $this->form['product_category_id'])
                    ->where('is_active', true)
                    ->get();

                Log::info('Líneas encontradas', [
                    'count' => $this->lines->count(),
                    'lines' => $this->lines->pluck('name', 'id')->toArray()
                ]);
            } else {
                $this->lines = collect();
            }
        }

        if ($propertyName === 'form.product_presentation_id') {
            $this->presentationChanged();
        }
    }

    public function updateProductLines()
    {
        if (!empty($this->form['product_subcategory_id'])) {
            $this->lines = ProductLine::where('product_subcategory_id', $this->form['product_subcategory_id'])
                ->where('is_active', true)
                ->get();
        } else {
            $this->lines = collect();
        }
    }

    public function mount()
    {
        $this->loadProducts();
        $this->lines = collect();
        $this->subcategories = collect();
    }

    public function loadProducts()
    {
        $personId = Auth::id();

        // Obtener productos universales de todos los productores universales
        $universalProducts = Product::whereHas('creator', function ($query) {
            $query->where('is_universal', true);
        })->where('is_universal', true)->get();

        // Obtener productos del vendedor actual
        $sellerProducts = Product::where('person_id', $personId)
            ->where('is_universal', false)
            ->get();

        $this->products = [
            'universal' => $universalProducts,
            'seller' => $sellerProducts,
        ];
    }

    public function openModal($productId = null)
    {
        $this->resetForm();
        $this->changeImage = false;
        if ($productId) {
            $this->editingProduct = Product::where('id', $productId)
                ->where('person_id', Auth::id())
                ->firstOrFail();
            
            $this->form = [
                'product_category_id' => $this->editingProduct->product_category_id,
                'product_subcategory_id' => $this->editingProduct->product_subcategory_id,
                'product_line_id' => $this->editingProduct->product_line_id,
                'brand_id' => $this->editingProduct->brand_id,
                'product_presentation_id' => $this->editingProduct->product_presentation_id,
                'name' => $this->editingProduct->name,
                'description' => $this->editingProduct->description,
                'sku_base' => $this->editingProduct->sku_base,
                'custom_quantity' => $this->editingProduct->custom_quantity ?? 1,
                'image' => $this->editingProduct->image,
                'seasonal_info' => $this->editingProduct->seasonal_info,
                'is_active' => $this->editingProduct->is_active,
            ];
            
            if ($this->form['product_presentation_id']) {
                $this->selectedPresentation = ProductPresentation::find($this->form['product_presentation_id']);
            }

            // Cargar las líneas de producto basadas en la categoría y subcategoría del producto
            if ($this->form['product_subcategory_id'] && $this->form['product_category_id']) {
                $this->lines = ProductLine::where('product_subcategory_id', $this->form['product_subcategory_id'])
                    ->where('product_category_id', $this->form['product_category_id'])
                    ->where('is_active', true)
                    ->get();
            }
        } else {
            $this->editingProduct = null;
            $this->lines = collect();
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
            'product_category_id' => '',
            'product_subcategory_id' => '',
            'product_line_id' => '',
            'brand_id' => '',
            'product_presentation_id' => '',
            'name' => '',
            'description' => '',
            'sku_base' => '',
            'custom_quantity' => 1,
            'image' => null,
            'seasonal_info' => '',
            'is_active' => true,
        ];
        $this->selectedPresentation = null;
    }

    public function categoryChanged($value)
    {
        $this->form['product_category_id'] = $value;
        $this->form['product_subcategory_id'] = '';
    }

    public function deleteProduct($productId)
    {
        $product = Product::where('id', $productId)->where('person_id', Auth::id())->firstOrFail();
        $product->delete();
        $this->loadProducts();
        session()->flash('success', 'Producto eliminado correctamente.');
        $this->dispatch('product-deleted');
    }

    public function saveProduct()
    {
        $this->validate();

        try {
            $data = collect($this->form)->except(['image'])->toArray();
            
            // Si el usuario es un productor universal y está creando un producto universal
            if (Auth::user()->is_universal && $this->form['is_universal']) {
                $data['is_universal'] = true;
            } else {
                $data['is_universal'] = false;
            }

            // Manejar la imagen antes de crear/actualizar el producto
            $imagePath = null;
            if ($this->form['image'] && $this->form['image'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Determinar el disco a usar basado en el entorno
                $disk = app()->environment('production') ? 'r2' : 'public';
                
                // Generar un nombre único para el archivo
                $extension = $this->form['image']->getClientOriginalExtension();
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                $path = 'products/' . $fileName;

                Log::info('Guardando imagen', [
                    'disk' => $disk,
                    'path' => $path,
                    'fileName' => $fileName
                ]);

                // Almacenar el archivo
                if ($disk === 'r2') {
                    // Para R2 en producción
                    $imagePath = $this->form['image']->storePublicly($path, ['disk' => $disk]);
                    
                    // Verificar si el archivo se guardó correctamente
                    $exists = Storage::disk($disk)->exists($imagePath);
                    Log::info('Verificación de almacenamiento en R2', [
                        'path' => $imagePath,
                        'exists' => $exists
                    ]);

                    if (!$exists) {
                        throw new \Exception('El archivo no se guardó correctamente en R2');
                    }
                } else {
                    // Para almacenamiento local en desarrollo
                    $imagePath = $this->form['image']->storeAs('products', $fileName, 'public');
                    Log::info('Imagen almacenada localmente', [
                        'path_resultado' => $imagePath
                    ]);
                }

                // Agregar la ruta de la imagen a los datos
                $data['image'] = $imagePath;
            }

            if ($this->editingProduct) {
                // Si estamos editando, eliminar la imagen anterior si existe
                if ($imagePath && $this->editingProduct->image) {
                    $this->editingProduct->deleteImage();
                }
                $this->editingProduct->update($data);
                $product = $this->editingProduct;
            } else {
                $data['person_id'] = Auth::id();
                $product = Product::create($data);
            }

            $this->dispatch('product-' . ($this->editingProduct ? 'updated' : 'added'));
            $this->closeModal();
            $this->loadProducts();

        } catch (\Exception $e) {
            Log::error('Error al guardar producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('error', $e->getMessage());
        }
    }

    public function enableImageChange()
    {
        $this->changeImage = true;
        $this->form['image'] = null;
    }

    public function confirmDelete($productId)
    {
        $this->productIdToDelete = $productId;
        $this->dispatch('show-delete-confirmation');
    }

    public function render()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        
        // Si no hay subcategorías cargadas y hay una categoría seleccionada, las cargamos
        if ((!isset($this->subcategories) || $this->subcategories->isEmpty()) && 
            !empty($this->form['product_category_id'])) {
            $this->subcategories = ProductSubcategory::where('product_category_id', $this->form['product_category_id'])
                ->where('is_active', true)
                ->get();
        }

        // Si no hay líneas cargadas y tenemos categoría y subcategoría, las cargamos
        if ((!isset($this->lines) || $this->lines->isEmpty()) && 
            !empty($this->form['product_category_id']) && 
            !empty($this->form['product_subcategory_id'])) {
            
            $this->lines = ProductLine::where('product_category_id', $this->form['product_category_id'])
                ->where('product_subcategory_id', $this->form['product_subcategory_id'])
                ->where('is_active', true)
                ->get();

            Log::info('Estado actual de líneas', [
                'category_id' => $this->form['product_category_id'],
                'subcategory_id' => $this->form['product_subcategory_id'],
                'lines_count' => $this->lines->count(),
                'lines' => $this->lines->pluck('name', 'id')->toArray()
            ]);
        }

        $brands = Brand::where('is_active', true)->get();
        $presentations = ProductPresentation::where('is_active', true)->get();

        return view('livewire.seller.products-crud', [
            'categories' => $categories,
            'subcategories' => $this->subcategories ?? collect(),
            'lines' => $this->lines ?? collect(),
            'brands' => $brands,
            'presentations' => $presentations,
            'products' => $this->products,
        ]);
    }
}
