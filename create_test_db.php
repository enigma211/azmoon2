<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS megaazmoon_test');
    echo "Database megaazmoon_test created or already exists." . PHP_EOL;
    
    $stmt = $pdo->query('SHOW DATABASES');
    while ($row = $stmt->fetch()) {
        echo $row[0] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
