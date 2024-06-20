<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");

$db = new Database();
$con = $db->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $tipo_permiso = $_POST['tipo_permiso'];
    $fecha_fin = $_POST['fecha_fin'];
    $nit_empresa = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión

    // Manejo de archivo subido
    $archivo = $_FILES['archivo'];
    $nombreArchivo = basename($archivo['name']);
    $rutaArchivo = '../../uploads/' . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Insertar en la base de datos
        $insertSQL = $con->prepare("INSERT INTO tram_permiso (id_usuario, id_tipo_permiso, descripcion, fecha_inicio, fecha_fin, incapacidad, nit_empresa) 
                                    VALUES (:id_usuario, :id_tipo_permiso, :descripcion, :fecha_inicio, :fecha_fin, :incapacidad, :nit_empresa)");

        $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':id_tipo_permiso', $tipo_permiso, PDO::PARAM_INT);
        $insertSQL->bindParam(':descripcion', $descripcion);
        $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
        $insertSQL->bindParam(':fecha_fin', $fecha_fin);
        $insertSQL->bindParam(':incapacidad', $rutaArchivo);
        $insertSQL->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_STR);

        if ($insertSQL->execute()) {
            echo '<script>alert("Permiso solicitado correctamente.");</script>';
            echo '<script>window.location.href = "tram_permiso.php";</script>';
        } else {
            echo '<script>alert("Error al solicitar el permiso.");</script>';
        }
    } else {
        echo '<script>alert("Error al subir el archivo.");</script>';
    }
} else {
    echo '<script>window.location.href = "tram_permiso.php";</script>';
}
?>
