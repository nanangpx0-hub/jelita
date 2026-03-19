<?php
require __DIR__ . '/../config/config.php';
global $pdo;
if ($pdo) {
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $tableMap = array_flip($tables);

        // Tampilkan schema aktif tanpa gagal saat instalasi masih memakai tabel legacy.
        foreach (['pendaftaran', 'pendaftaran_petugas', 'lowongan', 'materi_pelatihan', 'materi_bahan'] as $table) {
            if (!isset($tableMap[$table])) {
                echo "Table {$table}: tidak ada\n";
                continue;
            }

            $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
            echo "Table {$table}: {$count} row(s)\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "PDO not initialized.\n";
}
