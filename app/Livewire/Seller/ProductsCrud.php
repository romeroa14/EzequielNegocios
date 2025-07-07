<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
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
    public $productIdToDelete = null;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'deleteProduct'
    ];

    protected function rules()
    {
        $productId = $this->editingProduct ? $this->editingProduct->id : null;
        
        $rules = [
            'form.category_id' => 'required|exists:product_categories,id',
            'form.subcategory_id' => 'required|exists:product_subcategories,id',
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.unit_type' => 'required|in:kg,ton,saco,caja,unidad',
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
            'form.category_id.required' => 'La categoría es obligatoria.',
            'form.category_id.exists' => 'La categoría seleccionada no existe.',
            'form.subcategory_id.required' => 'La subcategoría es obligatoria.',
            'form.subcategory_id.exists' => 'La subcategoría seleccionada no existe.',
            'form.name.required' => 'El nombre es obligatorio.',
            'form.name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'form.description.required' => 'La descripción es obligatoria.',
            'form.sku_base.required' => 'El SKU base es obligatorio.',
            'form.sku_base.max' => 'El SKU base no puede tener más de 50 caracteres.',
            'form.sku_base.unique' => 'Este SKU base ya está en uso.',
            'form.unit_type.required' => 'El tipo de unidad es obligatorio.',
            'form.unit_type.in' => 'El tipo de unidad debe ser kg, ton, saco, caja o unidad.',
            'form.image.required' => 'La imagen es obligatoria.',
            'form.image.image' => 'El archivo debe ser una imagen.',
            'form.image.max' => 'La imagen no puede ser mayor a 2MB.',
            'form.seasonal_info.string' => 'La información estacional debe ser texto.',
            'form.is_active.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if ($propertyName === 'form.category_id') {
            $this->form['subcategory_id'] = '';
            // Las subcategorías se cargarán automáticamente en el render()
        }
    }

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $personId = Auth::id();
        $this->products = Product::where('person_id', $personId)
            ->where('is_active', true)
            ->with(['category', 'subcategory'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function openModal($productId = null)
    {
        $this->resetForm();
        $this->changeImage = false;
        if ($productId) {
            $this->editingProduct = Product::where('id', $productId)->where('person_id', Auth::id())->firstOrFail();
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
        $formImageRule = is_string($this->form['image']) ? 'nullable|string' : 'nullable|image|max:2048';
        $productId = $this->editingProduct ? $this->editingProduct->id : null;

        $validationRules = [
            'form.category_id' => 'required|exists:product_categories,id',
            'form.subcategory_id' => 'required|exists:product_subcategories,id',
            'form.name' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.unit_type' => 'required|in:kg,ton,saco,caja,unidad',
            'form.image' => $formImageRule,
            'form.seasonal_info' => 'nullable|string',
            'form.is_active' => 'boolean',
        ];

        // Agregar regla de SKU con validación condicional
        if ($productId) {
            $validationRules['form.sku_base'] = "required|string|max:50|unique:products,sku_base,{$productId}";
        } else {
            $validationRules['form.sku_base'] = 'required|string|max:50|unique:products,sku_base';
        }

        $this->validate($validationRules);

        try {
            DB::beginTransaction();
            
            $imagePath = null;

            // Manejar la imagen si se proporciona una nueva
            if ($this->form['image'] instanceof TemporaryUploadedFile) {
                $imagePath = $this->storeImage($this->form['image']);
                Log::info('Nueva imagen almacenada', ['path' => $imagePath]);
            } elseif ($this->editingProduct) {
                // Mantener la imagen existente si no se proporciona una nueva
                $imagePath = $this->editingProduct->image;
                Log::info('Manteniendo imagen existente', ['path' => $imagePath]);
            }

            $productData = [
                'category_id' => $this->form['category_id'],
                'subcategory_id' => $this->form['subcategory_id'],
                'name' => $this->form['name'],
                'description' => $this->form['description'],
                'sku_base' => $this->form['sku_base'],
                'unit_type' => $this->form['unit_type'],
                'seasonal_info' => $this->form['seasonal_info'],
                'is_active' => $this->form['is_active'],
            ];

            // Solo actualizar la imagen si tenemos una
            if ($imagePath !== null) {
                $productData['image'] = $imagePath;
            }

            if ($this->editingProduct) {
                // Actualizar producto existente
                $this->editingProduct->update($productData);
                $this->dispatch('product-updated');
                Log::info('Producto actualizado', ['id' => $this->editingProduct->id, 'image' => $imagePath]);
            } else {
                // Crear nuevo producto
                $productData['person_id'] = Auth::id();
                $product = Product::create($productData);
                $this->dispatch('product-added');
                Log::info('Producto creado', ['id' => $product->id, 'image' => $imagePath]);
            }

            DB::commit();
            $this->closeModal();
            $this->loadProducts();
            session()->flash('success', $this->editingProduct ? 'Producto actualizado correctamente.' : 'Producto creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
        
        // Cargar subcategorías solo si hay una categoría seleccionada
        $subcategories = [];
        if (!empty($this->form['category_id'])) {
            $subcategories = ProductSubcategory::where('category_id', $this->form['category_id'])
                ->where('is_active', true)
                ->get();
        }

        return view('livewire.seller.products-crud', [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'products' => $this->products,
        ]);
    }
}
