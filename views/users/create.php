<h1>Crear usuario</h1>

<a href="<?= url('/users') ?>">← Volver</a>

<form method="POST" action="<?= url('/users/store') ?>">
    <input name="name" placeholder="Nombre" required>
    <input name="email" type="email" placeholder="Email" required>
    <button type="submit">Guardar</button>
</form>
