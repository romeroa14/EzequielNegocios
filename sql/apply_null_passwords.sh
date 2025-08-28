#!/bin/bash

# =====================================================
# SCRIPT PARA APLICAR CONTRASEÑAS NULAS EN PRODUCCIÓN
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

echo -e "${BLUE}🔧 Aplicando configuración de contraseñas nulas en producción...${NC}"

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
if [ ! -f "allow_null_passwords_production.sql" ]; then
    error_exit "El archivo allow_null_passwords_production.sql no existe en el directorio actual."
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
warning_msg "⚠️  IMPORTANTE: Este script modificará la estructura de la tabla people"
warning_msg "⚠️  y limpiará las contraseñas de usuarios de Google OAuth"
echo ""

read -p "¿Estás seguro de que quieres continuar? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    info_msg "Operación cancelada"
    exit 0
fi

echo ""
info_msg "Ejecutando script SQL..."

# Ejecutar el script SQL
PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f allow_null_passwords_production.sql

if [ $? -eq 0 ]; then
    success_msg "Script SQL ejecutado exitosamente"
else
    error_exit "Error al ejecutar el script SQL"
fi

echo ""
success_msg "✅ Configuración de contraseñas nulas aplicada exitosamente"
echo ""
info_msg "💡 Ahora los usuarios de Google OAuth pueden tener password = NULL"
info_msg "💡 Estos usuarios solo pueden acceder usando Google OAuth"
