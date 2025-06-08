<?php
include 'conexion.php'; // Este archivo debe contener la conexión a la BD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que se hayan enviado email y password
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "SELECT id, nombre, email, password FROM medicos WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Comparar contraseña directamente (si no está encriptada)
            if ($password == $row['password']) {
                echo json_encode([
                    "id" => $row['id'],
                    "nombre" => $row['nombre'],
                    "email" => $row['email']
                ]);
            } else {
                echo json_encode(["error" => "Contraseña incorrecta"]);
            }
        } else {
            echo json_encode(["error" => "Correo no registrado"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Faltan parámetros"]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
