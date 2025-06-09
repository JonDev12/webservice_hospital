<?php
include 'conexion.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM medicos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode(["success" => true, "data" => $row]);
        } else {
            echo json_encode(["success" => false, "message" => "Médico no encontrado"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error en la consulta"]);
    }

    $stmt->close();
    $conn->close();
}
