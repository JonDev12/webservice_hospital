<?php
include 'conexion.php'; // Asegúrate que $conn esté definido aquí (mysqli)

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_medico'])) {
        $id_medico = $_GET['id_medico'];

        $sql = "SELECT COUNT(CASE WHEN c.estado = 'Pendiente' THEN 1 END) AS total_pendientes
                FROM citas c
                INNER JOIN pacientes p ON c.id_paciente = p.id
                INNER JOIN medicos m ON c.id_medico = m.id
                WHERE m.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_medico);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result->fetch_assoc();

        echo json_encode($data);

        $stmt->close();
    } else {
        echo json_encode(["error" => "Falta id_medico"]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
