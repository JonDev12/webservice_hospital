<?php
header("Content-Type: application/json");
include 'conexion.php'; // Debe usar mysqli_connect

$id_medico = isset($_GET['id_medico']) ? $_GET['id_medico'] : null;
$id_paciente = isset($_GET['id_paciente']) ? $_GET['id_paciente'] : null;
$mes = isset($_GET['mes']) ? $_GET['mes'] : null;
$anio = isset($_GET['anio']) ? $_GET['anio'] : null;

if (!$id_medico || !$id_paciente) {
    echo json_encode(["error" => "Parámetros requeridos: id_medico e id_paciente."]);
    exit;
}

// Llamada al procedimiento
$stmt = $conn->prepare("CALL sp_reporte_medico_paciente_fecha(?, ?, ?, ?)");

$mes = $mes !== "" ? $mes : null;
$anio = $anio !== "" ? $anio : null;

$stmt->bind_param("iiii", $id_medico, $id_paciente, $mes, $anio);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Error al ejecutar el procedimiento."]);
    exit;
}

$resultado = [
    "medicamentos" => [],
    "historial" => [],
    "citas" => []
];

// Obtener los 3 conjuntos de resultados
$index = 0;
do {
    $res = $stmt->get_result();
    if ($res) {
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        if ($index === 0) {
            $resultado['medicamentos'] = $rows;
        } elseif ($index === 1) {
            $resultado['historial'] = $rows;
        } elseif ($index === 2) {
            $resultado['citas'] = $rows;
        }
        $index++;
    }
} while ($stmt->more_results() && $stmt->next_result());

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

// Cierre
$stmt->close();
$conn->close();
?>
