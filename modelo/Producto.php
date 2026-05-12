<?php
require_once '../config/database.php';

class producto_regional extends Database {

    public function producto_regional() {
        $sql = "SELECT * FROM producto_Regional";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>