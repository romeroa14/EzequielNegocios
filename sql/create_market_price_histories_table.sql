-- Crear tabla para historial de cambios de precios de mercado
-- Esta tabla almacena el historial completo de cambios de precio por producto

CREATE TABLE market_price_histories (
    id BIGSERIAL PRIMARY KEY,
    product_id BIGINT NOT NULL,
    old_price DECIMAL(10,2) NOT NULL,
    new_price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'VES',
    change_date TIMESTAMP NOT NULL,
    notes TEXT,
    changed_by BIGINT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices para optimizar consultas
    CONSTRAINT fk_market_price_histories_product_id 
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_market_price_histories_changed_by 
        FOREIGN KEY (changed_by) REFERENCES people(id) ON DELETE SET NULL,
    
    -- Restricciones únicas para consultas eficientes
    CONSTRAINT idx_market_price_histories_product_date UNIQUE (product_id, change_date)
);

-- Comentario de la tabla
COMMENT ON TABLE market_price_histories IS 'Historial de cambios de precios de mercado - Trazabilidad completa de variaciones de precio';

-- Comentarios de las columnas
COMMENT ON COLUMN market_price_histories.product_id IS 'ID del producto al que pertenece el cambio de precio';
COMMENT ON COLUMN market_price_histories.old_price IS 'Precio anterior antes del cambio';
COMMENT ON COLUMN market_price_histories.new_price IS 'Nuevo precio después del cambio';
COMMENT ON COLUMN market_price_histories.currency IS 'Código de moneda (VES, USD, etc.)';
COMMENT ON COLUMN market_price_histories.change_date IS 'Fecha y hora exacta del cambio de precio';
COMMENT ON COLUMN market_price_histories.notes IS 'Notas o razones del cambio de precio';
COMMENT ON COLUMN market_price_histories.changed_by IS 'ID de la persona que realizó el cambio';
COMMENT ON COLUMN market_price_histories.created_at IS 'Fecha de creación del registro';
COMMENT ON COLUMN market_price_histories.updated_at IS 'Fecha de última actualización del registro';
