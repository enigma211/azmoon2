<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=megaazmoon_test', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('database/schema/mysql-schema.sql');
    
    // Using exec on the whole content can be tricky, but MariaDB/MySQL PDO usually supports it 
    // if the driver is configured. Alternatively, we can use the mysql command via system().
    
    $mysqlPath = 'C:\\xampp82\\mysql\\bin\\mysql.exe';
    $command = "$mysqlPath -u root megaazmoon_test < database/schema/mysql-schema.sql";
    
    // In Windows PowerShell/CMD, redirection works differently sometimes. 
    // Let's try exec() which runs in shell.
    system($command, $returnVar);
    
    if ($returnVar === 0) {
        echo "Schema loaded successfully into megaazmoon_test using mysql CLI." . PHP_EOL;
    } else {
        echo "Failed to load schema using mysql CLI. Return code: $returnVar" . PHP_EOL;
        
        // Fallback to PDO if CLI fails
        $pdo->exec($sql);
        echo "Attempted schema load via PDO exec." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
