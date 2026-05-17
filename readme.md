#  Pastelería IP — Guía de Configuración

##  Proyecto Completado

Se ha completado la estructura completa de tu sistema de gestión de pedidos de pastelería.

###  Archivos Creados/Actualizados

#### Modelos
- `modelo/Pedidos.php` — CRUD completo de pedidos
- `modelo/PedidosPersonalizados.php` — CRUD completo de pedidos personalizados
- `modelo/Usuario.php` — Ya existía, verifica password_hash

#### Controladores
- `controlador/pedidosController.php` — Actualizado con métodos CRUD
- `controlador/pedidosPersonalizadosController.php` — Nuevo, funcional completo

#### Vistas
- `vista/pedidos.php` — Actualizado con estructura similar a stock.php
- `vista/pedidosPersonalizados.php` — Actualizado con estructura completa

#### Inicialización
- `setup.php` — **IMPORTANTE:** Ejecutar primero para inicializar la BD

---

## PASOS PARA ACTIVAR

### 1️⃣ Ejecutar el Setup
```
1. Abre tu navegador
2. Ve a: http://localhost/Pasteleria/setup.php
3. Espera a ver el mensaje ✓ ¡Base de datos inicializada correctamente!
4. Puedes eliminar setup.php después de ejecutarlo (opcional)
```

### 2️ Acceder al Sistema
```
URL: http://localhost/Pasteleria/vista/login.php

Usuarios disponibles:
┌─────────────┬───────────┬─────────────┐
│ Rol         │ Usuario   │ Contraseña  │
├─────────────┼───────────┼─────────────┤
│ Administrador│ admin     │ admin123    │
│ Empleado    │ empleado  │ empleado123 │
└─────────────┴───────────┴─────────────┘
```

---

## Características Implementadas

### ✓ Autenticación
- Login con contraseñas hasheadas (PASSWORD_BCRYPT)
- Sesiones seguras
- Validación de roles (admin/empleado)

### ✓ Módulo de Pedidos Normales
- Crear pedidos de servicios predefinidos
- Listar mis pedidos con estado
- Ver detalles completos
- Cambiar estado (pendiente, en proceso, listo, entregado, cancelado)

### ✓ Módulo de Pedidos Personalizados
- Solicitar pedidos personalizados
- Especificar tamaño, presupuesto, fecha
- Listar todas las solicitudes
- Estados: pendiente_revisión, aprobado, en_proceso, listo, entregado, cancelado

### ✓ Gestión de Stock
- Ver vitrina con productos disponibles
- Filtrar por categoría
- Ver precios

---

##  Estructura de Base de Datos

```sql
Tablas creadas automáticamente:
├── trabajadores (usuarios)
├── servicios (servicios predefinidos)
├── stock (vitrina de productos)
├── pedidos (pedidos normales)
└── pedidos_personalizados (pedidos customizados)
```

---

##  Configuración Importante

### config/database.php
```php
$host    = 'localhost';
$db      = 'pasteleria_sistema_';
$user    = 'root';
$password = '';  // Sin contraseña (XAMPP por defecto)
```

Si tu MySQL tiene contraseña, actualiza este archivo.

---

##  Solución de Problemas

###  "Error al conectar a la base de datos"
- Verifica que MySQL esté corriendo en XAMPP
- Revisa la contraseña en `config/database.php`
- Asegúrate de estar en la carpeta correcta

###  "Usuario o contraseña incorrectos" después de setup.php
- Ejecuta `setup.php` nuevamente
- Las contraseñas son: admin123 / empleado123
- Las contraseñas están hasheadas en la BD

### "Error al registrar pedido"
- Verifica que la fecha de entrega sea posterior a hoy
- Asegúrate de haber seleccionado un servicio válido

---


## Próximas Mejoras Opcionales

- [ ] Panel de administrador para gestionar pedidos
- [ ] Envío de correos de confirmación
- [ ] Sistema de reportes/ganancias
- [ ] Carga de archivos (fotos de diseños)
- [ ] API REST para integraciones
- [ ] Sistema de notificaciones
- [ ] Validaciones avanzadas

