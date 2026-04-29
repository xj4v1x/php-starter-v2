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

----------------------------------

----------------------------------

----------------------------------

----------------------------------

## 🧱 1. CREAR BASE DE DATOS

**En MySQL / MariaDB:**

```
CREATE DATABASE starter_app;
```


🧭 2. USAR LA BASE DE DATOS
        
    USE starter_app;
🧱 3. CREAR TABLA USERS

Ahora sí:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
);
🧠 4. CONECTARLO CON TU PROYECTO

En tu .env:

DB_HOST=localhost
DB_NAME=starter_app
DB_USER=root
DB_PASS=
⚠️ 5. COMPROBACIÓN RÁPIDA (MUY IMPORTANTE)

Antes de seguir con el CRUD, prueba esto:

En cualquier parte temporal:

var_dump(db()->query("SELECT 1")->fetch());

Si funciona:

✔ conexión OK
✔ credenciales OK
✔ PDO funcionando

🚀 CONSEJO IMPORTANTE (te ahorra dolores de cabeza)

Para este starter:

👉 usa siempre una BD por proyecto

Ejemplo:

starter_app
crm_app
blog_app
🧭 ESTADO ACTUAL

Después de esto ya tienes:

✔ base de datos creada
✔ conexión configurada
✔ listo para empezar CRUD

👉 Objetivo: que puedas clonar el proyecto y tener un patrón reutilizable real.

🧱 1. BASE DE DATOS

Crea tabla:

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
);

🧠 2. CONEXIÓN DB (simple y reutilizable)

📁 config/database.php

<?php

function db() {
    static $pdo;

    if (!$pdo) {
        $pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}
📦 3. MODELO

📁 src/Models/User.php

<?php

namespace App\Models;

class User {

    public static function all() {
        $stmt = db()->query("SELECT * FROM users");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $stmt = db()->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        return $stmt->execute([$data['name'], $data['email']]);
    }

    public static function update($id, $data) {
        $stmt = db()->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        return $stmt->execute([$data['name'], $data['email'], $id]);
    }

    public static function delete($id) {
        $stmt = db()->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }
}
🎮 4. CONTROLLER

📁 src/Controllers/UserController.php

<?php

namespace App\Controllers;

use App\Models\User;

class UserController {

    public function index() {
        $users = User::all();
        require __DIR__ . '/../../views/users/index.php';
    }

    public function create() {
        require __DIR__ . '/../../views/users/create.php';
    }

    public function store() {
        User::create($_POST);
        header('Location: /users');
    }

    public function edit() {
        $user = User::find($_GET['id']);
        require __DIR__ . '/../../views/users/edit.php';
    }

    public function update() {
        User::update($_POST['id'], $_POST);
        header('Location: /users');
    }

    public function delete() {
        User::delete($_GET['id']);
        header('Location: /users');
    }
}
🧭 5. ROUTES

📁 routes/web.php

return [

    ['path' => '/', 'action' => 'HomeController@index'],

    ['path' => '/users', 'action' => 'UserController@index'],
    ['path' => '/users/create', 'action' => 'UserController@create'],
    ['path' => '/users/store', 'action' => 'UserController@store'],
    ['path' => '/users/edit', 'action' => 'UserController@edit'],
    ['path' => '/users/update', 'action' => 'UserController@update'],
    ['path' => '/users/delete', 'action' => 'UserController@delete'],
];
🖼️ 6. VISTAS
📁 views/users/index.php
<h1>Users</h1>

<a href="/users/create">Crear usuario</a>

<ul>
<?php foreach ($users as $user): ?>

    <li>
        <?= $user['name'] ?> (<?= $user['email'] ?>)
        <a href="/users/edit?id=<?= $user['id'] ?>">Editar</a>
        <a href="/users/delete?id=<?= $user['id'] ?>">Eliminar</a>
    </li>

<?php endforeach; ?>

</ul>
📁 views/users/create.php
<h1>Crear usuario</h1>

<form method="POST" action="/users/store">
    <input name="name" placeholder="Nombre">
    <input name="email" placeholder="Email">
    <button>Guardar</button>
</form>
📁 views/users/edit.php
<h1>Editar usuario</h1>

<form method="POST" action="/users/update">
    <input type="hidden" name="id" value="<?= $user['id'] ?>">
    <input name="name" value="<?= $user['name'] ?>">
    <input name="email" value="<?= $user['email'] ?>">
    <button>Actualizar</button>
</form>
🚀 7. PRUEBA FINAL

Abre:

/users

Y deberías poder:

✔ ver usuarios
✔ crear
✔ editar
✔ eliminar

🧠 RESULTADO

Ahora tu starter ya no es solo estructura:

🔥 Es un starter PHP funcional con ejemplo CRUD real

💡 IMPORTANTE (mentalidad correcta)

Este CRUD NO es para producción directa.

Es para:

👉 copiar patrón
👉 adaptar rápido
👉 no pensar desde cero