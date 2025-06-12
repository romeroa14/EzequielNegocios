# 🚀 URLs de Desarrollo - Sistema Compra-Venta

## 📍 Servidor Principal: http://127.0.0.1:8002

### 🌐 Frontend Público (No requiere autenticación)
- Página Principal: http://127.0.0.1:8002/
- Catálogo de Productos: http://127.0.0.1:8002/catalogo
- Directorio de Productores: http://127.0.0.1:8002/productores

### 🔐 Panel de Administración (Filament)
- Dashboard Admin: http://127.0.0.1:8002/admin
- Login Admin: http://127.0.0.1:8002/admin/login
- Gestión de Productos: http://127.0.0.1:8002/admin/products
- Gestión de Categorías: http://127.0.0.1:8002/admin/product-categories
- Gestión de Subcategorías: http://127.0.0.1:8002/admin/product-subcategories
- Gestión de Listados: http://127.0.0.1:8002/admin/product-listings
- Gestión de Personas: http://127.0.0.1:8002/admin/people

### 👤 Credenciales de Admin
```
Email: alfredoromerox15@gmail.com
Password: 12345
```

## 🔄 Cómo Usar Ambos Sistemas

### Para el Frontend:
1. Abre tu navegador en: http://127.0.0.1:8002/
2. Navega libremente sin necesidad de login
3. Explora productos, productores, etc.

### Para el Admin (Filament):
1. Abre una nueva pestaña: http://127.0.0.1:8002/admin
2. Inicia sesión con las credenciales de arriba
3. Gestiona productos, categorías, usuarios, etc.

## 🛠️ Comandos Útiles

```bash
# Iniciar servidor de desarrollo
php artisan serve --host=0.0.0.0 --port=8002

# Compilar assets (si cambias CSS/JS)
npm run build

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar cachés si hay problemas
php artisan route:clear && php artisan config:clear && php artisan view:clear
```

## 📊 Datos de Prueba Disponibles

- 3 Productores: Juan Pérez, María González, Carlos Rodríguez
- 6 Productos Base: Mango, Aguacate, Papaya, Lechuga, Espinaca, Maíz
- 14 Listados de Productos: Con diferentes precios, calidades y ubicaciones
- 6 Categorías: Frutas, Hortalizas, Granos y Cereales, etc.
- 14 Subcategorías: Frutas Tropicales, Verduras de Hoja, etc.

## 🎯 Flujo de Trabajo Recomendado

1. Desarrollo del Admin: Usar http://127.0.0.1:8002/admin para gestionar datos
2. Prueba del Frontend: Usar http://127.0.0.1:8002/ para ver cómo se muestran los datos
3. Iteración: Cambiar datos en admin, refrescar frontend para ver cambios 

## 📋 Solución de Problemas

### Si las rutas de admin no funcionan:
```bash
# Limpiar todas las cachés
php artisan route:clear && php artisan config:clear && php artisan view:clear && php artisan cache:clear

# Verificar rutas de Filament
php artisan route:list | grep admin

# Reiniciar servidor
php artisan serve --host=0.0.0.0 --port=8002
``` 