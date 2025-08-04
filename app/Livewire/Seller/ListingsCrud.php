<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\ProductListing;
use App\Models\Product;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Models\ProductPresentation;

class ListingsCrud extends Component
{
    use WithFileUploads;

    public $listings;
    public $showModal = false;
    public $editingListing = null;
    public $temporaryImages = []; // Para almacenar las imágenes temporales
    public $form = [
        'product_id' => '',
        'title' => '',
        'description' => '',
        'unit_price' => '',
        'quality_grade' => '',
        'harvest_date' => '',
        'state_id' => '',
        'municipality_id' => '',
        'parish_id' => '',
        'status' => 'pending',
        'images' => [], // Array para guardar las rutas de las imágenes
        'product_presentation_id' => '',
        'presentation_quantity' => 1,
    ];

    public $selectedImages = []; // Array para las imágenes seleccionadas

    public $states;
    public $municipalities = [];
    public $parishes = [];

    protected function rules()
    {
        return [
            'form.title' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.unit_price' => 'required|numeric|min:0',
            'form.quality_grade' => 'required|in:premium,standard,economic',
            'form.harvest_date' => 'required|date',
            'form.state_id' => 'required|exists:states,id',
            'form.municipality_id' => 'required|exists:municipalities,id',
            'form.parish_id' => 'required|exists:parishes,id',
            'form.product_id' => 'required|exists:products,id',
            'form.status' => 'required|in:pending,active,sold_out,inactive',
            'form.product_presentation_id' => 'required|exists:product_presentations,id',
            'form.presentation_quantity' => 'required|numeric|min:0.01',
            'temporaryImages.*' => 'nullable|image|max:2048', // Validación para las imágenes
        ];
    }

    public function getSelectedPresentationProperty()
    {
        if (!empty($this->form['product_presentation_id'])) {
            return ProductPresentation::find($this->form['product_presentation_id']);
        }
        return null;
    }

    public function updatedFormProductPresentationId($value)
    {
        // Log para debug
        Log::info('Presentación seleccionada:', [
            'id' => $value,
            'presentation' => $this->selectedPresentation
        ]);
    }

    public function mount()
    {
        $this->loadListings();
        $this->states = State::where('country_id', 296)->get();
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
        $this->resetForm();
        
        if ($listingId) {
            $this->editingListing = ProductListing::with([
                'state', 
                'municipality', 
                'parish', 
                'product',
                'productPresentation'
            ])->findOrFail($listingId);
            
            $this->form = [
                'product_id' => $this->editingListing->product_id,
                'title' => $this->editingListing->title,
                'description' => $this->editingListing->description,
                'unit_price' => $this->editingListing->unit_price,
                'quality_grade' => $this->editingListing->quality_grade,
                'harvest_date' => $this->editingListing->harvest_date ? $this->editingListing->harvest_date->format('Y-m-d') : '',
                'state_id' => $this->editingListing->state_id,
                'municipality_id' => $this->editingListing->municipality_id,
                'parish_id' => $this->editingListing->parish_id,
                'status' => $this->editingListing->status,
                'images' => $this->editingListing->images ?? [], // Cargar las imágenes existentes
                'product_presentation_id' => $this->editingListing->product_presentation_id,
                'presentation_quantity' => $this->editingListing->presentation_quantity,
            ];

            // Cargar las imágenes existentes en selectedImages para mostrarlas en la vista
            if (!empty($this->editingListing->images)) {
                foreach ($this->editingListing->images as $imagePath) {
                    $this->selectedImages[] = [
                        'id' => uniqid(),
                        'name' => basename($imagePath),
                        'path' => $imagePath,
                        'preview' => asset('storage/' . $imagePath)
                    ];
                }
            }
            
            if ($this->form['state_id']) {
                $this->municipalities = Municipality::where('state_id', $this->form['state_id'])->get();
            }
            
            if ($this->form['municipality_id']) {
                $this->parishes = Parish::where('municipality_id', $this->form['municipality_id'])->get();
            }
        } else {
            $this->editingListing = null;
        }
        
        $this->showModal = true;
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
            'quality_grade' => '',
            'harvest_date' => '',
            'state_id' => '',
            'municipality_id' => '',
            'parish_id' => '',
            'status' => 'pending',
            'images' => [], // Asegurarnos de resetear también las imágenes
            'product_presentation_id' => '',
            'presentation_quantity' => 1,
        ];
        $this->selectedImages = [];
        $this->temporaryImages = [];
    }

    public function saveListing()
    {
        Log::info('Iniciando saveListing()', [
            'form_data' => $this->form,
            'editing' => $this->editingListing ? $this->editingListing->id : null,
            'images_count' => count($this->form['images'] ?? [])
        ]);

        try {
            $this->validate();
            Log::info('Validación exitosa');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en saveListing', [
                'errors' => $e->errors(),
                'form_data' => $this->form
            ]);
            $this->dispatch('error', 'Error de validación: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())));
            return;
        }

        try {
            $person = Auth::user();
            if (!$person) {
                Log::error('Usuario no autenticado en saveListing');
                $this->dispatch('error', 'No tienes un perfil de vendedor asociado.');
                return;
            }

            Log::info('Usuario autenticado', [
                'user_id' => $person->id,
                'user_type' => get_class($person)
            ]);

            $listingData = array_merge($this->form, [
                'person_id' => $person->id,
                'images' => $this->form['images'] ?? [], // Asegurarnos de que las imágenes se guarden
            ]);

            Log::info('Datos preparados para guardar', [
                'listing_data' => $listingData,
                'images_array' => $listingData['images']
            ]);

            if ($this->editingListing) {
                Log::info('Actualizando listing existente', ['listing_id' => $this->editingListing->id]);
                
                // Si estamos editando, actualizar las imágenes
                $this->editingListing->update($listingData);
                
                Log::info('Listing actualizado exitosamente');
                $this->dispatch('listing-updated');
            } else {
                Log::info('Creando nuevo listing');
                
                // Si es nuevo, crear con las imágenes
                $newListing = ProductListing::create($listingData);
                
                Log::info('Listing creado exitosamente', [
                    'listing_id' => $newListing->id,
                    'images_saved' => $newListing->images
                ]);
                $this->dispatch('listing-added');
            }
            
            $this->closeModal();
            $this->loadListings();

            Log::info('saveListing completado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error detallado al guardar listing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'form_data' => $this->form,
                'user_id' => Auth::id(),
                'sql_error' => $e instanceof \Illuminate\Database\QueryException ? $e->errorInfo : null,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            $this->dispatch('error', 'Error al guardar la publicación: ' . $e->getMessage());
        }
    }

    public function testAddImage()
    {
        // Método simple de prueba
        $this->dispatch('success', 'Botón de agregar imagen funcionando correctamente');
    }

    /**
     * Maneja la selección de una imagen del input
     */
    public function handleImageSelected($fileData)
    {
        try {
            if (!$fileData) {
                throw new \Exception('No se recibieron datos de archivo');
            }

            // Validar tamaño del archivo antes de procesarlo
            if (isset($fileData['size']) && $fileData['size'] > 10 * 1024 * 1024) { // 10MB
                throw new \Exception('El archivo es demasiado grande. Máximo 10MB permitido.');
            }

            // Determinar el disco a usar basado en el entorno
            $disk = app()->environment('production') ? 'r2' : 'public';
            
            // Generar un nombre único para el archivo
            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            // SIEMPRE usar 'listings/' para las publicaciones
            $path = 'listings/' . $fileName;

            Log::info('Guardando imagen de listing', [
                'disk' => $disk,
                'path' => $path,
                'fileName' => $fileName,
                'environment' => app()->environment(),
                'file_size' => $fileData['size'] ?? 'unknown'
            ]);

            // Decodificar la imagen base64 y guardarla
            if (!isset($fileData['preview']) || empty($fileData['preview'])) {
                throw new \Exception('No se encontraron datos de imagen válidos');
            }

            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $fileData['preview']));
            
            if ($imageData === false) {
                throw new \Exception('Error al decodificar la imagen base64');
            }

            // Verificar que los datos decodificados no estén vacíos
            if (empty($imageData)) {
                throw new \Exception('Los datos de imagen están vacíos después de decodificar');
            }
            
            if ($disk === 'r2') {
                // Para R2 en producción
                Log::info('Intentando subir a R2', ['path' => $path, 'size' => strlen($imageData)]);
                
                $stored = Storage::disk($disk)->put($path, $imageData, 'public');
                
                if (!$stored) {
                    throw new \Exception('Falló la subida a R2 - put() retornó false');
                }
                
                // Verificar si el archivo se guardó correctamente
                $exists = Storage::disk($disk)->exists($path);
                Log::info('Verificación de almacenamiento en R2 - Listing', [
                    'path' => $path,
                    'exists' => $exists,
                    'stored' => $stored
                ]);

                if (!$exists) {
                    throw new \Exception('El archivo no se guardó correctamente en R2 - verificación falló');
                }
            } else {
                // Para almacenamiento local en desarrollo
                $stored = Storage::disk($disk)->put($path, $imageData);
                
                if (!$stored) {
                    throw new \Exception('Falló la subida local - put() retornó false');
                }
                
                Log::info('Imagen de listing almacenada localmente', [
                    'path_resultado' => $path,
                    'disk' => $disk,
                    'size' => strlen($imageData)
                ]);
            }

            // Agregar la imagen al array de imágenes seleccionadas
            $this->selectedImages[] = [
                'id' => uniqid(),
                'name' => $fileName,
                'path' => $path,
                'preview' => $fileData['preview']
            ];

            // Agregar la ruta al array de imágenes del formulario
            if (!is_array($this->form['images'])) {
                $this->form['images'] = [];
            }
            $this->form['images'][] = $path;

            $this->dispatch('success', 'Imagen agregada correctamente');
            
        } catch (\Exception $e) {
            Log::error('Error detallado al guardar imagen de listing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'fileData' => [
                    'name' => $fileData['name'] ?? 'unknown',
                    'size' => $fileData['size'] ?? 'unknown',
                    'has_preview' => isset($fileData['preview']) && !empty($fileData['preview']),
                    'preview_length' => isset($fileData['preview']) ? strlen($fileData['preview']) : 0
                ],
                'environment' => app()->environment(),
                'php_limits' => [
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'memory_limit' => ini_get('memory_limit')
                ]
            ]);
            $this->dispatch('error', 'Error al guardar la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Agrega una imagen cuando se hace clic en el botón +
     */
    public function addImage()
    {
        // Agregar una imagen de prueba
        $this->selectedImages[] = [
            'id' => uniqid(),
            'name' => 'imagen_' . count($this->selectedImages) . '.jpg',
            'preview' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+Cjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjc3NDhCIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+VmlzdGEgcHJldmlhPC90ZXh0Pgo8L3N2Zz4K'
        ];
        
        $this->dispatch('success', 'Imagen agregada con el botón +');
    }

    /**
     * Elimina una imagen
     */
    public function removeImage($index)
    {
        try {
            if (isset($this->selectedImages[$index])) {
                $image = $this->selectedImages[$index];
                
                // Si la imagen ya existe en el servidor (tiene path), eliminarla
                if (isset($image['path'])) {
                    $disk = app()->environment('production') ? 'r2' : 'public';
                    if (Storage::disk($disk)->exists($image['path'])) {
                        Storage::disk($disk)->delete($image['path']);
                    }
                    
                    // Eliminar también del array de imágenes del formulario
                    if (is_array($this->form['images'])) {
                        $key = array_search($image['path'], $this->form['images']);
                        if ($key !== false) {
                            unset($this->form['images'][$key]);
                            $this->form['images'] = array_values($this->form['images']);
                        }
                    }
                }

                // Eliminar de selectedImages
                unset($this->selectedImages[$index]);
                $this->selectedImages = array_values($this->selectedImages);

                $this->dispatch('success', 'Imagen eliminada correctamente');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('error', 'Error al eliminar la imagen');
        }
    }

    public function confirmDelete($listingId)
    {
        $this->js("
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteListing', { id: {$listingId} });
                }
            })
        ");
    }

    #[On('deleteListing')]
    public function deleteListing($id)
    {
        try {
            if (!$id) {
                throw new \Exception('ID de publicación no proporcionado');
            }

            $listing = ProductListing::where('person_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();

            // Eliminar las imágenes físicas
            if (!empty($listing->images)) {
                foreach ($listing->images as $image) {
                    $disk = app()->environment('production') ? 'r2' : 'public';
                    if (Storage::disk($disk)->exists($image)) {
                        Storage::disk($disk)->delete($image);
                    }
                }
            }

            // Eliminar el registro
            $listing->delete();

            // Recargar los listings después de eliminar
            $this->loadListings();

            $this->js("
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Publicación eliminada correctamente',
                    confirmButtonColor: '#3b82f6'
                }).then(() => {
                    window.location.reload();
                });
            ");

        } catch (\Exception $e) {
            Log::error('Error al eliminar publicación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->js("
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar la publicación',
                    confirmButtonColor: '#3b82f6'
                });
            ");
        }
    }

    public function render()
    {
        $products = Product::where(function($query) {
            $query->where('person_id', Auth::id())
                  ->orWhere('is_universal', true);
        })->get();

        $presentations = ProductPresentation::where('is_active', true)->get();
        
        return view('livewire.seller.listings-crud', [
            'listings' => $this->listings,
            'states' => $this->states,
            'municipalities' => $this->municipalities,
            'parishes' => $this->parishes,
            'products' => $products,
            'presentations' => $presentations,
            'selectedPresentation' => $this->selectedPresentation,
        ]);
    }
}

