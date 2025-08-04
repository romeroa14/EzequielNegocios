-- Script SQL para agregar columnas faltantes en product_listings
-- Ejecutar en producción para corregir la estructura de la tabla

-- 1. Verificar estructura actual de la tabla
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'product_listings' 
ORDER BY ordinal_position;

-- 2. Agregar columnas faltantes si no existen

-- Agregar product_presentation_id
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'product_listings' 
        AND column_name = 'product_presentation_id'
    ) THEN
        ALTER TABLE product_listings 
        ADD COLUMN product_presentation_id BIGINT NOT NULL DEFAULT 1;
        
        -- Agregar foreign key constraint
        ALTER TABLE product_listings 
        ADD CONSTRAINT product_listings_product_presentation_id_foreign 
        FOREIGN KEY (product_presentation_id) 
        REFERENCES product_presentations(id) 
        ON DELETE CASCADE;
        
        RAISE NOTICE 'Columna product_presentation_id agregada exitosamente';
    ELSE
        RAISE NOTICE 'Columna product_presentation_id ya existe';
    END IF;
END
$$;

-- Agregar presentation_quantity
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'product_listings' 
        AND column_name = 'presentation_quantity'
    ) THEN
        ALTER TABLE product_listings 
        ADD COLUMN presentation_quantity NUMERIC(10,2) NOT NULL DEFAULT 1.00;
        
        RAISE NOTICE 'Columna presentation_quantity agregada exitosamente';
    ELSE
        RAISE NOTICE 'Columna presentation_quantity ya existe';
    END IF;
END
$$;

-- 3. Verificar que las columnas necesarias existen
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

-- 4. Mostrar estructura final de la tabla
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_name = 'product_listings' 
ORDER BY ordinal_position;

-- 5. Contar registros en la tabla
SELECT 'Total de registros en product_listings: ' || COUNT(*) as info
FROM product_listings; 