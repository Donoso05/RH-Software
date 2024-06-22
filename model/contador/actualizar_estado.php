<?php
require_once("../../conexion/conexion.php");

$db = new Database();
$con = $db->conectar();

$id_prestamo = $_POST['id_prestamo'];
$nuevo_estado = $_POST['nuevo_estado'];
$motivo_rechazo = isset($_POST['motivo_rechazo']) ? $_POST['motivo_rechazo'] : null;

if ($nuevo_estado == 7 && !$motivo_rechazo) {
    echo "Debe seleccionar un motivo de rechazo.";
    exit();
}

$sql = "UPDATE solic_prestamo SET id_estado = :nuevo_estado, motivo_rechazo = :motivo_rechazo WHERE id_prestamo = :id_prestamo";
$stmt = $con->prepare($sql);
$stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_INT);
$stmt->bindParam(':motivo_rechazo', $motivo_rechazo, PDO::PARAM_INT);
$stmt->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "Estado actualizado exitosamente.";
} else {
    echo "Error al actualizar el estado.";
}   
?>
