<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=megaazmoon_test', 'root', '');
    $stmt = $pdo->query('SELECT migration FROM migrations');
    echo "Executed migrations in megaazmoon_test:" . PHP_EOL;
    while ($row = $stmt->fetch()) {
        echo $row['migration'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
