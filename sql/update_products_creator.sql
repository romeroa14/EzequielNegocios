-- =====================================================
-- SCRIPT PARA ACTUALIZAR creator_user_id EN TODOS LOS PRODUCTOS
-- =====================================================

-- Mostrar estado actual
SELECT 'Estado actual:' as info;
SELECT id, creator_user_id, person_id, name, is_universal FROM products ORDER BY id;

-- Actualizar todos los productos para asignar creator_user_id = 6
UPDATE products 
SET creator_user_id = 6 
WHERE id > 0;

-- Mostrar estado después de la actualización
SELECT 'Estado después de la actualización:' as info;
SELECT id, creator_user_id, person_id, name, is_universal FROM products ORDER BY id;

-- Verificar que todos los productos tengan creator_user_id = 6
SELECT 
    'Verificación:' as info,
    COUNT(*) as total_products,
    COUNT(CASE WHEN creator_user_id = 6 THEN 1 END) as products_with_creator_6,
    COUNT(CASE WHEN creator_user_id != 6 THEN 1 END) as products_with_different_creator
FROM products;

-- Mostrar resumen final
SELECT 'Resumen final:' as info;
SELECT 
    id,
    name,
    creator_user_id,
    person_id,
    is_universal,
    CASE 
        WHEN creator_user_id = 6 THEN '✅ Asignado correctamente'
        ELSE '❌ No asignado'
    END as estado
FROM products 
ORDER BY id;
