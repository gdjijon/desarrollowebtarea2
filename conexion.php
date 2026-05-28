<?php
$host = 'localhost';
$dbname = 'db_dweb_gjijon';
$username = 'root'; // Ajustar con las credenciales de tu hosting gratuito
$password = '';     // Ajustar con las credenciales de tu hosting gratuito

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Evitar emulación de prepares para mayor seguridad
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
}
?>