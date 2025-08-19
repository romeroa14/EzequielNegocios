# 🕐 Cronjob para Tasas BCV

Este sistema permite ejecutar automáticamente el comando `bcv:fetch-rates` cada 12 horas para obtener las tasas de cambio actualizadas del BCV.

## 📋 Archivos Creados

1. **`cronjobs/bcv_rates_cron`** - Archivo de configuración del cronjob
2. **`install_bcv_cron.sh`** - Script bash para gestionar el cronjob
3. **`app/Console/Commands/ManageBcvCron.php`** - Comando Artisan para gestionar el cronjob
4. **`BCV_CRONJOB_README.md`** - Este archivo de documentación

## 🚀 Instalación

### Opción 1: Usando el Script Bash

```bash
# Instalar el cronjob
./install_bcv_cron.sh install

# Verificar que se instaló correctamente
./install_bcv_cron.sh check

# Probar el comando
./install_bcv_cron.sh test
```

### Opción 2: Usando el Comando Artisan

```bash
# Instalar el cronjob
php artisan bcv:cron install

# Verificar que se instaló correctamente
php artisan bcv:cron check

# Probar el comando
php artisan bcv:cron test
```

## 📅 Configuración del Cronjob

El cronjob se ejecuta cada 12 horas con la siguiente configuración:

```bash
0 */12 * * * cd /var/www/html/ezequiel_negocios && php artisan bcv:fetch-rates --all >> /var/log/bcv_rates.log 2>&1
```

**Explicación:**
- `0 */12 * * *` = Cada 12 horas (a las 00:00 y 12:00)
- `cd /var/www/html/ezequiel_negocios` = Cambiar al directorio del proyecto
- `php artisan bcv:fetch-rates --all` = Ejecutar el comando con todas las tasas
- `>> /var/log/bcv_rates.log 2>&1` = Guardar la salida en el archivo de log

## 🛠️ Comandos Disponibles

### Script Bash (`./install_bcv_cron.sh`)

| Comando | Descripción |
|---------|-------------|
| `install` | Instalar el cronjob |
| `check` | Verificar cronjobs instalados |
| `remove` | Remover el cronjob |
| `test` | Probar el comando BCV |
| `logs` | Mostrar logs recientes |

### Comando Artisan (`php artisan bcv:cron`)

| Comando | Descripción |
|---------|-------------|
| `install` | Instalar el cronjob |
| `check` | Verificar cronjobs instalados |
| `remove` | Remover el cronjob |
| `test` | Probar el comando BCV |
| `logs` | Mostrar logs recientes |

## 📝 Logs

Los logs se guardan en `/var/log/bcv_rates.log` y contienen:

- Información de ejecución del comando
- Tasas obtenidas
- Errores si los hay
- Timestamps de cada ejecución

Para ver los logs:

```bash
# Usando el script
./install_bcv_cron.sh logs

# Usando el comando Artisan
php artisan bcv:cron logs

# Directamente
tail -f /var/log/bcv_rates.log
```

## 🔧 Personalización

### Cambiar la Frecuencia

Para cambiar la frecuencia de ejecución, edita el archivo `cronjobs/bcv_rates_cron`:

```bash
# Cada 6 horas
0 */6 * * * cd /var/www/html/ezequiel_negocios && php artisan bcv:fetch-rates --all >> /var/log/bcv_rates.log 2>&1

# Cada día a las 8:00 AM
0 8 * * * cd /var/www/html/ezequiel_negocios && php artisan bcv:fetch-rates --all >> /var/log/bcv_rates.log 2>&1

# Cada hora
0 * * * * cd /var/www/html/ezequiel_negocios && php artisan bcv:fetch-rates --all >> /var/log/bcv_rates.log 2>&1
```

### Cambiar el Archivo de Log

Para cambiar la ubicación del archivo de log, edita las variables en los scripts:

- En `install_bcv_cron.sh`: Cambia `LOG_FILE="/var/log/bcv_rates.log"`
- En `ManageBcvCron.php`: Cambia `$logFile = '/var/log/bcv_rates.log'`

## 🚨 Solución de Problemas

### El cronjob no se ejecuta

1. Verificar que cron esté activo:
   ```bash
   sudo systemctl status cron
   ```

2. Verificar los logs del sistema:
   ```bash
   sudo tail -f /var/log/syslog | grep CRON
   ```

3. Verificar permisos del archivo de log:
   ```bash
   sudo chown www-data:www-data /var/log/bcv_rates.log
   sudo chmod 644 /var/log/bcv_rates.log
   ```

### Error de permisos

Si hay errores de permisos, ejecuta:

```bash
sudo chown -R www-data:www-data /var/www/html/ezequiel_negocios
sudo chmod -R 755 /var/www/html/ezequiel_negocios
```

### El comando no funciona

1. Probar manualmente:
   ```bash
   php artisan bcv:fetch-rates --all
   ```

2. Verificar que el servicio BCV esté disponible:
   ```bash
   curl -I https://www.bcv.org.ve
   ```

## 📊 Monitoreo

Para monitorear que el cronjob esté funcionando correctamente:

1. **Verificar logs periódicamente:**
   ```bash
   tail -20 /var/log/bcv_rates.log
   ```

2. **Verificar la última ejecución:**
   ```bash
   grep "$(date +%Y-%m-%d)" /var/log/bcv_rates.log
   ```

3. **Verificar que las tasas se estén guardando en la base de datos:**
   ```bash
   php artisan tinker --execute="echo 'Últimas tasas: ' . App\Models\BcvRate::latest()->take(5)->pluck('rate', 'currency');"
   ```

## 🔄 Actualización

Para actualizar el cronjob después de cambios:

```bash
# Remover el cronjob actual
./install_bcv_cron.sh remove

# Instalar el nuevo cronjob
./install_bcv_cron.sh install
```

## 📞 Soporte

Si tienes problemas con el cronjob:

1. Revisa los logs: `/var/log/bcv_rates.log`
2. Verifica el estado del cron: `sudo systemctl status cron`
3. Prueba el comando manualmente: `php artisan bcv:fetch-rates --all`
4. Verifica los permisos de archivos y directorios
