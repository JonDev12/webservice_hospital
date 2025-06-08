<?php
header("Content-Type: application/json");
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $id_medico = $_POST['id_medico'] ?? '';

    if ($nombre && $apellido && $fecha_nacimiento && $genero && $id_medico) {
        $stmt = $conn->prepare("INSERT INTO Pacientes (nombre, apellido, fecha_nacimiento, genero, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $apellido, $fecha_nacimiento, $genero, $telefono, $direccion);

        if ($stmt->execute()) {
            $id_paciente = $conn->insert_id;

            // Valores por defecto para historial médico
            $diagnostico = "Pendiente";
            $tratamiento = "Pendiente";
            $fecha = date('Y-m-d');

            $stmt2 = $conn->prepare("INSERT INTO historialmedico (id_paciente, fecha, diagnostico, tratamiento, id_medico) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("isssi", $id_paciente, $fecha, $diagnostico, $tratamiento, $id_medico);

            if ($stmt2->execute()) {
                echo json_encode(["status" => "success", "message" => "Paciente y relación médica agregados correctamente"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Paciente guardado, pero fallo al guardar historial médico"]);
            }

            $stmt2->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Error al agregar paciente"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Faltan datos obligatorios"]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}
