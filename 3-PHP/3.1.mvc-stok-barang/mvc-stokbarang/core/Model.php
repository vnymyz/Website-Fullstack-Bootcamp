<?php
require_once __DIR__ . '/../config/database.php';

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
