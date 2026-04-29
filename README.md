# PHP Starter Kit v2

Starter kit PHP minimalista sin frameworks, diseñado como base reutilizable para aplicaciones reales en hosting compartido (cPanel / Apache).

Esta versión (v2) evoluciona respecto a v1 incorporando un módulo demo funcional (Users CRUD) y mejoras en la estructura de aplicación.

---

# 🧠 Diferencias entre v1 y v2

## 🟢 v1 — Infraestructura base

- MVC básico
- Router simple
- Dotenv
- PDO helper (db())
- BaseController (view/json/redirect)
- Views simples
- Sin ejemplos funcionales
- Sin módulos de aplicación

👉 v1 = base técnica reutilizable

---

## 🟡 v2 — Base + aplicación demo

Todo lo de v1 +:

### 📦 Módulo demo
- Users CRUD completo:
  - index
  - create
  - store
  - edit
  - update
  - delete

### ⚙️ Mejora de entorno
- Control de errores por APP_ENV
  - local → muestra errores
  - production → oculta errores

### 🧱 Estructura preparada para crecer
- src/Services/ (scaffold)
- storage/logs/ (logging preparado)
- .env.example completo

### 🧹 Limpieza del sistema
- eliminación de debug hardcodeado
- bootstrap más estable
- flujo de arranque ordenado

---

# 🧠 Filosofía del proyecto

Este proyecto NO es un framework.

Es un starter kit evolutivo:

- v1 → base técnica mínima
- v2 → base + ejemplo funcional real
- v3 → (futuro) capa de aplicación avanzada

---

# ⚙️ Requisitos

- PHP 8.1+
- Composer
- Apache con mod_rewrite habilitado
- MySQL / MariaDB

---

# 🚀 Instalación

```bash
git clone https://github.com/tuusuario/php-starter-kit-v2.git
cd php-starter-kit-v2
composer install

cp .env.example .env
```

---

# 🔐 Configuración .env

```env
APP_NAME=MyApp
APP_ENV=local
APP_BASE_PATH=

DB_HOST=localhost
DB_NAME=starter_app
DB_USER=root
DB_PASS=
```

---

# 🗄️ Base de datos

```sql
CREATE DATABASE starter_app;

USE starter_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```

---

# 🧠 Notas importantes

- v2 incluye un módulo demo (Users CRUD)
- No es un framework completo
- Está diseñado para ser extendido manualmente
