-- =====================================================
-- SCRIPT PARA PERMITIR CONTRASEÑAS NULAS EN PRODUCCIÓN
-- =====================================================

-- 1. Modificar la columna password para permitir valores nulos
ALTER TABLE people ALTER COLUMN password DROP NOT NULL;

-- 2. Agregar comentario a la columna para claridad
COMMENT ON COLUMN people.password IS 'Contraseña del usuario (NULL para usuarios de Google OAuth)';

-- 3. Verificar que el cambio se aplicó correctamente
SELECT 
    'Verificación de columna password:' as info,
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'people' AND column_name = 'password';

-- 4. Mostrar usuarios de Google OAuth con contraseñas
SELECT 
    'Usuarios de Google OAuth con contraseñas:' as info,
    id,
    first_name,
    last_name,
    email,
    google_id,
    CASE 
        WHEN password IS NOT NULL THEN 'Tiene contraseña'
        ELSE 'Sin contraseña'
    END as password_status
FROM people 
WHERE google_id IS NOT NULL
ORDER BY id;

-- 5. Limpiar contraseñas de usuarios de Google OAuth
UPDATE people 
SET password = NULL 
WHERE google_id IS NOT NULL AND password IS NOT NULL;

-- 6. Verificar el resultado después de la limpieza
SELECT 
    'Estado después de limpiar contraseñas:' as info,
    COUNT(*) as total_google_users,
    COUNT(CASE WHEN password IS NULL THEN 1 END) as users_without_password,
    COUNT(CASE WHEN password IS NOT NULL THEN 1 END) as users_with_password
FROM people 
WHERE google_id IS NOT NULL;

-- 7. Mostrar resumen final
SELECT 
    '✅ Contraseñas nulas configuradas exitosamente' as status,
    'Los usuarios de Google OAuth ahora pueden tener password = NULL' as description;
