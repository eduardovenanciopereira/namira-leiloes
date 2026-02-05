<?php
$config = parse_ini_file(__DIR__ . '/../../.env');

$host = $config["DB_HOST"];
$port = $config["DB_PORT"];
$db = $config["DB_NAME"];
$user = $config["DB_USER"];
$pass = $config["DB_PASS"];

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
?>