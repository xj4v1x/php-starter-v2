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

        header("Location: /users");
    }

    public function edit() {
        $user = User::find($_GET['id']);

        require __DIR__ . '/../../views/users/edit.php';
    }

    public function update() {
        User::update($_POST['id'], $_POST);

        header("Location: /users");
    }

    public function delete() {
        User::delete($_GET['id']);

        header("Location: /users");
    }
}