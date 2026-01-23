<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=megaazmoon_test', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    echo "Tables in megaazmoon_test:" . PHP_EOL;
    while ($row = $stmt->fetch()) {
        echo $row[0] . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
