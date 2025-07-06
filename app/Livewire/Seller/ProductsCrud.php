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
                'r2_config' => [
                    'url' => config('filesystems.disks.r2.url'),
                    'bucket' => config('filesystems.disks.r2.bucket'),
                    'endpoint' => config('filesystems.disks.r2.endpoint'),
                ]
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
                // Para R2 en producción
                $path = $image->storePublicly($path, ['disk' => $disk]);
                Log::info('Imagen almacenada en R2', [
                    'path_resultado' => $path,
                    'disk_config' => config('filesystems.disks.r2')
                ]);
            } else {
                // Para almacenamiento local en desarrollo
                $path = $image->storeAs('products', $fileName, 'public');
                Log::info('Imagen almacenada localmente', [
                    'path_resultado' => $path
                ]);
            }
            
            // Generar la URL según el disco
            $url = $disk === 'r2' 
                ? rtrim(config('filesystems.disks.r2.url'), '/') . '/' . ltrim($path, '/')
                : url('storage/' . $path);
            
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
