# ⌨️ Shortcuts de Cursor para Laravel

Este documento explica cómo configurar y usar shortcuts personalizados en Cursor para el desarrollo con Laravel.

## 📁 Archivos de Configuración

Los archivos de configuración están en la carpeta `.cursor/`:

- **`.cursor/snippets/laravel.json`**: Snippets de código para comandos frecuentes
- **`.cursor/keybindings.json`**: Atajos de teclado personalizados
- **`.cursor/settings.json`**: Configuración general del editor

## 🚀 Cómo Usar los Shortcuts

### 1. **Snippets (Fragmentos de Código)**

Escribe el prefijo y presiona `Tab` para expandir:

| Prefijo | Descripción | Comandos Incluidos |
|---------|-------------|-------------------|
| `laravel` | Comandos básicos de Laravel | `php artisan serve`, `migrate`, `db:seed`, etc. |
| `bcv` | Comandos de BCV y cronjobs | `bcv:fetch-rates`, `laravel-cloud:setup-cron`, etc. |
| `db` | Comandos de base de datos | `migrate`, `migrate:rollback`, `db:seed`, etc. |
| `prod` | Comandos de producción | `db:production`, `db:psql`, etc. |
| `cron` | Comandos de cronjobs | `crontab -l`, `systemctl status cron`, etc. |
| `git` | Comandos de Git | `git status`, `git add`, `git commit`, etc. |
| `composer` | Comandos de Composer | `composer install`, `composer update`, etc. |
| `node` | Comandos de Node.js | `npm install`, `npm run dev`, etc. |
| `debug` | Comandos de debugging | `tinker`, `route:list`, `make:command`, etc. |

### 2. **Keyboard Shortcuts (Atajos de Teclado)**

Estos atajos funcionan cuando el terminal está enfocado:

| Atajo | Comando |
|-------|---------|
| `Ctrl+Shift+L` | `php artisan serve --host=0.0.0.0 --port=8000` |
| `Ctrl+Shift+M` | `php artisan migrate` |
| `Ctrl+Shift+S` | `php artisan db:seed` |
| `Ctrl+Shift+C` | `php artisan config:clear && php artisan cache:clear` |
| `Ctrl+Shift+B` | `php artisan bcv:fetch-rates --all` |
| `Ctrl+Shift+R` | `php artisan schedule:run` |
| `Ctrl+Shift+P` | `php artisan laravel-cloud:setup-cron check` |
| `Ctrl+Shift+G` | `git status` |
| `Ctrl+Shift+N` | `npm run dev` |
| `Ctrl+Shift+D` | `composer install` |
| `Ctrl+/` | Comentar/descomentar línea |

## 🔧 Cómo Configurar en Cursor

### Opción 1: Copiar Archivos Manualmente

1. **Copiar los archivos** de `.cursor/` a tu proyecto
2. **Reiniciar Cursor** para que tome los cambios
3. **Verificar** que los snippets aparezcan al escribir los prefijos

### Opción 2: Configurar Globalmente

1. **Abrir Cursor**
2. **File > Preferences > Settings** (o `Ctrl+,`)
3. **Buscar "snippets"** y configurar globalmente
4. **File > Preferences > Keyboard Shortcuts** para keybindings

### Opción 3: Usar Command Palette

1. **Abrir Command Palette** (`Ctrl+Shift+P`)
2. **Buscar "Preferences: Configure User Snippets"**
3. **Seleccionar "New Global Snippets file"**
4. **Pegar el contenido** de `laravel.json`

## 📋 Ejemplos de Uso

### Ejemplo 1: Iniciar el Servidor
```
1. Abrir terminal en Cursor
2. Presionar Ctrl+Shift+L
3. El servidor se inicia automáticamente
```

### Ejemplo 2: Ejecutar Migraciones
```
1. Escribir "db" en el terminal
2. Presionar Tab
3. Seleccionar el comando de migración deseado
```

### Ejemplo 3: Verificar BCV
```
1. Presionar Ctrl+Shift+B
2. El comando BCV se ejecuta automáticamente
3. Verificar logs con Ctrl+Shift+P
```

### Ejemplo 4: Comentar Código
```
1. Seleccionar la línea o líneas a comentar
2. Presionar Ctrl+/
3. Las líneas se comentan automáticamente
4. Presionar Ctrl+/ nuevamente para descomentar
```

## 🎯 Comandos Más Frecuentes

### Desarrollo Diario
- `Ctrl+Shift+L` - Iniciar servidor
- `Ctrl+Shift+M` - Ejecutar migraciones
- `Ctrl+Shift+C` - Limpiar cache
- `Ctrl+Shift+G` - Verificar estado de Git
- `Ctrl+/` - Comentar/descomentar línea

### BCV y Cronjobs
- `Ctrl+Shift+B` - Ejecutar BCV
- `Ctrl+Shift+R` - Ejecutar scheduler
- `Ctrl+Shift+P` - Verificar cronjobs

### Base de Datos
- `db` + Tab - Ver comandos de DB
- `prod` + Tab - Ver comandos de producción

## 🔄 Personalización

### Agregar Nuevos Snippets

1. **Editar** `.cursor/snippets/laravel.json`
2. **Agregar** nueva entrada:
```json
"Nuevo Snippet": {
  "prefix": "nuevo",
  "body": [
    "// Tu comando aquí",
    "php artisan tu:comando"
  ],
  "description": "Descripción del snippet"
}
```

### Agregar Nuevos Keybindings

1. **Editar** `.cursor/keybindings.json`
2. **Agregar** nueva entrada:
```json
{
  "key": "ctrl+shift+x",
  "command": "workbench.action.terminal.sendSequence",
  "args": {
    "text": "tu-comando-aqui\n"
  },
  "when": "terminalFocus"
}
```

## 🛠️ Solución de Problemas

### Los snippets no aparecen
1. **Verificar** que el archivo esté en `.cursor/snippets/`
2. **Reiniciar** Cursor
3. **Verificar** la sintaxis JSON

### Los keybindings no funcionan
1. **Verificar** que el terminal esté enfocado
2. **Comprobar** que no haya conflictos con otros atajos
3. **Revisar** la sintaxis del archivo

### Configuración no se aplica
1. **Guardar** todos los archivos
2. **Reiniciar** Cursor completamente
3. **Verificar** permisos de archivos

## 📚 Recursos Adicionales

- [Documentación de Cursor](https://cursor.sh/docs)
- [VS Code Snippets](https://code.visualstudio.com/docs/editor/userdefinedsnippets)
- [VS Code Keybindings](https://code.visualstudio.com/docs/getstarted/keybindings)

## 🎉 ¡Listo!

Con estos shortcuts configurados, tu flujo de trabajo con Laravel será mucho más eficiente. Los comandos más frecuentes están a solo un atajo de teclado de distancia.
