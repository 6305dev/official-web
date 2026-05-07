<?php

namespace App\Databases;

use PDO;
use Exception;

class SQLite
{
    private $db;

    public function __construct(string $dbPath)
    {
        if (!file_exists($dbPath)) {
            throw new Exception("Database file not found at: {$dbPath}");
        }

        try {
            $this->db = new PDO('sqlite:' . $dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            throw new Exception("Database connection error: " . $e->getMessage(), 0, $e);
        }
    }

    public function getConnection(): PDO
    {
        return $this->db;
    }

    public function create(string $table, array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function read(string $table, array $conditions = [], ?int $limit = null): array
    {
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY created DESC";
        if ($limit !== null) {
            $sql .= " LIMIT $limit";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($conditions);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(string $table, array $data, array $conditions): bool
    {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = :set_$key";
        }
        $where = [];
        foreach ($conditions as $key => $value) {
            $where[] = "$key = :where_$key";
        }
        $sql = "UPDATE $table SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $where);
        $params = [];
        foreach ($data as $key => $value) {
            $params["set_$key"] = $value;
        }
        foreach ($conditions as $key => $value) {
            $params["where_$key"] = $value;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(string $table, array $conditions): bool
    {
        $where = [];
        foreach ($conditions as $key => $value) {
            $where[] = "$key = :$key";
        }
        $sql = "DELETE FROM $table WHERE " . implode(" AND ", $where);
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($conditions);
    }
}
