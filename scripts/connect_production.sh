#!/bin/bash

# Script para conectar a la base de datos de producción
# Uso: ./connect_production.sh [comando]

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

echo -e "${BLUE}🔗 Conectando a la Base de Datos de Producción${NC}"
echo ""

# Mostrar información de conexión
echo -e "${YELLOW}📋 Información de Conexión:${NC}"
echo "Host: $DB_HOST"
echo "Database: $DB_NAME"
echo "User: $DB_USER"
echo ""

# Verificar si se proporcionó un comando
if [ $# -eq 0 ]; then
    echo -e "${YELLOW}Comandos disponibles:${NC}"
    echo "  status    - Ver estado de la base de datos"
    echo "  psql      - Conectar con psql interactivo"
    echo "  backup    - Crear backup de la base de datos"
    echo "  migrate   - Ejecutar migraciones"
    echo "  seed      - Ejecutar seeders"
    echo "  tinker    - Abrir Tinker de Laravel"
    echo ""
    echo "Ejemplo: ./connect_production.sh status"
    exit 1
fi

COMMAND=$1

case $COMMAND in
    "status")
        echo -e "${GREEN}📊 Verificando estado de la base de datos...${NC}"
        PGPASSWORD=$DB_PASS psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME -c "
        SELECT 
            schemaname,
            tablename,
            n_tup_ins as inserts,
            n_tup_upd as updates,
            n_tup_del as deletes
        FROM pg_stat_user_tables 
        ORDER BY schemaname, tablename;
        "
        ;;
    "psql")
        echo -e "${YELLOW}⚠️  Conectando a PRODUCCIÓN - Ten cuidado!${NC}"
        read -p "¿Continuar? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${GREEN}🔧 Abriendo psql...${NC}"
            echo "Comandos útiles:"
            echo "  \\dt - Listar tablas"
            echo "  \\d table_name - Ver estructura de tabla"
            echo "  \\q - Salir"
            echo ""
            PGPASSWORD=$DB_PASS psql -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME
        else
            echo "Conexión cancelada"
        fi
        ;;
    "backup")
        echo -e "${GREEN}💾 Creando backup de la base de datos...${NC}"
        TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
        FILENAME="backup_production_${TIMESTAMP}.sql"
        
        PGPASSWORD=$DB_PASS pg_dump -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME > $FILENAME
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✅ Backup creado exitosamente: $FILENAME${NC}"
            ls -lh $FILENAME
        else
            echo -e "${RED}❌ Error creando backup${NC}"
        fi
        ;;
    "migrate")
        echo -e "${YELLOW}⚠️  Ejecutando migraciones en PRODUCCIÓN!${NC}"
        read -p "¿Estás seguro? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${GREEN}🔄 Ejecutando migraciones...${NC}"
            php artisan db:production migrate
        else
            echo "Operación cancelada"
        fi
        ;;
    "seed")
        echo -e "${YELLOW}⚠️  Ejecutando seeders en PRODUCCIÓN!${NC}"
        read -p "¿Estás seguro? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${GREEN}🌱 Ejecutando seeders...${NC}"
            php artisan db:production seed
        else
            echo "Operación cancelada"
        fi
        ;;
    "tinker")
        echo -e "${YELLOW}⚠️  Abriendo Tinker en PRODUCCIÓN!${NC}"
        read -p "¿Estás seguro? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            echo -e "${GREEN}🔧 Abriendo Tinker...${NC}"
            php artisan db:production tinker
        else
            echo "Operación cancelada"
        fi
        ;;
    *)
        echo -e "${RED}❌ Comando no válido: $COMMAND${NC}"
        echo "Comandos disponibles: status, psql, backup, migrate, seed, tinker"
        exit 1
        ;;
esac
