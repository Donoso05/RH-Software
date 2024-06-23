<?php
session_start();
require_once("../../conexion/conexion.php");

$db = new Database();
$con = $db->conectar();

$tipo_usuario = isset($_GET['tipo_usuario']) ? $_GET['tipo_usuario'] : '';
$nit_empresa = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión

if ($tipo_usuario == '2') {
    $sql = $con->prepare("SELECT id_tipo_cargo, cargo FROM tipo_cargo WHERE id_tipo_cargo IN (7, 21) AND nit_empresa = :nit_empresa");
    $sql->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
} elseif ($tipo_usuario == '3') {
    $sql = $con->prepare("SELECT id_tipo_cargo, cargo FROM tipo_cargo WHERE id_tipo_cargo NOT IN (1, 7, 21) AND nit_empresa = :nit_empresa");
    $sql->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);
} 

$sql->execute();
$cargos = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cargos);
?>