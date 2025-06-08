<?php
header('Content-Type: application/json');
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = isset($_POST['patientId']) ? intval($_POST['patientId']) : null;
    $doctorId = isset($_POST['doctorId']) ? intval($_POST['doctorId']) : null;
    $dateTime = isset($_POST['date']) ? $_POST['date'] : null; // Formato: "YYYY-MM-DD HH:MM:SS"
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;

    if ($patientId && $doctorId && $dateTime && $reason) {
        $sql = "INSERT INTO Citas (id_paciente, id_medico, fecha, motivo)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $patientId, $doctorId, $dateTime, $reason);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Cita registrada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar la cita: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Parámetros incompletos."]);
    }

    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
