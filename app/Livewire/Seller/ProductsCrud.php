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
        
        Log::info('🔍 GENERANDO REGLAS DE VALIDACIÓN', [
            'product_id' => $productId,
            'sku_base' => $this->form['sku_base'] ?? 'no definido',
            'editing_product' => $this->editingProduct ? $this->editingProduct->sku_base : 'nuevo producto'
        ]);
        
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
        ];

        // Regla de SKU Base con exclusión del producto actual al editar
        if ($productId) {
            $rules['form.sku_base'] = 'required|string|max:50|unique:products,sku_base,' . $productId;
            Log::info('📝 REGLA SKU PARA EDICIÓN', [
                'rule' => $rules['form.sku_base'],
                'excluding_id' => $productId
            ]);
        } else {
            $rules['form.sku_base'] = 'required|string|max:50|unique:products,sku_base';
            Log::info('📝 REGLA SKU PARA CREACIÓN', [
                'rule' => $rules['form.sku_base']
            ]);
        }
        
        // Agregar regla de imagen con validación condicional
        if ($productId) {
            $rules['form.image'] = 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048';
        } else {
            $rules['form.image'] = 'required|image|mimes:png,jpg,jpeg,webp|max:2048';
        }

        Log::info('✅ REGLAS GENERADAS', ['rules' => array_keys($rules)]);
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
            'form.image.mimes' => 'La imagen debe ser de tipo: PNG, JPG, JPEG o WEBP.',
            'form.image.max' => 'La imagen no puede ser mayor a 2MB.',
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
        
        // Disparar evento después de cargar productos
        $this->dispatch('products-loaded');
    }

    public function openModal($productId = null)
    {
        Log::info('🔓 ABRIENDO MODAL', [
            'product_id' => $productId,
            'user_id' => Auth::id()
        ]);

        $this->resetForm();
        $this->changeImage = false;
        
        if ($productId) {
            try {
            $this->editingProduct = Product::where('id', $productId)
                ->where('person_id', Auth::id())
                ->firstOrFail();
            
                Log::info('📦 PRODUCTO CARGADO PARA EDICIÓN', [
                    'product' => [
                        'id' => $this->editingProduct->id,
                        'name' => $this->editingProduct->name,
                        'sku_base' => $this->editingProduct->sku_base,
                        'person_id' => $this->editingProduct->person_id
                    ]
                ]);
                
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
                    'image' => null, // No cargar la imagen existente en el form
                'seasonal_info' => $this->editingProduct->seasonal_info,
                'is_active' => $this->editingProduct->is_active,
            ];
                
                Log::info('📝 FORMULARIO CARGADO', [
                    'form_data' => $this->form
                ]);
            
            if ($this->form['product_presentation_id']) {
                $this->selectedPresentation = ProductPresentation::find($this->form['product_presentation_id']);
                    Log::info('🎯 PRESENTACIÓN SELECCIONADA', [
                        'presentation' => $this->selectedPresentation ? $this->selectedPresentation->name : 'no encontrada'
                    ]);
                }

                // Cargar subcategorías para la categoría seleccionada
                if ($this->form['product_category_id']) {
                    $this->subcategories = ProductSubcategory::where('product_category_id', $this->form['product_category_id'])
                        ->where('is_active', true)
                        ->get();
                    
                    Log::info('📂 SUBCATEGORÍAS CARGADAS', [
                        'count' => $this->subcategories->count(),
                        'category_id' => $this->form['product_category_id']
                    ]);
            }

            // Cargar las líneas de producto basadas en la categoría y subcategoría del producto
            if ($this->form['product_subcategory_id'] && $this->form['product_category_id']) {
                $this->lines = ProductLine::where('product_subcategory_id', $this->form['product_subcategory_id'])
                    ->where('product_category_id', $this->form['product_category_id'])
                    ->where('is_active', true)
                    ->get();
                    
                    Log::info('📋 LÍNEAS CARGADAS', [
                        'count' => $this->lines->count(),
                        'subcategory_id' => $this->form['product_subcategory_id'],
                        'category_id' => $this->form['product_category_id']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('❌ ERROR AL CARGAR PRODUCTO PARA EDICIÓN', [
                    'product_id' => $productId,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                
                session()->flash('error', 'No se pudo cargar el producto para editar.');
                return;
            }
        } else {
            Log::info('➕ MODO CREACIÓN - NUEVO PRODUCTO');
            $this->editingProduct = null;
            $this->lines = collect();
            $this->subcategories = collect();
        }
        
        $this->showModal = true;
        Log::info('✅ MODAL ABIERTO CORRECTAMENTE');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingProduct = null;
        $this->resetForm();
        
        // Disparar evento para que el JavaScript sepa que el modal se cerró
        $this->dispatch('modal-closed');
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
        Log::info('🔧 INICIANDO saveProduct', [
            'editing_product_id' => $this->editingProduct ? $this->editingProduct->id : null,
            'form_data' => $this->form,
            'user_id' => Auth::id()
        ]);

        $this->validate();

        try {
            $data = collect($this->form)->except(['image'])->toArray();
            
            // Los productos del vendedor nunca son universales (solo los admins pueden crear productos universales)
                $data['is_universal'] = false;

            Log::info('📊 DATOS PREPARADOS', [
                'data' => $data,
                'has_image' => !empty($this->form['image']),
                'image_type' => $this->form['image'] ? get_class($this->form['image']) : null
            ]);

            // Manejar la imagen antes de crear/actualizar el producto
            $imagePath = null;
            if ($this->form['image'] && $this->form['image'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Siempre usar el disco 'public' para desarrollo/local
                $disk = app()->environment('production') ? 'r2' : 'public';
                
                // Generar un nombre único para el archivo
                $extension = $this->form['image']->getClientOriginalExtension();
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                $path = 'products/' . $fileName;

                Log::info('📷 GUARDANDO IMAGEN', [
                    'disk' => $disk,
                    'path' => $path,
                    'fileName' => $fileName,
                    'extension' => $extension
                ]);

                // Almacenar el archivo
                if ($disk === 'r2') {
                    // Para R2 en producción
                    $imagePath = $this->form['image']->storePublicly($path, ['disk' => $disk]);
                    
                    // Verificar si el archivo se guardó correctamente
                    $exists = Storage::disk($disk)->exists($imagePath);
                    Log::info('✅ VERIFICACIÓN R2', [
                        'path' => $imagePath,
                        'exists' => $exists
                    ]);

                    if (!$exists) {
                        throw new \Exception('El archivo no se guardó correctamente en R2');
                    }
                } else {
                    // Para almacenamiento local en desarrollo SIEMPRE usar storage/app/public/products
                    $imagePath = $this->form['image']->storeAs('products', $fileName, 'public');
                    Log::info('✅ IMAGEN LOCAL GUARDADA', [
                        'path_resultado' => $imagePath
                    ]);
                }

                // Agregar la ruta de la imagen a los datos
                $data['image'] = $imagePath;
                Log::info('📷 IMAGEN AGREGADA A DATOS', ['image_path' => $imagePath]);
            }

            if ($this->editingProduct) {
                Log::info('✏️ ACTUALIZANDO PRODUCTO', [
                    'product_id' => $this->editingProduct->id,
                    'data' => $data
                ]);

                // Si estamos editando, eliminar la imagen anterior si existe y hay una nueva
                if ($imagePath && $this->editingProduct->image) {
                    Log::info('🗑️ ELIMINANDO IMAGEN ANTERIOR', [
                        'old_image' => $this->editingProduct->image
                    ]);
                    $this->editingProduct->deleteImage();
                }
                
                $this->editingProduct->update($data);
                $product = $this->editingProduct;
                
                Log::info('✅ PRODUCTO ACTUALIZADO', [
                    'product_id' => $product->id,
                    'name' => $product->name
                ]);
            } else {
                Log::info('➕ CREANDO NUEVO PRODUCTO');
                $data['person_id'] = Auth::id();
                $product = Product::create($data);
                
                Log::info('✅ PRODUCTO CREADO', [
                    'product_id' => $product->id,
                    'name' => $product->name
                ]);
            }

            $this->dispatch('product-' . ($this->editingProduct ? 'updated' : 'added'), ['productId' => $product->id]);
            
            // Disparar evento adicional para scroll
            $this->dispatch('scroll-to-product', ['productId' => $product->id]);
            
            // Disparar evento JavaScript directo
            $this->js("
                setTimeout(() => {
                    if (window.scrollToProduct) {
                        window.scrollToProduct({$product->id});
                    }
                }, 2000);
            ");
            $this->closeModal();
            $this->loadProducts();

            Log::info('🎉 SAVEPRODUCT COMPLETADO EXITOSAMENTE');

        } catch (\Exception $e) {
            Log::error('❌ ERROR EN SAVEPRODUCT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $this->form,
                'editing_product_id' => $this->editingProduct ? $this->editingProduct->id : null
            ]);
            
            $this->dispatch('error', $e->getMessage());
            session()->flash('error', 'Error al guardar el producto: ' . $e->getMessage());
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
