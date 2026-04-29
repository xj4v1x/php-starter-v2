<?php

namespace App\Models;

class User {

    public static function all() {
        return db()->query("SELECT * FROM users")->fetchAll(\PDO::FETCH_ASSOC);
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