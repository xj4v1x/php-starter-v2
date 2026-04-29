<h1>Users</h1>

<a href="<?= url('/users/create') ?>">Crear usuario</a>

<ul>
<?php foreach ($users as $user): ?>
    <li>
        <?= htmlspecialchars($user['name']) ?> - <?= htmlspecialchars($user['email']) ?>
        <a href="<?= url('/users/edit?id=' . (int) $user['id']) ?>">Editar</a>
        <a href="<?= url('/users/delete?id=' . (int) $user['id']) ?>">Eliminar</a>
    </li>
<?php endforeach; ?>
</ul>