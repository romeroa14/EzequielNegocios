<!-- Search -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
    <input type="text" wire:model.live="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
</div>

<!-- Categories -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
    <select wire:model.live="selectedCategory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las categorías</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
</div>

<!-- Subcategories -->
@if($subcategories->isNotEmpty())
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Subcategoría</label>
    <select wire:model.live="selectedSubcategory" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las subcategorías</option>
        @foreach($subcategories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
        @endforeach
    </select>
</div>
@endif

<!-- Product Lines -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Línea de Producto</label>
    <select wire:model.live="selectedLine" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las líneas</option>
        @foreach($this->productLines as $line)
            <option value="{{ $line->id }}">{{ $line->name }}</option>
        @endforeach
    </select>
</div>

<!-- Brands -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
    <select wire:model.live="selectedBrand" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las marcas</option>
        @foreach($this->brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
        @endforeach
    </select>
</div>

<!-- Presentations -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Presentación</label>
    <select wire:model.live="selectedPresentation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las presentaciones</option>
        @foreach($this->presentations as $presentation)
            <option value="{{ $presentation->id }}">{{ $presentation->name }}</option>
        @endforeach
    </select>
</div>

<!-- Quality -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Calidad</label>
    <select wire:model.live="selectedQuality" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Todas las calidades</option>
        <option value="premium">Premium</option>
        <option value="standard">Estándar</option>
        <option value="economic">Económica</option>
    </select>
</div>

<!-- Price Range -->
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Rango de Precio</label>
    <div class="flex gap-3">
        <input type="number" wire:model.live="minPrice" placeholder="Min" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <input type="number" wire:model.live="maxPrice" placeholder="Max" class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
</div>

<!-- Clear Filters -->
<button wire:click="clearFilters" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
    Limpiar Filtros
</button> 