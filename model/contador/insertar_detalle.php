<?php
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$id_usuario = $_POST['id_usuario'];
$dias_trabajados = $_POST['dias_trabajados'];
$horas_extras = $_POST['horas_extras'];
$salario_total = $_POST['salario_total'];
$total_deducciones = $_POST['total_deducciones'];
$total_ingresos = $_POST['total_ingresos'];
$valor_neto = $_POST['valor_neto'];

$sql_insert_detalle = "INSERT INTO detalle (id_usuario, dias_trabajados, horas_extras, salario_total, total_deducciones, total_ingresos, valor_neto) 
                       VALUES (:id_usuario, :dias_trabajados, :horas_extras, :salario_total, :total_deducciones, :total_ingresos, :valor_neto)";
$stmt_insert_detalle = $con->prepare($sql_insert_detalle);
$stmt_insert_detalle->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':dias_trabajados', $dias_trabajados, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':horas_extras', $horas_extras, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':salario_total', $salario_total, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':total_deducciones', $total_deducciones, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':total_ingresos', $total_ingresos, PDO::PARAM_INT);
$stmt_insert_detalle->bindParam(':valor_neto', $valor_neto, PDO::PARAM_INT);

if ($stmt_insert_detalle->execute()) {
    echo "Detalles insertados correctamente.";
} else {
    echo "Error al insertar detalles.";
}
?>
