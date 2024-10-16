<?php
$host = 'bwbusqyou6y36nq07nen-mysql.services.clever-cloud.com';
$db = 'bwbusqyou6y36nq07nen';
$user = 'ui5erqq1nmgauixl';
$pass = 'am2w1jaNS2dIJMPwRZ0h';
$charset = 'utf8mb4';

// $host = 'localhost';
// $db = 'citizen';
// $user = 'root';
// $pass = '';
// $charset = 'utf8mb4';
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
