<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\ProductListing;
use App\Models\Product;
use App\Models\State;
use App\Models\Municipality;
use App\Models\Parish;
use Illuminate\Support\Facades\Auth;

class ListingsCrud extends Component
{
    public $listings;
    public $showModal = false;
    public $editingListing = null;
    public $form = [
        'product_id' => '',
        'title' => '',
        'description' => '',
        'unit_price' => '',
        'quantity_available' => '',
        'quality_grade' => '',
        'harvest_date' => '',
        'state_id' => '',
        'municipality_id' => '',
        'parish_id' => '',
        'status' => 'pending',
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
            'form.quantity_available' => 'required|integer|min:1',
            'form.quality_grade' => 'required|in:premium,standard,economic',
            'form.harvest_date' => 'required|date',
            'form.state_id' => 'required|exists:states,id',
            'form.municipality_id' => 'required|exists:municipalities,id',
            'form.parish_id' => 'required|exists:parishes,id',
            'form.product_id' => 'required|exists:products,id',
            'form.status' => 'required|in:pending,active,sold_out,inactive',
        ];
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
                'product'
            ])->findOrFail($listingId);
            
            $this->form = [
                'product_id' => $this->editingListing->product_id,
                'title' => $this->editingListing->title,
                'description' => $this->editingListing->description,
                'unit_price' => $this->editingListing->unit_price,
                'quantity_available' => $this->editingListing->quantity_available,
                'quality_grade' => $this->editingListing->quality_grade,
                'harvest_date' => $this->editingListing->harvest_date ? $this->editingListing->harvest_date->format('Y-m-d') : '',
                'state_id' => $this->editingListing->state_id,
                'municipality_id' => $this->editingListing->municipality_id,
                'parish_id' => $this->editingListing->parish_id,
                'status' => $this->editingListing->status,
            ];
            
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
            'quantity_available' => '',
            'quality_grade' => '',
            'harvest_date' => '',
            'state_id' => '',
            'municipality_id' => '',
            'parish_id' => '',
            'status' => 'pending',
        ];
        $this->selectedImages = [];
    }

    public function saveListing()
    {
        $this->validate();

        $person = Auth::user();
        if (!$person) {
            $this->dispatch('error', 'No tienes un perfil de vendedor asociado.');
            return;
        }

        $listingData = array_merge($this->form, [
            'person_id' => $person->id,
            'images' => [], // Las publicaciones tendrán sus propias imágenes independientes
        ]);

        if ($this->editingListing) {
            $this->editingListing->update($listingData);
            $this->dispatch('listing-updated');
        } else {
            ProductListing::create($listingData);
            $this->dispatch('listing-added');
        }
        
        $this->closeModal();
        $this->loadListings();
    }

    public function testAddImage()
    {
        // Método simple de prueba
        $this->dispatch('success', 'Botón de agregar imagen funcionando correctamente');
    }

    /**
     * Maneja la selección de una imagen del input
     */
    public function handleImageSelected($fileData = null)
    {
        if ($fileData) {
            // Usar la vista previa real del archivo
            $this->selectedImages[] = [
                'id' => uniqid(),
                'name' => 'imagen_' . count($this->selectedImages) . '.jpg',
                'preview' => $fileData
            ];
            $this->dispatch('success', 'Imagen agregada correctamente');
        } else {
            // Fallback con imagen de prueba
            $this->selectedImages[] = [
                'id' => uniqid(),
                'name' => 'imagen_' . count($this->selectedImages) . '.jpg',
                'preview' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjRjNGNEY2Ii8+Cjx0ZXh0IHg9IjUwIiB5PSI1MCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjEyIiBmaWxsPSIjNjc3NDhCIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+VmlzdGEgcHJldmlhPC90ZXh0Pgo8L3N2Zz4K'
            ];
            $this->dispatch('success', 'Imagen agregada correctamente');
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
        if (isset($this->selectedImages[$index])) {
            unset($this->selectedImages[$index]);
            $this->selectedImages = array_values($this->selectedImages);
            $this->dispatch('success', 'Imagen eliminada');
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

