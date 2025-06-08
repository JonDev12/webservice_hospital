<?php
include 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "SELECT * FROM medicamentos";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $medicamentos = [];

            while ($row = $result->fetch_assoc()) {
                $medicamentos[] = $row;
            }

            if (count($medicamentos) > 0) {
                echo json_encode([
                    "status" => "success",
                    "data" => $medicamentos
                ]);
            } else {
                echo json_encode([
                    "status" => "empty",
                    "message" => "No hay medicamentos registrados"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error al ejecutar la consulta"
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error al preparar la consulta"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Método no permitido"
    ]);
}

$conn->close();
?>
