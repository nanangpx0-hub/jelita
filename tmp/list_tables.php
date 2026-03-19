<?php
require __DIR__ . '/../config/config.php';
global $pdo;
if ($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables in " . DB_NAME . ":\n";
        foreach ($tables as $t) echo "- $t\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
