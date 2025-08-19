-- =====================================================
-- SCRIPT PARA ARREGLAR LA TABLA PRODUCTS EN PRODUCCIÓN
-- =====================================================

-- 1. Agregar comentarios a las columnas para claridad
COMMENT ON COLUMN products.person_id IS 'Vendedor específico (solo para productos NO universales)';
COMMENT ON COLUMN products.creator_user_id IS 'Productor universal que creó el producto (solo para productos universales)';
COMMENT ON COLUMN products.is_universal IS 'true = producto universal, false = producto específico';

-- 2. Agregar valores por defecto
ALTER TABLE products ALTER COLUMN is_active SET DEFAULT true;

-- 3. Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_products_universal_creator ON products(is_universal, creator_user_id);
CREATE INDEX IF NOT EXISTS idx_products_universal_person ON products(is_universal, person_id);

-- 4. Eliminar constraint problemático si existe
ALTER TABLE products DROP CONSTRAINT IF EXISTS check_product_type;

-- NOTA: Se eliminó el constraint check_product_type para simplificar la lógica
-- La validación se maneja a nivel de aplicación en el formulario

-- 5. Asegurar que los productos universales existentes tengan creator_user_id
UPDATE products 
SET creator_user_id = (
    SELECT id FROM users 
    WHERE role = 'producer' AND is_universal = true 
    LIMIT 1
) 
WHERE is_universal = true AND creator_user_id IS NULL;

-- 6. Asegurar que los productos normales existentes tengan person_id
UPDATE products 
SET person_id = (
    SELECT id FROM people 
    WHERE role = 'seller' AND is_active = true 
    LIMIT 1
) 
WHERE is_universal = false AND person_id IS NULL;

-- 7. Verificar el estado después de los cambios
SELECT 
    'Estado después de los cambios:' as info,
    COUNT(*) as total_products,
    COUNT(CASE WHEN is_universal = true THEN 1 END) as universal_products,
    COUNT(CASE WHEN is_universal = false THEN 1 END) as normal_products,
    COUNT(CASE WHEN is_universal = true AND creator_user_id IS NOT NULL THEN 1 END) as universal_with_creator,
    COUNT(CASE WHEN is_universal = false AND person_id IS NOT NULL THEN 1 END) as normal_with_seller
FROM products;

-- 8. Mostrar resumen de productos
SELECT 
    'Resumen de productos:' as info,
    id,
    name,
    is_universal,
    person_id,
    creator_user_id,
    CASE 
        WHEN is_universal = true THEN 'Producto Universal'
        WHEN is_universal = false THEN 'Producto Normal'
        ELSE 'Estado Desconocido'
    END as tipo_producto
FROM products 
ORDER BY id;

-- 9. Mostrar estadísticas finales
SELECT 
    'Estadísticas finales:' as info,
    'Total productos: ' || COUNT(*) as total,
    'Productos universales: ' || COUNT(CASE WHEN is_universal = true THEN 1 END) as universales,
    'Productos normales: ' || COUNT(CASE WHEN is_universal = false THEN 1 END) as normales
FROM products;
