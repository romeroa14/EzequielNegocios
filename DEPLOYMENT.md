# Guía de Corrección de Imágenes en Producción

## Problema
Las imágenes de `ProductListing` se están guardando en `products/` en lugar de `listings/` en producción.

## Pasos para corregir en producción:

### 1. Hacer backup de la base de datos
```bash
pg_dump sistema_compraventa > backup_before_image_fix.sql
```

### 2. Subir archivos actualizados
- `app/Livewire/Seller/ListingsCrud.php` (mejorado logging y validación)

### 3. Ejecutar script de migración
```bash
php fix_listings_images.php
```

### 4. Verificar resultados
```bash
php artisan tinker --execute="
\$listings = \App\Models\ProductListing::whereNotNull('images')->get();
foreach(\$listings as \$l) {
    echo 'ID: ' . \$l->id . ' - Imágenes: ' . json_encode(\$l->images) . PHP_EOL;
}
"
```

### 5. Verificar almacenamiento R2
```bash
php artisan tinker --execute="
echo 'Verificando archivos en R2...' . PHP_EOL;
\$disk = \Storage::disk('r2');
\$files = \$disk->allFiles('listings');
echo 'Archivos en listings/: ' . count(\$files) . PHP_EOL;
"
```

## Archivos modificados:
- ✅ `app/Livewire/Seller/ListingsCrud.php`
- ✅ `fix_listings_images.php` (script de migración)

## Resultado esperado:
- Todas las imágenes de listings deben estar en la carpeta `listings/`
- Mejor logging para debugging futuro
- Validación de almacenamiento en R2 