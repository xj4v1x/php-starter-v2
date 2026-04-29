<h1>Editar usuario</h1>

<a href="<?= url('/users') ?>">← Volver</a>

<form method="POST" action="<?= url('/users/update') ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
    <input name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
    <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <button type="submit">Actualizar</button>
</form>
