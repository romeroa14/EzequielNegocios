-- =====================================================
-- SCRIPT PARA AGREGAR SOPORTE DE GOOGLE OAUTH A PEOPLE
-- =====================================================

-- 1. Agregar columna google_id a la tabla people
ALTER TABLE people ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) UNIQUE;

-- 2. Agregar comentario a la columna
COMMENT ON COLUMN people.google_id IS 'ID único de Google para autenticación OAuth';

-- 3. Crear índice para mejorar el rendimiento de búsquedas por google_id
CREATE INDEX IF NOT EXISTS idx_people_google_id ON people(google_id);

-- 4. Verificar que la columna se agregó correctamente
SELECT 
    'Verificación de columna google_id en people:' as info,
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'people' AND column_name = 'google_id';

-- 5. Mostrar estructura actualizada de la tabla people
SELECT 
    'Estructura actualizada de people:' as info,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'people' 
ORDER BY ordinal_position;

-- 6. Verificar que no hay conflictos con datos existentes
SELECT 
    'Verificación de datos existentes en people:' as info,
    COUNT(*) as total_people,
    COUNT(CASE WHEN google_id IS NOT NULL THEN 1 END) as people_with_google_id,
    COUNT(CASE WHEN google_id IS NULL THEN 1 END) as people_without_google_id
FROM people;

-- 7. Mostrar resumen final
SELECT 
    '✅ Google OAuth configurado exitosamente en people' as status,
    'La tabla people ahora soporta autenticación con Google' as description;
