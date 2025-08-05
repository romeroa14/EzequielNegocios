#!/bin/bash

# Script para configurar el cron job de actualización de tasas de cambio
# Este script debe ejecutarse una sola vez para configurar el cron

PROJECT_PATH="/var/www/html/sistema-compraventa/sistema-compraventa"
SCRIPT_PATH="$PROJECT_PATH/scripts/update_exchange_rates.php"
LOG_PATH="$PROJECT_PATH/storage/logs/cron_exchange_rates.log"

echo "Configurando cron job para actualización de tasas de cambio..."

# Crear el cron job que se ejecuta todos los días a las 8:00 AM
CRON_JOB="0 8 * * * cd $PROJECT_PATH && /usr/bin/php $SCRIPT_PATH update >> $LOG_PATH 2>&1"

# Agregar el cron job al crontab del usuario actual
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo "Cron job configurado exitosamente!"
echo "El script se ejecutará todos los días a las 8:00 AM"
echo "Logs se guardarán en: $LOG_PATH"
echo ""
echo "Para verificar que el cron se configuró correctamente, ejecuta:"
echo "crontab -l"
echo ""
echo "Para probar el script manualmente:"
echo "cd $PROJECT_PATH && php scripts/update_exchange_rates.php update"
echo ""
echo "Para ver el estado actual de las tasas:"
echo "cd $PROJECT_PATH && php scripts/update_exchange_rates.php status" 