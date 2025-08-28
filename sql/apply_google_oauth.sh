#!/bin/bash

# =====================================================
# SCRIPT PARA APLICAR GOOGLE OAUTH EN PRODUCCIÓN
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

echo -e "${BLUE}🚀 Iniciando configuración de Google OAuth en producción...${NC}"

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

# Verificar que el archivo SQL existe
if [ ! -f "add_google_oauth.sql" ]; then
    error_exit "El archivo add_google_oauth.sql no existe en el directorio actual."
fi

info_msg "Conectando a la base de datos de producción..."

# Ejecutar el script SQL
echo -e "${BLUE}📊 Ejecutando script SQL...${NC}"

PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f add_google_oauth.sql

if [ $? -eq 0 ]; then
    success_msg "Script SQL ejecutado exitosamente"
else
    error_exit "Error al ejecutar el script SQL"
fi

# Verificar la conexión
info_msg "Verificando conexión a la base de datos..."

PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT 'Conexión exitosa' as status;" 2>/dev/null

if [ $? -eq 0 ]; then
    success_msg "Conexión a la base de datos verificada"
else
    error_exit "No se pudo conectar a la base de datos"
fi

# Verificar que la columna se agregó correctamente
info_msg "Verificando que la columna google_id se agregó correctamente..."

PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -c "SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'google_id';" 2>/dev/null

if [ $? -eq 0 ]; then
    success_msg "Columna google_id agregada correctamente"
else
    warning_msg "No se pudo verificar la columna google_id"
fi

echo -e "${GREEN}🎉 Configuración de Google OAuth completada exitosamente${NC}"
echo -e "${BLUE}📝 Próximos pasos:${NC}"
echo -e "   1. Configurar las credenciales de Google OAuth en el archivo .env"
echo -e "   2. Agregar las rutas de Google OAuth en routes/web.php"
echo -e "   3. Crear las vistas para el registro con Google"
echo -e "   4. Probar la funcionalidad de autenticación con Google"
