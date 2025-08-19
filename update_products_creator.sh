#!/bin/bash

# Script para actualizar creator_user_id en todos los productos
# Uso: ./update_products_creator.sh

# Configuración de la base de datos de producción
DB_HOST="ep-soft-snow-a5f00zvi.aws-us-east-2.pg.laravel.cloud"
DB_PORT="5432"
DB_NAME="main"
DB_USER="laravel"
DB_PASS="npg_Gr42CaFlQUKs"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔄 Actualizando creator_user_id en todos los productos${NC}"
echo ""

# Mostrar información de conexión
echo -e "${YELLOW}📋 Información de Conexión:${NC}"
echo "Host: $DB_HOST"
echo "Database: $DB_NAME"
echo "User: $DB_USER"
echo ""

# Verificar estado actual
echo -e "${YELLOW}📊 Estado actual de los productos:${NC}"
PGPASSWORD=$DB_PASS psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME -c "
SELECT id, creator_user_id, person_id, name, is_universal FROM products ORDER BY id;
"

echo ""
echo -e "${YELLOW}⚠️  ¿Estás seguro de que quieres asignar creator_user_id = 6 a TODOS los productos?${NC}"
read -p "¿Continuar? (y/N): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${GREEN}🔄 Aplicando actualización...${NC}"
    
    # Ejecutar el script SQL
    PGPASSWORD=$DB_PASS psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME -f sql/update_products_creator.sql
    
    if [ $? -eq 0 ]; then
        echo ""
        echo -e "${GREEN}✅ Actualización aplicada exitosamente${NC}"
        echo ""
        echo -e "${YELLOW}📊 Verificación final:${NC}"
        PGPASSWORD=$DB_PASS psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME -c "
        SELECT 
            'Verificación final:' as info,
            COUNT(*) as total_products,
            COUNT(CASE WHEN creator_user_id = 6 THEN 1 END) as products_with_creator_6
        FROM products;
        "
        
        echo ""
        echo -e "${GREEN}🎉 ¡Proceso completado!${NC}"
        echo -e "${YELLOW}Todos los productos ahora tienen creator_user_id = 6${NC}"
    else
        echo -e "${RED}❌ Error aplicando actualización${NC}"
        exit 1
    fi
else
    echo "Operación cancelada"
fi
