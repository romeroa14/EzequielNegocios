<?php

namespace App\Livewire\Seller;

use Livewire\Component;
use App\Models\ProductListing;
use App\Models\Product;
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
        'images' => [],
        'location_city' => '',
        'location_state' => '',
        'status' => 'pending',
    ];

    public function mount()
    {
        $this->loadListings();
    }

    public function loadListings()
    {
        $this->listings = ProductListing::with('product')->orderBy('id', 'desc')->get();
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
                'location_city' => $this->editingListing->location_city,
                'location_state' => $this->editingListing->location_state,
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
            'location_city' => '',
            'location_state' => '',
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
            'form.location_city' => 'required|string|max:255',
            'form.location_state' => 'required|string|max:255',
            'form.status' => 'required|in:active,pending,sold_out,inactive',
        ]);

        $person = Auth::user();
        if (!$person) {
            session()->flash('error', 'No tienes un perfil de vendedor asociado. Contacta al administrador.');
            return;
        }
        $personId = $person->id;

        $product = Product::find($this->form['product_id']);
        $imageArray = [];
        if ($product && $product->image) {
            $imageArray[] = $product->image;
        }

        if ($this->editingListing) {
            $this->editingListing->update($this->form);
        } else {
            ProductListing::create(array_merge($this->form, [
                'person_id' => $personId,
                'images' => $imageArray,
            ]));
        }
        $this->closeModal();
        $this->loadListings();
        session()->flash('success', $this->editingListing ? 'Publicación actualizada correctamente.' : 'Publicación creada correctamente.');
    }

    public function deleteListing($listingId)
    {
        $listing = ProductListing::findOrFail($listingId);
        $listing->delete();
        $this->loadListings();
        session()->flash('success', 'Publicación eliminada correctamente.');
    }

    public function render()
    {
        $products = Product::all();
        $selectedProduct = $products->where('id', $this->form['product_id'])->first();
        return view('livewire.seller.listings-crud', [
            'listings' => $this->listings,
            'products' => $products,
        ]);
    }
}

