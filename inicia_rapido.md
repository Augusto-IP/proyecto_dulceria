#  INICIO RÁPIDO — Pastelería IP

## 1️ Ejecuta el Setup (UNA SOLA VEZ)

```
http://localhost/Pasteleria/setup.php
```

Deberías ver: ✓ ¡Base de datos inicializada correctamente!

---

## 2️⃣ Login con uno de estos usuarios

```
 ADMIN
Usuario: admin
Contraseña: admin123

 EMPLEADO
Usuario: empleado
Contraseña: empleado123
```

URL: http://localhost/Pasteleria/vista/login.php

---

## 3️⃣ Navega por el sistema

- **Inicio** — Dashboard principal
- **Pedidos** — Crear y gestionar pedidos normales
- **Personalizados** — Crear y gestionar pedidos customizados
- **Vitrina** — Ver productos en stock

---

## Funciones

###  Crear Pedido Normal
1. Ir a "Pedidos"
2. Seleccionar servicio
3. Indicar cantidad y fecha de entrega
4. Registrar

### Crear Pedido Personalizado
1. Ir a "Personalizados"
2. Llenar nombre, descripción y tamaño
3. Indicar presupuesto y fecha
4.  Solicitar

### 👁️ Ver Pedidos
- Aparecen en la tabla debajo del formulario
- Se actualiza automáticamente

---

##  Si hay problemas

### "Error al conectar"
→ Verifica que MySQL esté corriendo

###  "Usuario incorrecto"
→ Vuelve a ejecutar setup.php

###  "Error al registrar"
→ Llena todos los campos (*)
→ Fecha debe ser futura

---

## Archivos Importantes

| Archivo | Función |
|---------|---------|
| `setup.php` | Inicializar BD |
| `vista/login.php` |  Login |
| `vista/home.php` |  Dashboard |
| `vista/pedidos.php` |  Pedidos |
| `vista/pedidosPersonalizados.php` |  Personalizados |
| `vista/stock.php` |  Vitrina |
| `README.md` |  Documentación |
| `ARQUITECTURA.md` |  Diseño del sistema |

---

##  Verificación Rápida

Después de login, deberías ver:
- ✓ Header con navegación
- ✓ Bienvenida con tu nombre
- ✓ Enlaces a todas las secciones
- ✓ Botón Salir funcional

---

