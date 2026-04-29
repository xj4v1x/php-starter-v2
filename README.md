# PHP Starter Kit

Starter kit PHP minimalista sin frameworks. Listo para desplegar en hosting compartido (cPanel / Apache).

## Requisitos

- PHP 8.1+
- Composer
- Apache con `mod_rewrite` habilitado
- MySQL / MariaDB

## Instalación

```bash
git clone https://github.com/tuusuario/php-starter-kit.git
cd php-starter-kit
composer install
```

Copia el archivo de entorno y configúralo:

```bash
cp .env.example .env
```

## Configuración `.env`

```env
APP_NAME=MyApp
APP_ENV=local
APP_BASE_PATH=        # vacío si el app está en la raíz del dominio
                      # ej: /php-starter/public si está en un subdirectorio

DB_HOST=localhost
DB_NAME=starter_app
DB_USER=root
DB_PASS=
```

`APP_BASE_PATH` controla el prefijo de todas las URLs generadas con `url()`. Déjalo vacío en producción si el document root apunta directamente a `/public`.

## Base de datos

```sql
CREATE DATABASE starter_app;

USE starter_app;

CREATE TABLE users (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```

## Estructura

```
project/
├── config/
│   ├── config.php      # helper url()
│   └── database.php    # helper db() — conexión PDO singleton
├── public/
│   ├── .htaccess       # rewrite all → index.php
│   └── index.php       # entry point: carga env, router, despacha controller
├── routes/
│   └── web.php         # definición de rutas (path + method + action)
├── src/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   └── UserController.php
│   ├── Core/
│   │   └── BaseController.php  # view(), json(), redirect()
│   ├── Models/
│   │   └── User.php
│   └── Services/
├── views/
│   ├── home.php
│   └── users/
│       ├── index.php
│       ├── create.php
│       └── edit.php
├── .env
└── composer.json
```

## Routing

Las rutas se definen en `routes/web.php`. Cada ruta requiere `path`, `method` y `action`:

```php
return [
    ['path' => '/',             'method' => 'GET',  'action' => 'HomeController@index'],
    ['path' => '/users',        'method' => 'GET',  'action' => 'UserController@index'],
    ['path' => '/users/store',  'method' => 'POST', 'action' => 'UserController@store'],
];
```

## Controllers

Extienden `BaseController` y tienen acceso a tres métodos:

```php
$this->view('users/index', ['users' => $users]); // renderiza views/users/index.php
$this->json(['ok' => true]);                      // respuesta JSON
$this->redirect(url('/users'));                   // redirect con basePath
```

## Helper `url()`

Genera URLs respetando el `APP_BASE_PATH` configurado en `.env`:

```php
url('/users')        // → /php-starter/public/users  (local)
url('/users')        // → /users                     (producción en raíz)
```

Úsalo en vistas y controllers para todos los `href`, `action` y redirects.

## Modelos

Los modelos usan el helper `db()` que devuelve una instancia PDO singleton. Todos los queries usan prepared statements:

```php
User::all();
User::find($id);
User::create(['name' => 'Ana', 'email' => 'ana@mail.com']);
User::update($id, ['name' => 'Ana', 'email' => 'ana@mail.com']);
User::delete($id);
```

## Despliegue en hosting compartido

1. Sube todos los archivos excepto `.env`
2. Crea el `.env` directamente en el servidor
3. Apunta el document root a la carpeta `/public`
4. Si el app vive en un subdirectorio, configura `APP_BASE_PATH` con ese prefijo
5. Verifica que `mod_rewrite` esté activo en Apache
