<?php
// $host = 'b7gbc7flaugenhm21xei-mysql.services.clever-cloud.com';
// $db = 'b7gbc7flaugenhm21xei';
// $user = 'uhylskz8zf8aqxqd';
// $pass = 'PGpJXMYpzvF2Kl3pdEff';
// $charset = 'utf8mb4';

$host = 'localhost';
$db = 'citizen';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

return $pdo;
?>
