#!/bin/bash

# =====================================================
# SCRIPT PARA INSTALAR Y GESTIONAR CRONJOB DE BCV
# =====================================================

PROJECT_DIR="/var/www/html/ezequiel_negocios"
CRON_FILE="cronjobs/bcv_rates_cron"
LOG_FILE="/var/log/bcv_rates.log"

echo "🔄 Configurando cronjob para tasas BCV..."

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio del proyecto Laravel."
    exit 1
fi

# Crear el archivo de log si no existe
sudo touch $LOG_FILE
sudo chown www-data:www-data $LOG_FILE
sudo chmod 644 $LOG_FILE

echo "✅ Archivo de log creado: $LOG_FILE"

# Función para instalar el cronjob
install_cronjob() {
    echo "📅 Instalando cronjob..."
    
    # Crear el cronjob temporal
    TEMP_CRON=$(mktemp)
    
    # Agregar el cronjob al archivo temporal
    cat > $TEMP_CRON << EOF
# =====================================================
# CRONJOB PARA OBTENER TASAS BCV CADA 12 HORAS
# =====================================================
# Instalado el $(date)
# Proyecto: $PROJECT_DIR
# =====================================================

# Ejecutar cada 12 horas (a las 00:00 y 12:00)
0 */12 * * * cd $PROJECT_DIR && php artisan bcv:fetch-rates --all >> $LOG_FILE 2>&1

EOF

    # Instalar el cronjob
    crontab $TEMP_CRON
    
    # Limpiar archivo temporal
    rm $TEMP_CRON
    
    echo "✅ Cronjob instalado exitosamente"
    echo "📋 El comando se ejecutará cada 12 horas"
    echo "📝 Los logs se guardarán en: $LOG_FILE"
}

# Función para verificar el cronjob
check_cronjob() {
    echo "🔍 Verificando cronjobs instalados..."
    echo "Cronjobs actuales:"
    crontab -l 2>/dev/null || echo "No hay cronjobs instalados"
}

# Función para remover el cronjob
remove_cronjob() {
    echo "🗑️ Removiendo cronjob..."
    crontab -r 2>/dev/null
    echo "✅ Cronjob removido"
}

# Función para probar el comando
test_command() {
    echo "🧪 Probando el comando BCV..."
    cd $PROJECT_DIR
    php artisan bcv:fetch-rates --all
}

# Función para ver logs
show_logs() {
    echo "📋 Mostrando últimos logs..."
    if [ -f "$LOG_FILE" ]; then
        tail -20 $LOG_FILE
    else
        echo "No se encontró el archivo de log: $LOG_FILE"
    fi
}

# Menú principal
case "$1" in
    "install")
        install_cronjob
        ;;
    "check")
        check_cronjob
        ;;
    "remove")
        remove_cronjob
        ;;
    "test")
        test_command
        ;;
    "logs")
        show_logs
        ;;
    *)
        echo "Uso: $0 {install|check|remove|test|logs}"
        echo ""
        echo "Comandos disponibles:"
        echo "  install  - Instalar el cronjob"
        echo "  check    - Verificar cronjobs instalados"
        echo "  remove   - Remover el cronjob"
        echo "  test     - Probar el comando BCV"
        echo "  logs     - Mostrar logs recientes"
        echo ""
        echo "Ejemplo: $0 install"
        exit 1
        ;;
esac
