<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;

class UserController extends BaseController
{
    public function index()
    {
        $users = User::all();
        $this->view('users/index', ['users' => $users]);
    }

    public function create()
    {
        $this->view('users/create');
    }

    public function store()
    {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name !== '' && $email !== '') {
            User::create(['name' => $name, 'email' => $email]);
        }

        $this->redirect(url('/users'));
    }

    public function edit()
    {
        $user = User::find((int) ($_GET['id'] ?? 0));
        $this->view('users/edit', ['user' => $user]);
    }

    public function update()
    {
        $id    = (int) ($_POST['id']    ?? 0);
        $name  = trim($_POST['name']    ?? '');
        $email = trim($_POST['email']   ?? '');

        if ($id > 0 && $name !== '' && $email !== '') {
            User::update($id, ['name' => $name, 'email' => $email]);
        }

        $this->redirect(url('/users'));
    }

    public function delete()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id > 0) {
            User::delete($id);
        }

        $this->redirect(url('/users'));
    }
}
