# 🚀 PHP Starter Kit (v1)

Un starter kit PHP minimalista, estable y listo para desplegar en hosting compartido (cPanel / Apache).  
Diseñado para evitar reinventar la estructura en cada proyecto.

---

# 🧭 Objetivo

Este proyecto es una base reutilizable para aplicaciones PHP sin frameworks.

✔ Fácil de clonar  
✔ Fácil de desplegar  
✔ Sin build tools  
✔ Sin complejidad innecesaria  
✔ Compatible con hosting compartido  

---

# 🧱 Estructura

```text
project/
│
├── public/
│   ├── index.php
│   └── .htaccess
│
├── src/
│   ├── Controllers/
│   ├── Models/
│   ├── Core/
│   └── Services/
│
├── routes/
│   └── web.php
│
├── config/
├── storage/
├── vendor/
├── .env
├── composer.json
└── README.md

⚙️ Requisitos
PHP 8.1+
Composer
Apache / Nginx (o hosting compartido tipo cPanel)
MySQL / MariaDB (opcional)
📦 Instalación
1. Clonar proyecto
git clone https://github.com/tuusuario/php-starter-kit.git
cd php-starter-kit
2. Instalar dependencias
composer install
3. Configurar entorno

Crear archivo .env:

APP_NAME=MyApp
APP_ENV=local

DB_HOST=localhost
DB_NAME=test
DB_USER=root
DB_PASS=
4. Configurar servidor

El document root debe apuntar a:

/public
🌐 Routing

Las rutas se definen en:

/routes/web.php

Ejemplo:

return [
    [
        'path' => '/',
        'action' => 'HomeController@index'
    ],
];
🎮 Controllers

Ubicación:

/src/Controllers/

Ejemplo:

namespace App\Controllers;

class HomeController {

    public function index() {
        echo "Hello World";
    }
}
🧠 BaseController (opcional)

Se usa para reutilizar lógica común como:

render de vistas
JSON responses
redirects

Ubicación:

/src/Controllers/BaseController.php
🌐 Entry point

Archivo principal:

/public/index.php

Responsable de:

cargar Composer
cargar dotenv
iniciar router
ejecutar controllers
🔐 Seguridad básica
.env nunca se sube a producción pública
Prepared statements si se usa DB
Validar inputs
No exponer /src ni /vendor
🚀 Flujo de uso
Clonar el proyecto
Configurar .env
Añadir controllers
Añadir rutas
Desplegar en hosting
🧭 Filosofía del proyecto

Este starter kit sigue estas reglas:

✔ Minimalista
✔ Sin dependencias innecesarias
✔ Fácil de entender
✔ Fácil de desplegar
✔ Reutilizable en cualquier proyecto

📌 Objetivo final

Evitar repetir estructura base en cada proyecto PHP y empezar siempre desde una base estable.