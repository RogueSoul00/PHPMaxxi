<?php
// Database connection settings
$host = 'localhost';
$dbname = 'MaxxiDatabase';
$username = 'root';
$password = '';

try {
    // Connecting to the database with PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}

// Table name
$tabela = 'tabela1';

try {
    // SQL query to get the data
    $sql = "SELECT * FROM $tabela";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Get the results
    $linhas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>
