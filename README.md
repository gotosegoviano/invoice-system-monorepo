# 🧾 Invoice System

Aplicación full-stack de gestión de facturas construida como monorepo, con cálculo dinámico de ítems mediante estrategias desacopladas siguiendo principios de **Clean Architecture** y **SOLID**.

---

## 🏗️ Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 10+ (PHP 8.x) |
| Frontend | Vue 3 + TypeScript + Vite |
| Base de datos | MySQL |
| Infraestructura | Docker + Docker Compose |
| CI/CD | GitHub Actions |
| Deploy Frontend | Vercel |
| Deploy Backend | Railway |

---

## 📁 Estructura del Monorepo

```
invoice-system/
│
├── backend/           # API REST en Laravel
├── frontend/          # Aplicación Vue 3
├── docker-compose.yml
└── README.md
```

> El enfoque monorepo simplifica el versionado, mantiene coherencia entre contratos de API y facilita las pruebas integradas.

---

## 🔧 Backend – Laravel

### Estructura por Dominio

```
app/
 └── Domain/
      └── Invoices/
           ├── Strategies/
           │    ├── ProductCalculationStrategy.php
           │    └── ServiceCalculationStrategy.php
           ├── Enums/
           └── Services/
```

### 🧠 Patrón Strategy

La lógica de cálculo está desacoplada mediante el **Patrón Strategy**:

- `ProductCalculationStrategy` – calcula totales de ítems tipo producto
- `ServiceCalculationStrategy` – calcula totales de ítems tipo servicio

**Beneficios:**
- Extender tipos de ítems sin modificar la lógica existente (Principio Open/Closed)
- Pruebas unitarias independientes por estrategia
- Sin condicionales extensos — soporta futuros tipos como `SubscriptionStrategy`, `DiscountedBundleStrategy`, etc.

### 🧪 Testing

Las pruebas unitarias cubren:

- Lógica de cálculo por estrategia
- Validación de descuentos e impuestos
- Resultados financieros determinísticos

```
Tests/Unit/Domain/Invoice/ProductCalculationStrategyTest.php
```

---

## 🎨 Frontend – Vue 3

### Funcionalidades

- Creación dinámica de facturas
- Agregar/eliminar ítems
- Tooltips contextuales (con posicionamiento corregido)
- Cálculo reactivo de totales
- Integración con la API mediante variables de entorno

### Configuración de Entorno

```env
VITE_API_URL=http://localhost:8000/api
```

---

## 🐳 Configuración Docker

Levanta el stack completo (backend + base de datos + frontend) con:

```bash
docker-compose up --build
```

La configuración incluye:

- **Backend**: imagen PHP con Composer install y optimizaciones para producción
- **Frontend**: etapa de build con Node y salida estática optimizada
- **Base de datos**: servicio MySQL

---

## 🚀 Cómo Correr el Proyecto

### Sin Docker

**Backend:**

```bash
cd backend
composer install
php artisan migrate
php artisan serve
```

**Frontend:**

```bash
cd frontend
npm install
npm run dev
```

### Con Docker

```bash
docker-compose up --build
```

---

## 🔁 CI/CD

El pipeline de GitHub Actions se encarga de:

1. Instalación de dependencias
2. Ejecución de tests
3. Build del proyecto
4. Preparación del deploy

---

## 🧩 Decisiones de Diseño

**¿Por qué estructura por Dominio en lugar de Services planos?**
Mejora la mantenibilidad, clarifica responsabilidades, evita God Services y facilita las pruebas unitarias.

**¿Por qué Patrón Strategy?**
Permite distintas lógicas de cálculo por tipo de ítem, habilita extensiones futuras y elimina condicionales en cascada.

**¿Por qué Monorepo?**
Simplifica la ejecución local, unifica el versionado y reduce la fricción durante la revisión del código.

---

## 📈 Mejoras Futuras

- Implementar caché de facturas
- Agregar autenticación JWT
- Agregar filtros avanzados
- Separar infraestructura en repositorio independiente
- Implementar DDD más estricto con Application Layer + Use Cases

---

## 📄 Licencia

Este proyecto fue desarrollado como prueba técnica, demostrando arquitectura limpia, principios SOLID, testing real y manejo completo del ciclo de despliegue.