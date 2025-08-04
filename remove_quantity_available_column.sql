-- Script SQL para eliminar la columna quantity_available de product_listings
-- Esta columna se agregó por error y no está en el modelo ni en el formulario

-- 1. Verificar si la columna existe
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'product_listings' 
            AND column_name = 'quantity_available'
        ) 
        THEN '⚠️ quantity_available EXISTE - será eliminada' 
        ELSE '✅ quantity_available NO EXISTE' 
    END as status;

-- 2. Eliminar la columna si existe
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'product_listings' 
        AND column_name = 'quantity_available'
    ) THEN
        ALTER TABLE product_listings 
        DROP COLUMN quantity_available;
        
        RAISE NOTICE 'Columna quantity_available eliminada exitosamente';
    ELSE
        RAISE NOTICE 'Columna quantity_available no existe, no se requiere acción';
    END IF;
END
$$;

-- 3. Verificar que la columna fue eliminada
SELECT 
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM information_schema.columns 
            WHERE table_name = 'product_listings' 
            AND column_name = 'quantity_available'
        ) 
        THEN '❌ quantity_available AÚN EXISTE (ERROR)' 
        ELSE '✅ quantity_available ELIMINADA CORRECTAMENTE' 
    END as verification_status;

-- 4. Mostrar estructura final de la tabla (sin quantity_available)
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'product_listings' 
ORDER BY ordinal_position;

-- 5. Verificar que las columnas necesarias están presentes
SELECT 
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'product_listings' AND column_name = 'product_presentation_id') 
        THEN '✅ product_presentation_id' 
        ELSE '❌ product_presentation_id FALTA' 
    END as product_presentation_id_status,
    
    CASE 
        WHEN EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'product_listings' AND column_name = 'presentation_quantity') 
        THEN '✅ presentation_quantity' 
        ELSE '❌ presentation_quantity FALTA' 
    END as presentation_quantity_status; 