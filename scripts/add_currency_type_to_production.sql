-- Script para agregar el campo currency_type a la tabla product_listings en producción
-- Ejecutar este script en la base de datos de producción

-- 1. Agregar el campo currency_type
ALTER TABLE product_listings 
ADD COLUMN currency_type VARCHAR(3) NOT NULL DEFAULT 'USD' 
CHECK (currency_type IN ('USD', 'VES'));

-- 2. Agregar comentario al campo
COMMENT ON COLUMN product_listings.currency_type IS 'Moneda en la que está expresado el precio';

-- 3. Verificar que el campo se agregó correctamente
SELECT column_name, data_type, column_default, is_nullable 
FROM information_schema.columns 
WHERE table_name = 'product_listings' AND column_name = 'currency_type';

-- 4. Mostrar algunos registros para verificar
SELECT id, title, unit_price, currency_type, created_at 
FROM product_listings 
LIMIT 5; 