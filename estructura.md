# Arquitectura del Proyecto — Pastelería IP

##  Estructura MVC

```
┌─────────────────────────────────────────────────────────┐
│                      VISTA (vista/)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │ login.php    │  │ home.php     │  │ stock.php    │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
│  ┌──────────────────────┐  ┌──────────────────────────┐  │
│  │ pedidos.php          │  │ pedidosPersonalizados.php│  │
│  └──────────────────────┘  └──────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↑
                    (require_once)
                          ↓
┌─────────────────────────────────────────────────────────┐
│                 CONTROLADOR (controlador/)               │
│  ┌──────────────────┐  ┌──────────────────────────────┐ │
│  │ authController   │  │ stockController              │ │
│  └──────────────────┘  └──────────────────────────────┘ │
│  ┌──────────────────────┐  ┌──────────────────────────┐  │
│  │ pedidosController    │  │ pedidosPersonalizados     │  │
│  │                      │  │ Controller               │  │
│  └──────────────────────┘  └──────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ↑
                    (usar nuevos)
                          ↓
┌─────────────────────────────────────────────────────────┐
│                    MODELO (modelo/)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │ Usuario      │  │ Stock        │  │ Pedidos      │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
│  ┌──────────────────────────────────────────────────┐    │
│  │ PedidosPersonalizados                            │    │
│  └──────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
                          ↑
                     (PDO queries)
                          ↓
┌─────────────────────────────────────────────────────────┐
│              BASE DE DATOS (MySQL)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │ trabajadores │  │ servicios    │  │ stock        │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
│  ┌──────────────────┐  ┌──────────────────────────────┐  │
│  │ pedidos          │  │ pedidos_personalizados       │  │
│  └──────────────────┘  └──────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 Flujo de Datos — Ejemplo: Crear un Pedido

```
Usuario
   ↓
[pedidos.php - Formulario]
   ↓
POST → pedidosController::registrar()
   ↓
Pedidos::crear() [Modelo]
   ↓
INSERT INTO pedidos (SQL)
   ↓
Base de Datos [MySQL]
   ↓
Retorna true/false
   ↓
Mensaje de éxito/error
   ↓
Actualiza lista de pedidos
   ↓
Usuario ve cambios
```

---

##  Árbol de Directorios Actualizado

```
Pasteleria/
├── index.php                          (Redirige a login)
├── setup.php                           EJECUTAR PRIMERO
├── README.md                          (Este archivo)
│
├── config/
│   └── database.php                  (Conexión MySQL)
│
├── modelo/
│   ├── Usuario.php                   (Auth)
│   ├── Stock.php                     (Vitrina)
│   ├── Pedidos.php                    NUEVO
│   └── PedidosPersonalizados.php      NUEVO
│
├── controlador/
│   ├── authController.php            (Login/Logout)
│   ├── stockController.php           (Vitrina)
│   ├── pedidosController.php         (Actualizado)
│   └── pedidosPersonalizadosController.php   NUEVO
│
├── vista/
│   ├── login.php                     (Acceso)
│   ├── logout.php                    (Salida)
│   ├── home.php                      (Dashboard)
│   ├── stock.php                     (Vitrina)
│   ├── pedidos.php                   (Actualizado)
│   └── pedidosPersonalizados.php     (Actualizado)
│
└── activos/
    ├── css/
    │   ├── home.css
    │   ├── login.css
    │   ├── pedidos.css
    │   └── stock.css
    └── js/
        └── app.js
```

---

##  Flujo de Autenticación

```
┌─────────────────────┐
│ usuario/contraseña  │
└──────────┬──────────┘
           ↓
    [login.php]
           ↓
 authController::login()
           ↓
 Usuario::validar() [Modelo]
           ↓
 password_verify() ← (Contraseña hasheada)
           ↓
    ¿Válido?
    /      \
  SÍ        NO
  ↓         ↓
SET     Error
SESSION  Msg
  ↓
[home.php]
```

---

## 📋 Relaciones en Base de Datos

```
trabajadores (1)
    ↑
    │ id_trabajador
    │
    ├─── (n) pedidos
    │        ├─ cantidad
    │        ├─ fecha_entrega
    │        ├─ estado
    │        └─ → servicios
    │
    └─── (n) pedidos_personalizados
             ├─ presupuesto
             ├─ tamaño
             └─ estado

servicios (1)
    ↑
    │ id_servicio
    │
    └─── (n) pedidos [asociación]

stock (independiente)
    ├─ nombre
    ├─ categoría
    └─ precio
```

---

##  Funcionalidades por Módulo

###  Autenticación
- `authController.php` + `Usuario.php`
- Login con hash BCRYPT
- Sesiones seguras
- Logout

###  Stock / Vitrina
- `stockController.php` + `Stock.php`
- Ver productos disponibles
- Filtrar por categoría
- Ver precios

###  Pedidos Normales
- `pedidosController.php` + `Pedidos.php`
- Crear pedidos de servicios
- Listar mis pedidos
- Ver estado
- Cambiar estado (admin)

###  Pedidos Personalizados
- `pedidosPersonalizadosController.php` + `PedidosPersonalizados.php`
- Solicitar pedidos custom
- Especificar detalles
- Cambiar estado
- Listar solicitudes

---

## Métodos Principales

### Pedidos
```php
$ctrl->obtenerMisPedidos($id_usuario)      // Mis pedidos
$ctrl->obtenerTodos()                       // Todos (admin)
$ctrl->registrar(...)                       // Crear nuevo
$ctrl->actualizar(...)                      // Modificar
$ctrl->cambiarEstado(...)                   // Cambiar estado
$ctrl->eliminar(...)                        // Borrar
```

### PedidosPersonalizados
```php
$ctrl->obtenerMisPedidos($id_usuario)      // Mis pedidos
$ctrl->obtenerTodos()                       // Todos (admin)
$ctrl->registrar(...)                       // Solicitar nuevo
$ctrl->actualizar(...)                      // Modificar
$ctrl->cambiarEstado(...)                   // Cambiar estado
$ctrl->eliminar(...)                        // Cancelar
```

---

##  Estados de Pedidos

### Pedidos Normales
- `pendiente` - Recién creado
- `en_proceso` - En elaboración
- `listo` - Completado
- `entregado` - Entregado al cliente
- `cancelado` - Cancelado

### Pedidos Personalizados
- `pendiente_revision` - Esperando aprobación
- `aprobado` - Aprobado por admin
- `en_proceso` - En elaboración
- `listo` - Completado
- `entregado` - Entregado
- `cancelado` - Cancelado

---

##  Datos de Prueba (setup.php)

```
✓ 2 usuarios creados
✓ 8 servicios disponibles
✓ 6 productos en stock
✓ BD completamente funcional
```

---

**Diagrama creado:** 17 de mayo de 2026
