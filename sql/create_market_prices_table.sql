-- Crear tabla para precios de mercado
-- Esta tabla almacena los precios actuales de los productos (un precio activo por producto)

CREATE TABLE market_prices (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'VES',
    price_date DATE NOT NULL,
    notes TEXT,
    is_active BOOLEAN NOT NULL DEFAULT true,
    updated_by BIGINT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Restricciones de integridad referencial
    CONSTRAINT fk_market_prices_product_id 
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_market_prices_updated_by 
        FOREIGN KEY (updated_by) REFERENCES people(id) ON DELETE SET NULL,
    
    -- Restricciones únicas para optimizar consultas
    CONSTRAINT idx_market_prices_product_active UNIQUE (product_id, is_active)
);

-- Comentario de la tabla
COMMENT ON TABLE market_prices IS 'Precios de mercado para productos - Actualizaciones semanales de Coche';

-- Comentarios de las columnas
COMMENT ON COLUMN market_prices.product_id IS 'ID del producto al que pertenece el precio';
COMMENT ON COLUMN market_prices.price IS 'Precio del producto en la moneda especificada';
COMMENT ON COLUMN market_prices.currency IS 'Código de moneda (VES, USD, etc.)';
COMMENT ON COLUMN market_prices.price_date IS 'Fecha del precio (cuando se estableció)';
COMMENT ON COLUMN market_prices.notes IS 'Observaciones sobre el precio, condiciones especiales, etc.';
COMMENT ON COLUMN market_prices.is_active IS 'Indica si este precio está activo (solo uno por producto)';
COMMENT ON COLUMN market_prices.updated_by IS 'ID de la persona que actualizó el precio';
COMMENT ON COLUMN market_prices.created_at IS 'Fecha de creación del registro';
COMMENT ON COLUMN market_prices.updated_at IS 'Fecha de última actualización del registro';
