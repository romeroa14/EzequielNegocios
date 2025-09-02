-- Crear tabla para tasas de cambio del BCV
-- Esta tabla almacena las tasas de cambio obtenidas del Banco Central de Venezuela

CREATE TABLE bcv_rates (
    id BIGSERIAL PRIMARY KEY,
    currency_code VARCHAR(3) NOT NULL,
    rate DECIMAL(20,8) NOT NULL,
    fetched_at TIMESTAMP NOT NULL,
    source VARCHAR(50) NOT NULL DEFAULT 'BCV',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices para optimizar consultas
    CONSTRAINT idx_bcv_rates_currency_code UNIQUE (currency_code, fetched_at)
);

-- Comentario de la tabla
COMMENT ON TABLE bcv_rates IS 'Tasas de cambio del BCV - Monitoreo de divisas para conversiones automáticas';

-- Comentarios de las columnas
COMMENT ON COLUMN bcv_rates.currency_code IS 'Código de la moneda (USD, EUR, CNY, etc.)';
COMMENT ON COLUMN bcv_rates.rate IS 'Tasa de cambio en bolívares (precisión de 8 decimales)';
COMMENT ON COLUMN bcv_rates.fetched_at IS 'Fecha y hora exacta cuando se obtuvo la tasa';
COMMENT ON COLUMN bcv_rates.source IS 'Fuente de la tasa (BCV, API, etc.)';
COMMENT ON COLUMN bcv_rates.created_at IS 'Fecha de creación del registro';
COMMENT ON COLUMN bcv_rates.updated_at IS 'Fecha de última actualización del registro';
