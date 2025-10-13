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
use Illuminate\Support\Facades\DB;
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
        'currency_type' => 'USD',
        'quality_grade' => '',
        'is_harvesting' => false,
        'harvest_date' => '',
        'selling_location_type' => 'farm_gate',
        'market_id' => '',
        'state_id' => '',
        'municipality_id' => '',
        'parish_id' => '',
        'status' => 'pending',
        'images' => [], // Array para guardar las rutas de las imágenes
        'product_presentation_id' => '',
        'presentation_quantity' => 1,
    ];

    public $selectedImages = []; // Array para las imágenes seleccionadas
    public $listingIdToDelete = null; // Para almacenar el ID de la publicación a eliminar

    public $states;
    public $municipalities = [];
    public $parishes = [];

    protected function rules()
    {
        return [
            'form.title' => 'required|string|max:255',
            'form.description' => 'required|string',
            'form.unit_price' => 'required|numeric|min:0',
            'form.currency_type' => 'required|in:USD,VES',
            'form.quality_grade' => 'required|in:premium,standard,economic',
            'form.harvest_date' => 'required_if:form.is_harvesting,true|nullable|date',
            'form.selling_location_type' => 'required|in:farm_gate,wholesale_market',
            'form.market_id' => 'required_if:form.selling_location_type,wholesale_market|nullable|exists:markets,id',
            'form.state_id' => 'required_if:form.selling_location_type,farm_gate|nullable|exists:states,id',
            'form.municipality_id' => 'required_if:form.selling_location_type,farm_gate|nullable|exists:municipalities,id',
            'form.parish_id' => 'required_if:form.selling_location_type,farm_gate|nullable|exists:parishes,id',
            'form.product_id' => [
                'required',
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    // Verificar que el producto pertenece al usuario o es universal
                    $product = \App\Models\Product::find($value);
                    if (!$product) {
                        $fail('El producto seleccionado no existe.');
                        return;
                    }
                    
                    $userId = Auth::id();
                    if ($product->person_id !== $userId && !$product->is_universal) {
                        $fail('No tienes permiso para usar este producto.');
                        return;
                    }
                    
                    Log::info('Validación de producto exitosa', [
                        'product_id' => $value,
                        'product_name' => $product->name,
                        'product_person_id' => $product->person_id,
                        'is_universal' => $product->is_universal,
                        'user_id' => $userId
                    ]);
                }
            ],
            'form.status' => 'required|in:pending,active,sold_out,inactive',
            'form.product_presentation_id' => 'required|exists:product_presentations,id',
            'form.presentation_quantity' => 'required|numeric|min:0.01',
            'temporaryImages.*' => 'nullable|image|max:2048', // Validación para las imágenes
        ];
    }

    protected function messages()
    {
        return [
            'form.title.required' => 'El título es obligatorio.',
            'form.title.string' => 'El título debe ser texto.',
            'form.title.max' => 'El título no puede tener más de 255 caracteres.',
            'form.description.required' => 'La descripción es obligatoria.',
            'form.description.string' => 'La descripción debe ser texto.',
            'form.unit_price.required' => 'El precio es obligatorio.',
            'form.unit_price.numeric' => 'El precio debe ser un número.',
            'form.unit_price.min' => 'El precio debe ser mayor a 0.',
            'form.currency_type.required' => 'El tipo de moneda es obligatorio.',
            'form.currency_type.in' => 'El tipo de moneda debe ser USD o VES.',
            'form.quality_grade.required' => 'La calidad es obligatoria.',
            'form.quality_grade.in' => 'La calidad debe ser premium, standard o economic.',
            'form.harvest_date.required_if' => 'La fecha de cosecha es obligatoria cuando está en cosecha.',
            'form.harvest_date.date' => 'La fecha de cosecha debe ser una fecha válida.',
            'form.selling_location_type.required' => 'Debe indicar dónde vende.',
            'form.selling_location_type.in' => 'Tipo de venta inválido.',
            'form.market_id.required_if' => 'Debe seleccionar un mercado mayorista.',
            'form.market_id.exists' => 'El mercado seleccionado no es válido.',
            'form.state_id.required_if' => 'El estado es obligatorio para puerta de finca.',
            'form.state_id.exists' => 'El estado seleccionado no es válido.',
            'form.municipality_id.required_if' => 'El municipio es obligatorio para puerta de finca.',
            'form.municipality_id.exists' => 'El municipio seleccionado no es válido.',
            'form.parish_id.required_if' => 'La parroquia es obligatoria para puerta de finca.',
            'form.parish_id.exists' => 'La parroquia seleccionada no es válida.',
            'form.product_id.required' => 'El producto es obligatorio.',
            'form.product_id.exists' => 'El producto seleccionado no es válido.',
            'form.status.required' => 'El estatus es obligatorio.',
            'form.status.in' => 'El estatus debe ser pending, active, sold_out o inactive.',
            'form.product_presentation_id.required' => 'La presentación es obligatoria.',
            'form.product_presentation_id.exists' => 'La presentación seleccionada no es válida.',
            'form.presentation_quantity.required' => 'La cantidad es obligatoria.',
            'form.presentation_quantity.numeric' => 'La cantidad debe ser un número.',
            'form.presentation_quantity.min' => 'La cantidad debe ser mayor a 0.',
            'temporaryImages.*.image' => 'El archivo debe ser una imagen.',
            'temporaryImages.*.max' => 'La imagen no puede ser mayor a 2MB.',
        ];
    }

    public function getSelectedPresentationProperty()
    {
        if (!empty($this->form['product_presentation_id'])) {
            return ProductPresentation::find($this->form['product_presentation_id']);
        }
        return null;
    }

    public function getCurrentUsdRateProperty()
    {
        return \App\Models\ProductListing::getUsdRate()['rate'] ?? 0;
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
                'currency_type' => $this->editingListing->currency_type,
                'quality_grade' => $this->editingListing->quality_grade,
                'is_harvesting' => $this->editingListing->is_harvesting ?? false,
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
                foreach ($this->editingListing->images as $index => $imagePath) {
                    // Usar la misma lógica que el trait HasListingImages
                    $disk = app()->environment('production') ? 'r2' : 'public';
                    
                    if ($disk === 'r2') {
                        $publicUrl = config('filesystems.disks.r2.url');
                        $path = ltrim($imagePath, '/');
                        $previewUrl = rtrim($publicUrl, '/') . '/' . $path;
                    } else {
                        $previewUrl = asset('storage/' . $imagePath);
                    }
                    
                    $this->selectedImages[] = [
                        'id' => uniqid(),
                        'name' => basename($imagePath),
                        'path' => $imagePath,
                        'preview' => $previewUrl
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
            'currency_type' => 'USD',
            'quality_grade' => '',
            'is_harvesting' => false,
            'harvest_date' => '',
            'state_id' => '',
            'municipality_id' => '',
            'parish_id' => '',
            'status' => 'active',
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

        // Limpiar datos antes de guardar
        $cleanForm = $this->form;
        
        // Si no está en cosecha, limpiar la fecha de cosecha
        if (!$cleanForm['is_harvesting']) {
            $cleanForm['harvest_date'] = null;
        }
        
        // Si está en cosecha pero no hay fecha, mantener null
        if ($cleanForm['is_harvesting'] && empty($cleanForm['harvest_date'])) {
            $cleanForm['harvest_date'] = null;
        }

        // Normalizar valores vacíos a null para columnas numéricas/fecha
        foreach (['state_id', 'municipality_id', 'parish_id', 'market_id', 'product_id', 'product_presentation_id'] as $key) {
            if (array_key_exists($key, $cleanForm) && ($cleanForm[$key] === '' || $cleanForm[$key] === null)) {
                $cleanForm[$key] = null;
            }
        }
        // Si es mercado mayorista, limpiar ubicación de finca
        if (($cleanForm['selling_location_type'] ?? 'farm_gate') === 'wholesale_market') {
            $cleanForm['state_id'] = null;
            $cleanForm['municipality_id'] = null;
            $cleanForm['parish_id'] = null;
        }
        if (empty($cleanForm['harvest_date'])) {
            $cleanForm['harvest_date'] = null;
        }

        $listingData = array_merge($cleanForm, [
            'person_id' => $person->id,
            'images' => $cleanForm['images'] ?? [], // Asegurarnos de que las imágenes se guarden
        ]);

            Log::info('Datos preparados para guardar', [
                'listing_data' => $listingData,
                'images_array' => $listingData['images'],
                'is_harvesting' => $listingData['is_harvesting'],
                'harvest_date' => $listingData['harvest_date']
            ]);

            // Verificar que el producto existe antes de crear
            $productExists = \App\Models\Product::where('id', $listingData['product_id'])->exists();
            Log::info('Verificación de producto antes de crear', [
                'product_id' => $listingData['product_id'],
                'exists' => $productExists,
                'connection' => \Illuminate\Support\Facades\DB::connection()->getName()
            ]);

            if (!$productExists) {
                throw new \Exception("El producto con ID {$listingData['product_id']} no existe en la base de datos");
            }

            // Usar transacción para asegurar consistencia
            \Illuminate\Support\Facades\DB::beginTransaction();

        if ($this->editingListing) {
                Log::info('Actualizando listing existente', ['listing_id' => $this->editingListing->id]);
                
                // Si estamos editando, actualizar las imágenes
            $this->editingListing->update($listingData);
                
                Log::info('Listing actualizado exitosamente');
            $this->dispatch('listing-updated');
        } else {
                Log::info('Creando nuevo listing');
                
                try {
                    // Si es nuevo, crear con las imágenes
                    $newListing = ProductListing::create($listingData);
                    
                    Log::info('Listing creado exitosamente', [
                        'listing_id' => $newListing->id,
                        'images_saved' => $newListing->images
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::error('Error SQL al crear listing', [
                        'sql_error' => $e->getMessage(),
                        'sql_code' => $e->getCode(),
                        'sql_info' => $e->errorInfo ?? null,
                        'bindings' => $e->getBindings() ?? null,
                        'sql' => $e->getSql() ?? null
                    ]);
                    throw new \Exception('Error de base de datos: ' . $e->getMessage());
                } catch (\Exception $e) {
                    Log::error('Error general al crear listing', [
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
                
            $this->dispatch('listing-added');
        }
            
            \Illuminate\Support\Facades\DB::commit();
        
        $this->closeModal();
        $this->loadListings();

            Log::info('saveListing completado exitosamente');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
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

            // Validar extensión del archivo
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
            $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $allowedExtensions)) {
                throw new \Exception('Formato de imagen no válido. Solo se permiten: PNG, JPG, JPEG, WEBP');
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

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'deleteListing'
    ];

    public function confirmDelete($listingId)
    {
        $this->listingIdToDelete = $listingId;
        $this->dispatch('show-delete-confirmation');
    }

    // Métodos para compartir publicaciones
    public function shareToWhatsApp($listingId)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($listingId);
            
            // Construir mensaje personalizado
            $message = "🌱 *" . $listing->title . "*\n\n";
            $message .= "💰 *Precio:* " . $listing->formatted_price . "\n";
            $message .= "📦 *Presentación:* " . $listing->formatted_presentation . "\n";
            $message .= "📍 *Ubicación:* " . $listing->location . "\n";
            $message .= "👤 *Vendedor:* " . $listing->person->name . "\n\n";
            $message .= "🔗 *Ver más detalles:* " . route('market.index') . "\n\n";
            $message .= "#EzequielNegocios #Agricultura #Venezuela";
            
            // URL de WhatsApp con mensaje
            $url = 'https://wa.me/?text=' . urlencode($message);
            
            return redirect()->away($url);
        } catch (\Exception $e) {
            Log::error('Error al compartir por WhatsApp', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo abrir WhatsApp para compartir.');
        }
    }

    public function shareToFacebook($listingId)
    {
        try {
            $listing = ProductListing::findOrFail($listingId);
            $url = route('market.index');
            
            // URL de Facebook con parámetros
            $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);
            
            return redirect()->away($facebookUrl);
        } catch (\Exception $e) {
            Log::error('Error al compartir por Facebook', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo abrir Facebook para compartir.');
        }
    }

    public function shareToTwitter($listingId)
    {
        try {
            $listing = ProductListing::with(['product'])->findOrFail($listingId);
            $url = route('market.index');
            
            // Mensaje para Twitter
            $text = "🌱 " . $listing->title . " - " . $listing->formatted_price . " en EzequielNegocios";
            
            // URL de Twitter con parámetros
            $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($text) . '&url=' . urlencode($url);
            
            return redirect()->away($twitterUrl);
        } catch (\Exception $e) {
            Log::error('Error al compartir por Twitter', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo abrir Twitter para compartir.');
        }
    }

    public function shareToEmail($listingId)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($listingId);
            $url = route('market.index');
            
            // Asunto del email
            $subject = "🌱 " . $listing->title . " - EzequielNegocios";
            
            // Cuerpo del email
            $body = "Hola,\n\n";
            $body .= "Te comparto esta publicación de " . $listing->person->name . ":\n\n";
            $body .= "📦 Producto: " . $listing->product->name . "\n";
            $body .= "💰 Precio: " . $listing->formatted_price . "\n";
            $body .= "📦 Presentación: " . $listing->formatted_presentation . "\n";
            $body .= "📍 Ubicación: " . $listing->location . "\n\n";
            $body .= "🔗 Ver más detalles: " . $url . "\n\n";
            $body .= "¡Saludos!";
            
            // URL de mailto
            $emailUrl = 'mailto:?subject=' . urlencode($subject) . '&body=' . urlencode($body);
            
            return redirect()->away($emailUrl);
        } catch (\Exception $e) {
            Log::error('Error al compartir por Email', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo abrir el cliente de email.');
        }
    }

    public function downloadSocialMediaImage($listingId)
    {
        try {
            $listing = ProductListing::with(['product', 'person'])->findOrFail($listingId);
            
            // Generar URL para descargar imagen personalizada para redes sociales
            $imageUrl = route('listing.social-media-image', $listingId);
            
            // Abrir en nueva ventana para descargar
            $this->js("
                window.open('" . $imageUrl . "', '_blank');
            ");
            
            $this->dispatch('success', 'Imagen para redes sociales generada. Descárgala y compártela en tus redes sociales.');
            
        } catch (\Exception $e) {
            Log::error('Error al generar imagen para redes sociales', [
                'listing_id' => $listingId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('error', 'No se pudo generar la imagen para redes sociales.');
        }
    }

    #[On('deleteListing')]
    public function deleteListing($listingId = null)
    {
        try {
            // Usar el ID pasado como parámetro o el almacenado en la propiedad
            $id = $listingId ?? $this->listingIdToDelete;
            
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

            // Resetear el ID almacenado
            $this->listingIdToDelete = null;

            // Dispatch del evento de éxito
            $this->dispatch('listing-deleted');

        } catch (\Exception $e) {
            Log::error('Error al eliminar publicación', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'listing_id' => $id ?? 'no definido'
            ]);
            
            // Dispatch del evento de error
            $this->dispatch('error', 'Error al eliminar la publicación: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = Product::where(function($query) {
            $query->where('person_id', Auth::id())
                  ->orWhere('is_universal', true);
        })->get();

        // Log para debugging - qué productos están disponibles
        Log::info('Productos disponibles para listings', [
            'user_id' => Auth::id(),
            'products_count' => $products->count(),
            'products' => $products->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'person_id' => $p->person_id,
                    'is_universal' => $p->is_universal
                ];
            })->toArray()
        ]);

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

