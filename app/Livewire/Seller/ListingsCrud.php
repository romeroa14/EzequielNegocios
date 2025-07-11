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
        'images' => [],
        'state_id' => '',
        'municipality_id' => '',
        'parish_id' => '',
        'status' => 'pending',
    ];

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
        }
    }

    public function updatedFormMunicipalityId($value)
    {
        if ($value) {
            $this->parishes = Parish::where('municipality_id', $value)->get();
            $this->form['parish_id'] = null;
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
            $this->editingListing = ProductListing::findOrFail($listingId);
            $this->form = [
                'product_id' => $this->editingListing->product_id,
                'title' => $this->editingListing->title,
                'description' => $this->editingListing->description,
                'unit_price' => $this->editingListing->unit_price,
                'quantity_available' => $this->editingListing->quantity_available,
                'quality_grade' => $this->editingListing->quality_grade,
                'harvest_date' => $this->editingListing->harvest_date ? $this->editingListing->harvest_date->format('Y-m-d') : '',
                'images' => $this->editingListing->images ?? [],
                'state_id' => $this->editingListing->state_id,
                'municipality_id' => $this->editingListing->municipality_id,
                'parish_id' => $this->editingListing->parish_id,
                'status' => $this->editingListing->status,
            ];
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
            'images' => [],
            'state_id' => '',
            'municipality_id' => '',
            'parish_id' => '',
            'status' => 'pending',
        ];
    }

    public function saveListing()
    {
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
        ]);

        $person = Auth::user();
        if (!$person) {
            $this->dispatch('error', 'No tienes un perfil de vendedor asociado. Contacta al administrador.');
            return;
        }
        $personId = $person->id;

        $product = Product::find($this->form['product_id']);
        $imageArray = [];
        if ($product && $product->image) {
            $imageArray[] = $product->image;
        }

        try {
            if ($this->editingListing) {
                $this->editingListing->update(array_merge($this->form, [
                    'person_id' => $personId,
                    'images' => $imageArray,
                ]));
                $this->dispatch('listing-updated');
            } else {
                ProductListing::create(array_merge($this->form, [
                    'person_id' => $personId,
                    'images' => $imageArray,
                ]));
                $this->dispatch('listing-added');
            }
            $this->closeModal();
            $this->loadListings();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Hubo un error al guardar la publicación. Por favor, intenta de nuevo.');
        }
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

