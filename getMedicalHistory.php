<?php
header("Content-Type: application/json");
include 'conexion.php';

$id_medico = isset($_GET['id_medico']) ? $_GET['id_medico'] : null;
$id_paciente = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : null;
$mes = isset($_GET['mes']) ? $_GET['mes'] : null;
$anio = isset($_GET['anio']) ? $_GET['anio'] : null;

if (!$id_medico || !$id_paciente) {
    echo json_encode(["error" => "Parámetros requeridos: id_medico e id_paciente."]);
    exit;
}

$sql = "SELECT
            p.nombre,
            p.apellido,
            md.nombre AS medicamento,
            rd.cantidad AS dosis,
            rd.instrucciones,
            r.fecha
        FROM pacientes p
        INNER JOIN recetas r ON r.id_paciente = p.id
        INNER JOIN recetadetalle rd ON rd.id_receta = r.id
        INNER JOIN medicamentos md ON md.id = rd.id_medicamento
        INNER JOIN medicos m ON m.id = r.id_medico
        WHERE m.id = ? AND p.id = ?";

$params = [$id_medico, $id_paciente];
$types = "ii";

if ($mes !== null && $anio !== null) {
    $sql .= " AND MONTH(r.fecha) = ? AND YEAR(r.fecha) = ?";
    $params[] = $mes;
    $params[] = $anio;
    $types .= "ii";
}

$sql .= " ORDER BY r.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (empty($data)) {
    echo json_encode(["message" => "No se encontraron recetas en estas fechas."]);
} else {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
