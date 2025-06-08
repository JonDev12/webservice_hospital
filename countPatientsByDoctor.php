<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_medico'])) {
        $id_medico = $_GET['id_medico'];

        $sql = "SELECT COUNT(DISTINCT id_paciente) AS total 
                FROM historialmedico 
                WHERE id_medico = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_medico);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode(["total" => $row['total']]);
        } else {
            echo json_encode(["total" => 0]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Falta id_medico"]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
