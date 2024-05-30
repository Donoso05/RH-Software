<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

// Obtener el id de usuario de la sesión
$id_usuario = $_SESSION["id_usuario"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/liquidacion.css">

</head>
<body>
<?php include("nav.php") ?>
<div class="container">
        <h1>Mis Liquidaciones</h1>
        <table id="liquidacionesTable">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Monto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <!-- Las filas se llenarán dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
    <script src="script.js"></script>
</body>
</html>