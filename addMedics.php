<?php
include 'conexion.php'; // Este archivo debe contener la conexión a la BD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que todos los parámetros estén presentes
    if (
        isset($_POST['name']) &&
        isset($_POST['lastname']) &&
        isset($_POST['speciality']) &&
        isset($_POST['phone']) &&
        isset($_POST['email']) &&
        isset($_POST['password'])
    ) {
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $speciality = $_POST['speciality'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password']; // Considerar encriptar

        $sql = "INSERT INTO medicos (nombre, apellido, especialidad, telefono, email, password)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $lastname, $speciality, $phone, $email, $password);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $stmt->close();
    } else {
        echo "missing_params";
    }

    $conn->close();
} else {
    echo "invalid_method";
}
?>
