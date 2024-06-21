<?php
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$tipo_usuario = $_GET['tipo_usuario'];

if ($tipo_usuario == '2') {
    $sql = $con->prepare("SELECT * FROM tipo_cargo WHERE id_tipo_cargo = 4");
} elseif ($tipo_usuario == '3') {
    $sql = $con->prepare("SELECT * FROM tipo_cargo WHERE id_tipo_cargo IN (2, 3, 7)");
} else {
    $sql = $con->prepare("SELECT * FROM tipo_cargo WHERE id_tipo_cargo >= 2");
}

$sql->execute();
$cargos = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cargos);
?>
