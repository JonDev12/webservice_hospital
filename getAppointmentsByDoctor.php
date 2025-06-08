<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_medico'])) {
    $id_medico = $_GET['id_medico'];

    $sql = "SELECT p.nombre, c.motivo, c.estado, c.fecha
            FROM citas c
            INNER JOIN pacientes p ON p.id = c.id_paciente
            WHERE c.id_medico = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_medico);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = array();

    while ($row = $result->fetch_assoc()) {
        $fechaCompleta = $row["fecha"]; // Ej: "2025-04-15 14:30:00"
        $fecha = date("Y-m-d", strtotime($fechaCompleta)); // Solo fecha
        $hora = date("H:i", strtotime($fechaCompleta));    // Solo hora

        $appointments[] = array(
            "nombre" => $row["nombre"],
            "motivo" => $row["motivo"],
            "estado" => $row["estado"],
            "fecha" => $fecha,
            "hora" => $hora
        );
    }

    echo json_encode($appointments);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido o parámetro faltante"]);
}
