-- =====================================================
-- SCRIPT PARA AGREGAR SOPORTE DE GOOGLE OAUTH
-- =====================================================

-- 1. Agregar columna google_id a la tabla users
ALTER TABLE users ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) UNIQUE;

-- 2. Agregar comentario a la columna
COMMENT ON COLUMN users.google_id IS 'ID único de Google para autenticación OAuth';

-- 3. Crear índice para mejorar el rendimiento de búsquedas por google_id
CREATE INDEX IF NOT EXISTS idx_users_google_id ON users(google_id);

-- 4. Verificar que la columna se agregó correctamente
SELECT 
    'Verificación de columna google_id:' as info,
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'users' AND column_name = 'google_id';

-- 5. Mostrar estructura actualizada de la tabla users
SELECT 
    'Estructura actualizada de users:' as info,
    column_name,
    data_type,
    is_nullable
FROM information_schema.columns 
WHERE table_name = 'users' 
ORDER BY ordinal_position;

-- 6. Verificar que no hay conflictos con datos existentes
SELECT 
    'Verificación de datos existentes:' as info,
    COUNT(*) as total_users,
    COUNT(CASE WHEN google_id IS NOT NULL THEN 1 END) as users_with_google_id,
    COUNT(CASE WHEN google_id IS NULL THEN 1 END) as users_without_google_id
FROM users;

-- 7. Mostrar resumen final
SELECT 
    '✅ Google OAuth configurado exitosamente' as status,
    'La tabla users ahora soporta autenticación con Google' as description;
