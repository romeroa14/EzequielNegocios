-- Script de instalación del sistema de precios de mercado
-- Ejecutar en el orden especificado para evitar errores de dependencias

DO $$
BEGIN
    RAISE NOTICE '=====================================================';
    RAISE NOTICE '🚀 INSTALANDO SISTEMA DE PRECIOS DE MERCADO';
    RAISE NOTICE '=====================================================';
    RAISE NOTICE 'Iniciando instalación...';
END $$;

-- 1. Crear tabla de tasas del BCV
\i sql/create_bcv_rates_table.sql

-- 2. Crear tabla de precios de mercado
\i sql/create_market_prices_table.sql

-- 3. Crear tabla de historial de precios
\i sql/create_market_price_histories_table.sql

-- 4. Crear índices adicionales
\i sql/create_indexes.sql

-- 5. Verificar que las tablas se crearon correctamente
DO $$
DECLARE
    table_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO table_count 
    FROM information_schema.tables 
    WHERE table_name IN ('bcv_rates', 'market_prices', 'market_price_histories')
    AND table_schema = 'public';
    
    IF table_count = 3 THEN
        RAISE NOTICE '✅ Todas las tablas se crearon exitosamente';
        RAISE NOTICE '📊 Tablas creadas: %', table_count;
    ELSE
        RAISE NOTICE '❌ Error: Solo se crearon % tablas de 3 esperadas', table_count;
    END IF;
END $$;

-- 6. Mostrar resumen de la instalación
DO $$
BEGIN
    RAISE NOTICE '=====================================================';
    RAISE NOTICE '🎯 INSTALACIÓN COMPLETADA';
    RAISE NOTICE '=====================================================';
    RAISE NOTICE '✅ bcv_rates - Tasas de cambio del BCV';
    RAISE NOTICE '✅ market_prices - Precios actuales de productos';
    RAISE NOTICE '✅ market_price_histories - Historial de cambios';
    RAISE NOTICE '✅ Índices optimizados para consultas rápidas';
    RAISE NOTICE '';
    RAISE NOTICE '🚀 El sistema está listo para usar!';
    RAISE NOTICE '📝 Próximo paso: Configurar el webhook en cron-job.org';
    RAISE NOTICE '=====================================================';
END $$;
