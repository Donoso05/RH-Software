<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php"); // Ruta correcta al archivo de conexión

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

$idUsuario = $_SESSION["id_usuario"];
$monto = $_POST["monto"];
$cuotas = $_POST["cuotas"];

// Verificar si el usuario tiene más créditos pendientes
$stmt = $con->prepare("SELECT COUNT(*) AS total FROM solic_prestamo WHERE id_usuario = :idUsuario AND id_estado != :idEstado");
$stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$idEstadoEnRevision = 1; // ID del estado "En revisión"
$stmt->bindParam(':idEstado', $idEstadoEnRevision, PDO::PARAM_INT);
$stmt->execute();
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if ($datos["total"] > 0) {
    echo '<script>alert("Ya tienes un préstamo pendiente.");</script>';
    echo '<script>window.location.href = "credito.html";</script>';
    exit();
}

// Procesar solicitud de préstamo
// Insertar el préstamo en la base de datos
// ...

echo '<script>alert("Solicitud de préstamo enviada con éxito.");</script>';
echo '<script>window.location.href = "credito.html";</script>';
exit();
?>
