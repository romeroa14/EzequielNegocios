#!/bin/bash

# =====================================================
# SCRIPT PARA VERIFICAR CONFIGURACIÓN DE GOOGLE OAUTH EN PRODUCCIÓN
# =====================================================

# Configuración de la base de datos de producción
DB_HOST="ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud"
DB_PORT="5432"
DB_NAME="main"
DB_USER="laravel"
DB_PASSWORD="npg_Gr42CaFlQUKs"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Verificando configuración de Google OAuth en producción...${NC}"

# Función para mostrar mensajes de error
error_exit() {
    echo -e "${RED}❌ Error: $1${NC}" >&2
    exit 1
}

# Función para mostrar mensajes de éxito
success_msg() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Función para mostrar mensajes de información
info_msg() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Función para mostrar mensajes de advertencia
warning_msg() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Verificar que psql esté instalado
if ! command -v psql &> /dev/null; then
    error_exit "psql no está instalado. Por favor instala PostgreSQL client."
fi

info_msg "Conectando a la base de datos de producción..."

# Verificar la conexión
PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT 'Conexión exitosa' as status;" 2>/dev/null

if [ $? -eq 0 ]; then
    success_msg "Conexión a la base de datos verificada"
else
    error_exit "No se pudo conectar a la base de datos"
fi

echo ""
info_msg "Verificando configuración de Google OAuth..."

# Verificar si existe la columna google_id en people
echo -e "${BLUE}📊 Verificando columna google_id en tabla people...${NC}"

PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "
SELECT 
    'Verificación de columna google_id en people:' as info,
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'people' AND column_name = 'google_id';
"

echo ""
info_msg "Verificando usuarios con Google OAuth..."

# Verificar usuarios con Google OAuth
PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "
SELECT 
    'Usuarios con Google OAuth:' as info,
    COUNT(*) as total_people,
    COUNT(CASE WHEN google_id IS NOT NULL THEN 1 END) as people_with_google_id,
    COUNT(CASE WHEN google_id IS NULL THEN 1 END) as people_without_google_id
FROM people;
"

echo ""
info_msg "Mostrando usuarios con Google OAuth..."

# Mostrar usuarios con Google OAuth
PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "
SELECT 
    'Detalles de usuarios con Google OAuth:' as info,
    id,
    first_name,
    last_name,
    email,
    google_id,
    is_verified,
    role,
    created_at
FROM people 
WHERE google_id IS NOT NULL
ORDER BY created_at DESC;
"

echo ""
warning_msg "⚠️  IMPORTANTE: Verifica que en Google Cloud Console tengas configurada la URL correcta:"
echo -e "${YELLOW}   https://ezequielnegocios-ezequielnegocios-ytjhfb.laravel.cloud/auth/google/callback${NC}"
echo ""
warning_msg "⚠️  NO: https://ezequielnegocios-ezequielnegocios-ytjhfb.laravel.cloud/auth/g"
echo ""

success_msg "Verificación completada"
