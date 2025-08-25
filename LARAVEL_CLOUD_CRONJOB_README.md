# ☁️ Cronjob para Laravel Cloud (Producción)

Este documento explica cómo configurar y gestionar cronjobs en Laravel Cloud para el comando `bcv:fetch-rates`.

## 🔍 Problema Identificado

En Laravel Cloud, los cronjobs se manejan de manera diferente que en desarrollo local. El sistema ya tiene configurado el scheduler de Laravel en `app/Console/Kernel.php`, pero necesita un cronjob que ejecute `php artisan schedule:run` cada minuto.

## 📋 Configuración Actual

### Scheduler de Laravel (app/Console/Kernel.php)

Ya tienes configurado:

```php
// Actualizar tasas de cambio cada hora
$schedule->call(function () {
    // Ejecuta bcv:fetch-rates --all
})->hourly()->name('update-exchange-rates');

// Backup diario a las 7:00 AM
$schedule->call(function () {
    // Ejecuta bcv:fetch-rates --all
})->dailyAt('07:00')->name('daily-rates-backup');

// Heartbeat cada 10 minutos
$schedule->call(function () {
    // Log de verificación
})->everyTenMinutes()->name('scheduler-heartbeat');
```

## 🚀 Instalación en Laravel Cloud

### Opción 1: Usando el Comando Artisan

```bash
# Instalar el cronjob del scheduler
php artisan laravel-cloud:setup-cron install

# Verificar que se instaló correctamente
php artisan laravel-cloud:setup-cron check

# Probar el scheduler
php artisan laravel-cloud:setup-cron test
```

### Opción 2: Configuración Manual

Si necesitas configurar manualmente el cronjob:

```bash
# Editar el crontab
crontab -e

# Agregar esta línea:
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📅 Frecuencia de Ejecución

Con esta configuración:

- **Scheduler**: Se ejecuta cada minuto
- **BCV Rates**: Se ejecuta cada hora
- **Backup diario**: Se ejecuta a las 7:00 AM
- **Heartbeat**: Se ejecuta cada 10 minutos

## 🛠️ Comandos Disponibles

### Comando Laravel Cloud (`php artisan laravel-cloud:setup-cron`)

| Comando | Descripción |
|---------|-------------|
| `install` | Instalar el cronjob del scheduler |
| `check` | Verificar cronjobs instalados |
| `remove` | Remover el cronjob del scheduler |
| `test` | Probar el scheduler |

### Comandos de Verificación

```bash
# Verificar cronjobs instalados
crontab -l

# Verificar logs del scheduler
tail -f storage/logs/laravel.log | grep -E "(HEARTBEAT|BCV|TASAS)"

# Ejecutar scheduler manualmente
php artisan schedule:run

# Ver comandos programados
php artisan schedule:list
```

## 📝 Logs y Monitoreo

### Logs del Scheduler

Los logs se guardan en `storage/logs/laravel.log` y contienen:

- **HEARTBEAT**: Verificación de que el scheduler funciona
- **BCV/TASAS**: Ejecución del comando de tasas
- **ERROR**: Errores en la ejecución

### Verificar Logs

```bash
# Ver logs recientes del scheduler
php artisan laravel-cloud:setup-cron test

# Ver logs específicos
grep "HEARTBEAT" storage/logs/laravel.log
grep "BCV" storage/logs/laravel.log
grep "TASAS" storage/logs/laravel.log
```

## 🔧 Solución de Problemas

### El scheduler no se ejecuta

1. **Verificar que el cronjob esté instalado:**
   ```bash
   crontab -l
   ```

2. **Verificar que el servicio cron esté activo:**
   ```bash
   sudo systemctl status cron
   ```

3. **Probar el scheduler manualmente:**
   ```bash
   php artisan schedule:run
   ```

### No se ven logs

1. **Verificar permisos de logs:**
   ```bash
   sudo chown -R www-data:www-data storage/logs
   sudo chmod -R 755 storage/logs
   ```

2. **Verificar configuración de logs en .env:**
   ```env
   LOG_CHANNEL=daily
   LOG_LEVEL=info
   ```

### El comando BCV falla

1. **Probar manualmente:**
   ```bash
   php artisan bcv:fetch-rates --all
   ```

2. **Verificar conectividad:**
   ```bash
   curl -I https://www.bcv.org.ve
   ```

## 📊 Monitoreo en Laravel Cloud

### Verificar Estado

1. **En el dashboard de Laravel Cloud:**
   - Ve a la sección "Commands"
   - Ejecuta `php artisan laravel-cloud:setup-cron check`
   - Ejecuta `php artisan laravel-cloud:setup-cron test`

2. **Verificar logs:**
   - Ve a la sección "Logs"
   - Busca entradas con "HEARTBEAT", "BCV", "TASAS"

### Alertas

Configura alertas para:
- Logs de error en el scheduler
- Falta de ejecución del heartbeat
- Errores en el comando BCV

## 🔄 Actualización

Para actualizar la configuración:

1. **Modificar el scheduler:**
   - Edita `app/Console/Kernel.php`
   - Agrega o modifica comandos programados

2. **Reiniciar el cronjob:**
   ```bash
   php artisan laravel-cloud:setup-cron remove
   php artisan laravel-cloud:setup-cron install
   ```

## 📞 Soporte

Si tienes problemas:

1. Verifica los logs: `storage/logs/laravel.log`
2. Ejecuta: `php artisan laravel-cloud:setup-cron test`
3. Verifica el crontab: `crontab -l`
4. Prueba manualmente: `php artisan schedule:run`

## 🎯 Resumen

- ✅ El scheduler está configurado en `app/Console/Kernel.php`
- ✅ El comando BCV se ejecuta cada hora automáticamente
- ✅ Los logs se guardan en `storage/logs/laravel.log`
- ✅ El heartbeat verifica que todo funcione cada 10 minutos
- 🔧 Solo necesitas instalar el cronjob: `php artisan laravel-cloud:setup-cron install`
