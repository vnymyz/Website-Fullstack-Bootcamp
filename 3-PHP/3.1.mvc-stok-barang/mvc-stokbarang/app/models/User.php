<?php
require_once __DIR__ . '/../../core/Model.php';

class User extends Model {
    public function findByEmail(string $email): array|false {
        return $this->query('SELECT * FROM users WHERE email = ? LIMIT 1', [$email])->fetch();
    }

    public function findById(int $id): array|false {
        return $this->query('SELECT * FROM users WHERE id = ? LIMIT 1', [$id])->fetch();
    }
}
