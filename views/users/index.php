<h1>Users</h1>

<a href="/users/create">Crear usuario</a>

<ul>
<?php foreach ($users as $user): ?>
    <li>
        <?= $user['name'] ?> - <?= $user['email'] ?>
        <a href="/users/edit?id=<?= $user['id'] ?>">Editar</a>
        <a href="/users/delete?id=<?= $user['id'] ?>">Eliminar</a>
    </li>
<?php endforeach; ?>
</ul>