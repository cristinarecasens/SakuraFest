<?php
// Carreguem les variables del fitxer .env
$env = parse_ini_file(__DIR__ . '/.env');

// Configuració BBDD
$host    = $env['DB_HOST'];
$db      = $env['DB_NAME'];
$user    = $env['DB_USER'];
$pass    = $env['DB_PASS'];

// Google Maps API Key
$google_maps_key = $env['GOOGLE_MAPS_KEY'];

$charset = 'utf8';
$dsn = "mysql:host=$host;port=3307;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error de connexió: " . $e->getMessage());
}
