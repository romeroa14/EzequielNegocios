-- Crear índices adicionales para optimizar consultas
-- PostgreSQL requiere que los índices se creen por separado

-- Índices para bcv_rates
CREATE INDEX idx_bcv_rates_currency_code ON bcv_rates(currency_code);
CREATE INDEX idx_bcv_rates_fetched_at ON bcv_rates(fetched_at);
CREATE INDEX idx_bcv_rates_source ON bcv_rates(source);

-- Índices para market_prices
CREATE INDEX idx_market_prices_product_id ON market_prices(product_id);
CREATE INDEX idx_market_prices_price_date ON market_prices(price_date);
CREATE INDEX idx_market_prices_is_active ON market_prices(is_active);
CREATE INDEX idx_market_prices_updated_by ON market_prices(updated_by);

-- Índices para market_price_histories
CREATE INDEX idx_market_price_histories_change_date ON market_price_histories(change_date);
CREATE INDEX idx_market_price_histories_changed_by ON market_price_histories(changed_by);
CREATE INDEX idx_market_price_histories_currency ON market_price_histories(currency);

-- Comentarios de los índices
COMMENT ON INDEX idx_bcv_rates_currency_code IS 'Índice para búsquedas rápidas por código de moneda';
COMMENT ON INDEX idx_bcv_rates_fetched_at IS 'Índice para búsquedas por fecha de obtención';
COMMENT ON INDEX idx_market_prices_product_id IS 'Índice para búsquedas por producto';
COMMENT ON INDEX idx_market_prices_is_active IS 'Índice para filtrar precios activos';
COMMENT ON INDEX idx_market_price_histories_change_date IS 'Índice para búsquedas por fecha de cambio';
