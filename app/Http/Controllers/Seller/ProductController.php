<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductListing;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $listings = auth()->user()->productListings()
            ->with(['product.category', 'product.subcategory'])
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('listings'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('seller.products.create', compact('categories'));
    }

    public function getSubcategories(ProductCategory $category)
    {
        return response()->json([
            'subcategories' => $category->subcategories()
                ->where('is_active', true)
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'unit_type' => 'required|string|max:50',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'minimum_order_quantity' => 'required|numeric|min:1',
            'maximum_order_quantity' => 'required|numeric|min:1',
            'delivery_time' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Crear o encontrar el producto base
            $product = Product::firstOrCreate(
                [
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'subcategory_id' => $request->subcategory_id,
                ],
                [
                    'description' => $request->description,
                    'unit_type' => $request->unit_type,
                    'is_active' => true,
                ]
            );

            // Manejar la imagen si se proporcionó una
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $product->update(['image' => $imagePath]);
            }

            // Crear el listing del producto
            $listing = ProductListing::create([
                'product_id' => $product->id,
                'person_id' => auth()->id(),
                'price' => $request->price,
                'available_quantity' => $request->available_quantity,
                'minimum_order_quantity' => $request->minimum_order_quantity,
                'maximum_order_quantity' => $request->maximum_order_quantity,
                'delivery_time' => $request->delivery_time,
                'is_active' => true,
                'status' => ProductListing::STATUS_ACTIVE,
            ]);

            DB::commit();

            return redirect()
                ->route('seller.products.index')
                ->with('success', '¡Producto creado y publicado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear el producto. Por favor, intente nuevamente.');
        }
    }

    public function edit(ProductListing $listing)
    {
        $this->authorize('update', $listing);
        
        $categories = ProductCategory::where('is_active', true)->get();
        $subcategories = $listing->product->category->subcategories;
        
        return view('seller.products.edit', compact('listing', 'categories', 'subcategories'));
    }

    public function update(Request $request, ProductListing $listing)
    {
        $this->authorize('update', $listing);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'unit_type' => 'required|string|max:50',
            'image' => 'nullable|image|max:2048',
            'price' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'minimum_order_quantity' => 'required|numeric|min:1',
            'maximum_order_quantity' => 'required|numeric|min:1',
            'delivery_time' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar el producto base
            $listing->product->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'unit_type' => $request->unit_type,
            ]);

            // Manejar la imagen si se proporcionó una nueva
            if ($request->hasFile('image')) {
                if ($listing->product->image) {
                    Storage::disk('public')->delete($listing->product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $listing->product->update(['image' => $imagePath]);
            }

            // Actualizar el listing
            $listing->update([
                'price' => $request->price,
                'available_quantity' => $request->available_quantity,
                'minimum_order_quantity' => $request->minimum_order_quantity,
                'maximum_order_quantity' => $request->maximum_order_quantity,
                'delivery_time' => $request->delivery_time,
            ]);

            DB::commit();

            return redirect()
                ->route('seller.products.index')
                ->with('success', '¡Producto actualizado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto. Por favor, intente nuevamente.');
        }
    }

    public function destroy(ProductListing $listing)
    {
        $this->authorize('delete', $listing);

        try {
            DB::beginTransaction();

            // Si es el último listing de este producto, eliminar también el producto
            if ($listing->product->productListings()->count() === 1) {
                if ($listing->product->image) {
                    Storage::disk('public')->delete($listing->product->image);
                }
                $listing->product->delete();
            }

            $listing->delete();

            DB::commit();

            return redirect()
                ->route('seller.products.index')
                ->with('success', '¡Producto eliminado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el producto. Por favor, intente nuevamente.');
        }
    }

    public function toggleStatus(ProductListing $listing)
    {
        $this->authorize('update', $listing);

        $listing->update([
            'is_active' => !$listing->is_active
        ]);

        return back()->with('success', 
            $listing->is_active 
                ? '¡Producto activado exitosamente!' 
                : '¡Producto desactivado exitosamente!'
        );
    }
} 