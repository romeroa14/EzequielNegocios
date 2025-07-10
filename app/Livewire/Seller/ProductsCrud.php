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
        ];

        // Agregar regla de SKU con validación condicional
        if ($productId) {
            $rules['form.sku_base'] = "required|string|max:50|unique:products,sku_base,{$productId}";
        } else {
            $rules['form.sku_base'] = 'required|string|max:50|unique:products,sku_base';
        }

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
        $this->products = Product::where('person_id', $personId)
            ->where('is_active', true)
            ->with(['productCategory', 'productSubcategory'])
            ->orderBy('id', 'desc')
            ->get();
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

    private function storeImage($image)
    {
        try {
            Log::info('Intentando almacenar imagen', [
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'ambiente' => app()->environment(),
            ]);

            // Generar un nombre único para el archivo
            $extension = $image->getClientOriginalExtension();
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $path = 'products/' . $fileName;

            // Determinar el disco a usar
            $disk = app()->environment('production') ? 'r2' : 'public';

            Log::info('Configuración de almacenamiento', [
                'disk' => $disk,
                'path' => $path,
                'fileName' => $fileName
            ]);

            // Almacenar el archivo
            if ($disk === 'r2') {
                try {
                    // Para R2 en producción
                    $path = $image->storePublicly($path, ['disk' => $disk]);
                    
                    // Verificar si el archivo se guardó correctamente
                    $exists = Storage::disk($disk)->exists($path);
                    Log::info('Verificación de almacenamiento en R2', [
                        'path' => $path,
                        'exists' => $exists
                    ]);

                    if (!$exists) {
                        throw new \Exception('El archivo no se guardó correctamente en R2');
                    }

                    // Construir la URL directamente para R2
                    $url = rtrim(config('filesystems.disks.r2.url'), '/') . '/' . ltrim($path, '/');
                    Log::info('URL del archivo en R2', ['url' => $url]);

                } catch (\Exception $e) {
                    Log::error('Error al almacenar en R2', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                // Para almacenamiento local en desarrollo
                $path = $image->storeAs('products', $fileName, 'public');
                $url = url('storage/' . $path);
                Log::info('Imagen almacenada localmente', [
                    'path_resultado' => $path,
                    'url' => $url
                ]);
            }

            Log::info('Imagen almacenada correctamente', [
                'path' => $path,
                'disk' => $disk,
                'url' => $url,
                'storage_exists' => Storage::disk($disk)->exists($path)
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('Error al almacenar imagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'disk' => $disk ?? 'unknown',
                'path' => $path ?? 'unknown'
            ]);
            
            throw $e;
        }
    }

    public function saveProduct()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $imageToSave = $this->form['image'];
            if ($imageToSave instanceof TemporaryUploadedFile) {
                $imagePath = $imageToSave->store('products', 'public');
            } else {
                $imagePath = $this->editingProduct ? $this->editingProduct->image : null;
            }

            $productData = [
                'person_id' => Auth::id(),
                'product_category_id' => $this->form['product_category_id'],
                'product_subcategory_id' => $this->form['product_subcategory_id'],
                'product_line_id' => $this->form['product_line_id'],
                'brand_id' => $this->form['brand_id'],
                'product_presentation_id' => $this->form['product_presentation_id'],
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'sku_base' => $this->form['sku_base'],
                'custom_quantity' => $this->form['custom_quantity'],
                'image' => $imagePath,
                'seasonal_info' => $this->form['seasonal_info'],
                'is_active' => $this->form['is_active'],
            ];

            if ($this->editingProduct) {
                $this->editingProduct->update($productData);
                $this->dispatch('product-updated');
            } else {
                Product::create($productData);
                $this->dispatch('product-added');
            }

            DB::commit();
            $this->loadProducts();
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar producto:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al guardar el producto. Por favor, intente nuevamente.');
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
