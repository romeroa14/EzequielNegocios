-- Script SQL para corregir rutas de imágenes en product_listings
-- Cambiar todas las ocurrencias de "products\/" por "listings\/" en el campo images (JSON ARRAY)

-- 1. Hacer backup de la tabla antes de modificar (recomendado)
-- CREATE TABLE product_listings_backup AS SELECT * FROM product_listings;

-- 2. Ver registros que serán afectados (para verificar antes de ejecutar)
-- Buscar en el contenido del array JSON cualquier elemento que contenga "products\/"
SELECT 
    id, 
    title,
    images as images_before
FROM product_listings 
WHERE images::text LIKE '%products\\/%';

-- 3. UPDATE para cambiar "products\/" por "listings\/" directamente en el texto JSON
UPDATE product_listings 
SET images = REPLACE(images::text, 'products\/', 'listings\/')::json
WHERE images::text LIKE '%products\\/%';

-- 4. Verificar los cambios realizados
SELECT 
    id, 
    title,
    images as images_after
FROM product_listings 
WHERE images::text LIKE '%listings\\/%';

-- 5. Contar registros afectados
SELECT 
    'Total registros en product_listings' as descripcion,
    COUNT(*) as cantidad
FROM product_listings
UNION ALL
SELECT 
    'Registros con imágenes en listings\/' as descripcion,
    COUNT(*) as cantidad
FROM product_listings 
WHERE images::text LIKE '%listings\\/%'
UNION ALL
SELECT 
    'Registros con imágenes en products\/ (deben ser 0)' as descripcion,
    COUNT(*) as cantidad
FROM product_listings 
WHERE images::text LIKE '%products\\/%';

-- 6. Script de rollback (en caso de necesitarlo)
-- UPDATE product_listings 
-- SET images = REPLACE(images::text, 'listings\/', 'products\/')::json
-- WHERE images::text LIKE '%listings\\/%';

-- 7. ALTERNATIVA (si la barra no está escapada):
-- UPDATE product_listings 
-- SET images = REPLACE(images::text, '"products/', '"listings/')::json
-- WHERE images::text LIKE '%"products/%'; 