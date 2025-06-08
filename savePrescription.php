<?php
header('Content-Type: application/json');
include 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['id_paciente']) || !isset($data['id_medico']) ||
    !isset($data['fecha']) || !isset($data['medicamentos']) ||
    !is_array($data['medicamentos']) || count($data['medicamentos']) == 0
) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Datos incompletos o inválidos."]);
    exit;
}

$id_paciente = $conn->real_escape_string($data['id_paciente']);
$id_medico   = $conn->real_escape_string($data['id_medico']);
$fecha       = $conn->real_escape_string($data['fecha']);
$medicamentos = $data['medicamentos'];

try {
    $conn->begin_transaction();

    // Insertar receta
    $sql_receta = "INSERT INTO Recetas (id_paciente, id_medico, fecha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_receta);
    $stmt->bind_param("iis", $id_paciente, $id_medico, $fecha);

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar receta: " . $stmt->error);
    }

    $id_receta = $conn->insert_id;
    $stmt->close();

    // Insertar cada medicamento en RecetaDetalle
    $sql_detalle = "INSERT INTO RecetaDetalle (id_receta, id_medicamento, cantidad, instrucciones) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);

    foreach ($medicamentos as $med) {
        if (!isset($med['id_medicamento']) || !isset($med['cantidad']) || !isset($med['instrucciones'])) {
            throw new Exception("Faltan datos en al menos un medicamento.");
        }

        $id_medicamento = $conn->real_escape_string($med['id_medicamento']);
        $cantidad = $conn->real_escape_string($med['cantidad']);
        $instrucciones = $conn->real_escape_string($med['instrucciones']);

        $stmt_detalle->bind_param("iiis", $id_receta, $id_medicamento, $cantidad, $instrucciones);

        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al guardar detalle: " . $stmt_detalle->error);
        }
    }

    $stmt_detalle->close();
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Receta guardada con éxito."]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
