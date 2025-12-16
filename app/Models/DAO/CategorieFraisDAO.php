<?php
namespace App\Models\DAO;
use App\Models\Entities\CategorieFrais;
use App\Config\Database;
use PDO;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Entities/CategorieFrais.php';

class CategorieFraisDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function findAll() {
        $sql = 'SELECT * FROM categories_frais ORDER BY type ASC';
        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $list = [];
        foreach ($rows as $r) {
            $list[] = new CategorieFrais($r['id'], $r['type'], $r['description']);
        }
        return $list;
    }

    public function findById($id) {
        $sql = 'SELECT * FROM categories_frais WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) return null;
        return new CategorieFrais($r['id'], $r['type'], $r['description']);
    }

    public function insert($type, $description) {
        $sql = 'INSERT INTO categories_frais (type, description) VALUES (?, ?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$type, $description]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $type, $description) {
        $sql = 'UPDATE categories_frais SET type = ?, description = ? WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$type, $description, $id]);
    }

    public function delete($id) {
        // Vérifier si la catégorie est utilisée
        $checkSql = 'SELECT COUNT(*) FROM details_frais WHERE categorie_id = ?';
        $checkStmt = $this->pdo->prepare($checkSql);
        $checkStmt->execute([$id]);
        $count = $checkStmt->fetchColumn();
        
        if ($count > 0) {
            return false; // Catégorie utilisée
        }
        
        $sql = 'DELETE FROM categories_frais WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function isUsed($id) {
        $sql = 'SELECT COUNT(*) FROM details_frais WHERE categorie_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}