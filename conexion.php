<?php
$host = "192.168.1.210";
$user = "hospitaldb";
$password = "Sixvegas12"; // Usa tu contraseña si le pusiste una
$database = "hospitaldb"; // Asegúrate que este nombre sea correcto

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
