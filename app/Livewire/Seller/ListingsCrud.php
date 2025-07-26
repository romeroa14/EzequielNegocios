<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\ProductListing;
use App\Models\Product;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ListingsCrud extends Component
{
    use WithFileUploads;

    public $listings;
    public $showModal = false;
    public $editingListing = null;
    public $listingIdToDelete = null;
    public $form = [
        'product_id' => '',
        'title' => '',
        'description' => '',
        'unit_price' => '',
        'quantity_available' => '',
        'quality_grade' => '',
        'harvest_date' => '',
        'images' => [], // Solo imágenes nuevas
        'state_id' => '',
        'municipality_id' => '',
        'parish_id' => '',
        'status' => 'pending',
    ];

    public $newImages = [];
    public $existingImages = []; // Para imágenes existentes

    public $states;
    public $municipalities = [];
    public $parishes = [];

    protected function rules()
    {
        return [
            'form.title' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.unit_price' => 'required|numeric|min:0',
            'form.quantity_available' => 'required|integer|min:1',
            'form.quality_grade' => 'required|in:premium,standard,economic',
            'form.harvest_date' => 'required|date',
            'form.state_id' => 'required|exists:states,id',
            'form.municipality_id' => 'required|exists:municipalities,id',
            'form.parish_id' => 'required|exists:parishes,id',
            'form.images.*' => 'image|max:2048',
            'newImages.*' => 'image|max:2048',
            'form.product_id' => 'required|exists:products,id',
            'form.status' => 'required|in:pending,active,sold_out,inactive',
        ];
    }

    public function mount()
    {
        $this->loadListings();
        $this->states = State::where('country_id', 296)->get(); // 296 es el ID de Venezuela
        
        // Si hay un estado seleccionado, cargar sus municipios
        if ($this->form['state_id']) {
            $this->municipalities = Municipality::where('state_id', $this->form['state_id'])->get();
        }
        
        // Si hay un municipio seleccionado, cargar sus parroquias
        if ($this->form['municipality_id']) {
            $this->parishes = Parish::where('municipality_id', $this->form['municipality_id'])->get();
        }
    }

    public function updatedFormStateId($value)
    {
        if ($value) {
            $this->municipalities = Municipality::where('state_id', $value)->get();
            $this->form['municipality_id'] = null;
            $this->form['parish_id'] = null;
            $this->parishes = [];
        } else {
            $this->municipalities = [];
            $this->parishes = [];
        }
    }

    public function updatedFormMunicipalityId($value)
    {
        if ($value) {
            $this->parishes = Parish::where('municipality_id', $value)->get();
            $this->form['parish_id'] = null;
        } else {
            $this->parishes = [];
        }
    }

    public function loadListings()
    {
        $personId = Auth::id();
        $this->listings = ProductListing::with('product')
            ->where('person_id', $personId)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function openModal($listingId = null)
    {
        try {
            Log::info('Iniciando openModal', ['listingId' => $listingId]);
            
            $this->resetForm();
            
            if ($listingId) {
                Log::info('Editando listing existente', ['listingId' => $listingId]);
                
                // Buscar el listing con todas las relaciones
                $this->editingListing = ProductListing::with([
                    'state', 
                    'municipality', 
                    'parish', 
                    'product'
                ])->findOrFail($listingId);
                
                Log::info('Listing encontrado', [
                    'listing_id' => $this->editingListing->id,
                    'state_id' => $this->editingListing->state_id,
                    'municipality_id' => $this->editingListing->municipality_id,
                    'parish_id' => $this->editingListing->parish_id,
                    'state_name' => $this->editingListing->state?->name,
                    'municipality_name' => $this->editingListing->municipality?->name,
                    'parish_name' => $this->editingListing->parish?->name,
                ]);
                
                $this->form = [
                    'product_id' => $this->editingListing->product_id,
                    'title' => $this->editingListing->title,
                    'description' => $this->editingListing->description,
                    'unit_price' => $this->editingListing->unit_price,
                    'quantity_available' => $this->editingListing->quantity_available,
                    'quality_grade' => $this->editingListing->quality_grade,
                    'harvest_date' => $this->editingListing->harvest_date ? $this->editingListing->harvest_date->format('Y-m-d') : '',
                    'images' => [], // Inicialmente vacío para nuevas imágenes
                    'state_id' => $this->editingListing->state_id,
                    'municipality_id' => $this->editingListing->municipality_id,
                    'parish_id' => $this->editingListing->parish_id,
                    'status' => $this->editingListing->status,
                ];
                
                // Cargar imágenes existentes en una propiedad separada
                $this->existingImages = $this->editingListing->images ?? [];
                
                Log::info('Formulario cargado', [
                    'form_state_id' => $this->form['state_id'],
                    'form_municipality_id' => $this->form['municipality_id'],
                    'form_parish_id' => $this->form['parish_id'],
                ]);
                
                // Cargar las dependencias basándose en los valores del formulario
                if ($this->form['state_id']) {
                    $this->municipalities = Municipality::where('state_id', $this->form['state_id'])->get();
                  
                } else {
                    Log::warning('No hay state_id para cargar municipios');
                }
                
                if ($this->form['municipality_id']) {
                    $this->parishes = Parish::where('municipality_id', $this->form['municipality_id'])->get();
                    
                } else {
                    Log::warning('No hay municipality_id para cargar parroquias');
                }
                
            } else {
                Log::info('Creando nuevo listing');
                $this->editingListing = null;
            }
            
            $this->showModal = true;
            Log::info('Modal abierto exitosamente');
            
        } catch (\Exception $e) {
            Log::error('Error en openModal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'listingId' => $listingId
            ]);
            
            $this->dispatch('error', 'Error al abrir el formulario: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingListing = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->form = [
            'product_id' => '',
            'title' => '',
            'description' => '',
            'unit_price' => '',
            'quantity_available' => '',
            'quality_grade' => '',
            'harvest_date' => '',
            'images' => [], // Solo imágenes nuevas
            'state_id' => '',
            'municipality_id' => '',
            'parish_id' => '',
            'status' => 'pending',
        ];
        $this->newImages = [];
        $this->existingImages = []; // Limpiar imágenes existentes
    }

    public function saveListing()
    {
        try {
            Log::info('Iniciando saveListing', [
                'editingListing' => $this->editingListing ? $this->editingListing->id : null,
                'form_data' => $this->form
            ]);
            
            // Validación básica sin imágenes
            $this->validate([
                'form.product_id' => 'required|exists:products,id',
                'form.title' => 'required|string|max:255',
                'form.description' => 'required|string',
                'form.unit_price' => 'required|numeric|min:0',
                'form.quantity_available' => 'required|integer|min:0',
                'form.quality_grade' => 'required|in:premium,standard,economic',
                'form.harvest_date' => 'required|date',
                'form.state_id' => 'required|exists:states,id',
                'form.municipality_id' => 'required|exists:municipalities,id',
                'form.parish_id' => 'required|exists:parishes,id',
                'form.status' => 'required|in:active,pending,sold_out,inactive',
                'newImages.*' => 'image|max:2048',
            ], [
                'form.product_id.required' => 'El producto es obligatorio.',
                'form.product_id.exists' => 'El producto seleccionado no existe.',
                'form.title.required' => 'El título es obligatorio.',
                'form.title.max' => 'El título no puede tener más de 255 caracteres.',
                'form.description.required' => 'La descripción es obligatoria.',
                'form.description.string' => 'La descripción debe ser una cadena de texto.',
                'form.unit_price.required' => 'El precio unitario es obligatorio.',
                'form.unit_price.numeric' => 'El precio unitario debe ser un número.',
                'form.unit_price.min' => 'El precio unitario debe ser mayor o igual a 0.',
                'form.quantity_available.required' => 'La cantidad disponible es obligatoria.',
                'form.quantity_available.integer' => 'La cantidad disponible debe ser un número entero.',
                'form.quantity_available.min' => 'La cantidad disponible debe ser mayor o igual a 0.',
                'form.quality_grade.required' => 'La calidad es obligatoria.',
                'form.quality_grade.in' => 'La calidad debe ser premium, standard o economic.',
                'form.harvest_date.required' => 'La fecha de cosecha es obligatoria.',
                'form.harvest_date.date' => 'La fecha de cosecha debe ser una fecha válida.',
                'form.state_id.required' => 'El estado es obligatorio.',
                'form.state_id.exists' => 'El estado seleccionado no existe.',
                'form.municipality_id.required' => 'El municipio es obligatorio.',
                'form.municipality_id.exists' => 'El municipio seleccionado no existe.',
                'form.parish_id.required' => 'La parroquia es obligatoria.',
                'form.parish_id.exists' => 'La parroquia seleccionada no existe.',
                'newImages.*.image' => 'Los archivos deben ser imágenes.',
                'newImages.*.max' => 'Las imágenes no pueden ser mayores a 2MB.',
            ]);
            
            Log::info('Validación pasada exitosamente');

            $person = Auth::user();
            if (!$person) {
                Log::error('Usuario no autenticado');
                $this->dispatch('error', 'No tienes un perfil de vendedor asociado. Contacta al administrador.');
                return;
            }
            $personId = $person->id;
            
            Log::info('Usuario autenticado', ['person_id' => $personId]);

            // Procesar imágenes
            $imagePaths = [];
            
            // Si estamos editando, mantener las imágenes existentes
            if ($this->editingListing && !empty($this->existingImages)) {
                $imagePaths = $this->existingImages;
                Log::info('Manteniendo imágenes existentes', [
                    'existing_images_count' => count($imagePaths),
                    'existing_images' => $imagePaths
                ]);
            }
            
            // Agregar la imagen del producto si existe y no está ya incluida
            $product = Product::find($this->form['product_id']);
            if ($product && $product->image && !in_array($product->image, $imagePaths)) {
                $imagePaths[] = $product->image;
                Log::info('Agregada imagen del producto', ['product_image' => $product->image]);
            }

            // Procesar imágenes adicionales subidas (solo las que son TemporaryUploadedFile)
            if (!empty($this->form['images'])) {
                Log::info('Procesando imágenes nuevas', ['new_images_count' => count($this->form['images'])]);
                foreach ($this->form['images'] as $index => $image) {
                    if ($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        $imagePath = $this->storeListingImage($image);
                        if ($imagePath) {
                            $imagePaths[] = $imagePath;
                            Log::info('Imagen procesada exitosamente', [
                                'index' => $index,
                                'path' => $imagePath
                            ]);
                        } else {
                            Log::error('Error procesando imagen', ['index' => $index]);
                        }
                    } else {
                        Log::info('Imagen ya procesada (string)', [
                            'index' => $index,
                            'path' => $image
                        ]);
                        // Si ya es un string (ruta guardada), agregarlo directamente
                        if (!in_array($image, $imagePaths)) {
                            $imagePaths[] = $image;
                        }
                    }
                }
            }

            $listingData = array_merge($this->form, [
                'person_id' => $personId,
                'images' => $imagePaths,
            ]);
            
            Log::info('Datos del listing preparados', [
                'listing_data' => $listingData,
                'total_images' => count($imagePaths)
            ]);

            if ($this->editingListing) {
                Log::info('Actualizando listing existente', ['listing_id' => $this->editingListing->id]);
                $this->editingListing->update($listingData);
                $this->dispatch('listing-updated');
                Log::info('Listing actualizado exitosamente');
            } else {
                Log::info('Creando nuevo listing');
                ProductListing::create($listingData);
                $this->dispatch('listing-added');
                Log::info('Listing creado exitosamente');
            }
            
            $this->closeModal();
            $this->loadListings();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en saveListing', [
                'errors' => $e->errors(),
                'form_data' => $this->form
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error en saveListing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $this->form
            ]);
            $this->dispatch('error', 'Hubo un error al guardar la publicación: ' . $e->getMessage());
        }
    }

    /**
     * Almacena una imagen de listing
     */
    private function storeListingImage($image)
    {
        try {
            // Determinar el disco a usar basado en el entorno
            $disk = app()->environment('production') ? 'r2' : 'public';
            
            // Generar un nombre único para el archivo
            $extension = $image->getClientOriginalExtension();
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $path = 'listings/' . $fileName;

            Log::info('Guardando imagen de listing', [
                'disk' => $disk,
                'path' => $path,
                'fileName' => $fileName
            ]);

            // Almacenar el archivo
            if ($disk === 'r2') {
                // Para R2 en producción
                $imagePath = $image->storePublicly($path, ['disk' => $disk]);
                
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
                $imagePath = $image->storeAs('listings', $fileName, 'public');
                Log::info('Imagen de listing almacenada localmente', [
                    'path_resultado' => $imagePath
                ]);
            }

            return $imagePath;
        } catch (\Exception $e) {
            Log::error('Error al almacenar imagen de listing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Elimina una imagen de la vista previa
     */
    public function removeImage($index)
    {
        if (isset($this->form['images'][$index])) {
            unset($this->form['images'][$index]);
            $this->form['images'] = array_values($this->form['images']);
        }
    }

    /**
     * Elimina una imagen existente del listing
     */
    public function removeExistingImage($index)
    {
        if (isset($this->existingImages[$index])) {
            $imagePath = $this->existingImages[$index];
            
            // Eliminar físicamente el archivo
            $disk = app()->environment('production') ? 'r2' : 'public';
            if (Storage::disk($disk)->exists($imagePath)) {
                Storage::disk($disk)->delete($imagePath);
            }
            
            // Remover del array
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);
            
            Log::info('Imagen existente eliminada', [
                'index' => $index,
                'path' => $imagePath,
                'remaining_count' => count($this->existingImages)
            ]);
        }
    }

    /**
     * Maneja la adición de nuevas imágenes
     */
    public function updatedNewImages()
    {
        try {
            Log::info('updatedNewImages llamado', [
                'newImages_count' => count($this->newImages),
                'current_form_images_count' => count($this->form['images'])
            ]);
            
            if (!empty($this->newImages)) {
                foreach ($this->newImages as $index => $image) {
                    Log::info('Procesando nueva imagen', [
                        'index' => $index,
                        'image_type' => get_class($image),
                        'is_temporary' => $image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                    ]);
                    
                    if ($image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        // Validar la imagen
                        $this->validate([
                            'newImages.*' => 'image|max:2048',
                        ], [
                            'newImages.*.image' => 'Los archivos deben ser imágenes.',
                            'newImages.*.max' => 'Las imágenes no pueden ser mayores a 2MB.',
                        ]);
                        
                        // Agregar a la pila de imágenes
                        $this->form['images'][] = $image;
                        Log::info('Imagen agregada a la pila', [
                            'index' => $index,
                            'new_total_count' => count($this->form['images'])
                        ]);
                    } else {
                        Log::warning('Imagen no es del tipo esperado', [
                            'index' => $index,
                            'actual_type' => get_class($image)
                        ]);
                    }
                }
                
                // Limpiar el input de nuevas imágenes
                $this->newImages = [];
                Log::info('Input de nuevas imágenes limpiado');
            } else {
                Log::info('No hay nuevas imágenes para procesar');
            }
            
        } catch (\Exception $e) {
            Log::error('Error en updatedNewImages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('error', 'Error al procesar imágenes: ' . $e->getMessage());
        }
    }

    /**
     * Limpia todas las imágenes nuevas
     */
    public function clearNewImages()
    {
        $this->form['images'] = [];
        $this->newImages = [];
    }

    public function confirmDelete($listingId)
    {
        $this->listingIdToDelete = $listingId;
        $this->dispatch('show-delete-confirmation');
    }

    public function deleteListing($listingId)
    {
        $listingId = $listingId ?? $this->listingIdToDelete;
    
    if (!$listingId) {
        $this->dispatch('error', 'No se encontró la publicación a eliminar.');
        return;
    }

    try {
        $listing = ProductListing::findOrFail($listingId);
        $listing->delete();
        $this->loadListings();
        $this->dispatch('listing-deleted');
    } catch (\Exception $e) {
        $this->dispatch('error', 'Hubo un error al eliminar la publicación.');
    }
    }

    public function render()
    {
        $products = Product::where(function($query) {
            $query->where('person_id', Auth::id())
                  ->orWhere('is_universal', true);
        })->get();
        
        return view('livewire.seller.listings-crud', [
            'listings' => $this->listings,
            'states' => $this->states,
            'municipalities' => $this->municipalities,
            'parishes' => $this->parishes,
            'products' => $products,
        ]);
    }
}

