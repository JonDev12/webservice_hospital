<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_medico'])) {
        $id_medico = $_GET['id_medico'];

        $sql = "SELECT p.id, p.nombre, p.apellido, p.fecha_nacimiento, p.genero, p.telefono, p.direccion, h.diagnostico 
                FROM historialmedico h
                INNER JOIN pacientes p ON h.id_paciente = p.id
                WHERE h.id_medico = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_medico);
        $stmt->execute();
        $result = $stmt->get_result();

        $pacientes = array();
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }

        echo json_encode($pacientes);

        $stmt->close();
    } else {
        echo json_encode(["error" => "Falta id_medico"]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
