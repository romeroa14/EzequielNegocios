<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductListing;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->person->productListings()
            ->with(['product', 'product.category', 'product.subcategory'])
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::with('subcategories')->get();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'condition' => 'required|in:new,used,refurbished',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
        ]);

        // Crear el producto base
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'specifications' => $request->specifications,
        ]);

        // Crear el listing del producto
        $listing = ProductListing::create([
            'product_id' => $product->id,
            'person_id' => auth()->user()->person->id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'status' => 'pending', // Pendiente de aprobación
        ]);

        // Procesar y guardar las imágenes
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/' . $product->id, 'public');
                $images[] = $path;
            }
            $product->update(['images' => $images]);
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto creado exitosamente y enviado para aprobación.');
    }

    public function edit(ProductListing $listing)
    {
        $this->authorize('update', $listing);
        
        $categories = ProductCategory::with('subcategories')->get();
        $product = $listing->product;
        
        return view('seller.products.edit', compact('listing', 'product', 'categories'));
    }

    public function update(Request $request, ProductListing $listing)
    {
        $this->authorize('update', $listing);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'condition' => 'required|in:new,used,refurbished',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'specifications' => 'nullable|array',
        ]);

        // Actualizar el producto base
        $listing->product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'subcategory_id' => $request->subcategory_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'specifications' => $request->specifications,
        ]);

        // Actualizar el listing
        $listing->update([
            'price' => $request->price,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'status' => 'pending', // Vuelve a pendiente al editar
        ]);

        // Procesar nuevas imágenes si se proporcionan
        if ($request->hasFile('images')) {
            // Eliminar imágenes anteriores
            foreach ($listing->product->images ?? [] as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products/' . $listing->product->id, 'public');
                $images[] = $path;
            }
            $listing->product->update(['images' => $images]);
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto actualizado exitosamente y enviado para aprobación.');
    }

    public function destroy(ProductListing $listing)
    {
        $this->authorize('delete', $listing);

        // Eliminar imágenes
        foreach ($listing->product->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }

        // Eliminar el producto y el listing
        $listing->product->delete();
        $listing->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function getSubcategories(ProductCategory $category)
    {
        $subcategories = ProductSubcategory::where('category_id', $category->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }
} 