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

$id_usuario = $_SESSION["id_usuario"];


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trámite de Permisos</title>
    <link rel="stylesheet" href="css/permiso.css">
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container">


        <h1>Solicitud de Permisos</h1>

        <form id="permisoForm" action="submit_permiso.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="descripcion">Descripción de la excusa:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>

            <div class="form-group">
                <label for="archivo">Subir archivo (PDF):</label>
                <input type="file" id="archivo" name="archivo" accept="application/pdf" required>
            </div>

            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
            </div>

            <div class="form-group">
                <label for="tipo_permiso">Tipo de permiso:</label>
                <select id="tipo_permiso" name="tipo_permiso" required>
                    <option value="vacaciones">Vacaciones</option>
                    <option value="enfermedad">Enfermedad</option>
                    <option value="personal">Personal</option>
                </select>
            </div>

            <button type="submit">Solicitar Permiso</button>
        </form>

        <h2>Permisos Solicitados</h2>

        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Archivo</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Fin</th>
                    <th>Tipo de Permiso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="permisoTableBody">
                
            </tbody>
        </table>
    </div>
</body>

</html>