<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../conexion/conexion.php");

$DataBase = new Database;
$con = $DataBase->conectar();


// Obtener el ID de usuario de la URL
$idvariable = $_GET["id_arl"];

// Eliminar el usuario de la base de datos
$eliminarConsulta = "DELETE FROM arl WHERE id_arl  = $idvariable";
$con->query($eliminarConsulta);


// Redirigir de vuelta a la página de listado de usuarios
header("Location: tabla_arl.php");
echo '<script>alert("Registro Eliminado Exitosamente");</script>';
exit;
?>