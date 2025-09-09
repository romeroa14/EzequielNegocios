-- Script para hacer el campo 'role' nullable en la tabla 'people'
-- Ejecutar en producción para permitir usuarios sin rol asignado inicialmente

-- Hacer el campo 'role' nullable
ALTER TABLE people ALTER COLUMN role DROP NOT NULL;

-- Verificar que el cambio se aplicó correctamente
SELECT column_name, is_nullable, data_type, column_default 
FROM information_schema.columns 
WHERE table_name = 'people' AND column_name = 'role';
