-- Agregar campo is_harvesting a product_listings
-- Este campo indica si el producto está en cosecha o no

-- Agregar el campo is_harvesting
ALTER TABLE product_listings 
ADD COLUMN is_harvesting BOOLEAN DEFAULT FALSE;

-- Agregar comentario al campo is_harvesting
COMMENT ON COLUMN product_listings.is_harvesting IS 'Indica si el producto está en cosecha';

-- Hacer nullable el campo harvest_date (ya que solo es requerido si is_harvesting = true)
ALTER TABLE product_listings 
ALTER COLUMN harvest_date DROP NOT NULL;

-- Agregar comentario al campo harvest_date
COMMENT ON COLUMN product_listings.harvest_date IS 'Fecha de cosecha (requerida solo si is_harvesting = true)';

-- Crear índice para mejorar consultas por estado de cosecha
CREATE INDEX idx_product_listings_is_harvesting ON product_listings(is_harvesting);

-- Verificar que los cambios se aplicaron correctamente
SELECT 
    column_name, 
    data_type, 
    is_nullable, 
    column_default
FROM information_schema.columns 
WHERE table_name = 'product_listings' 
AND column_name IN ('is_harvesting', 'harvest_date')
ORDER BY ordinal_position;
