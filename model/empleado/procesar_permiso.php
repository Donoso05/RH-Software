<?php
session_start();

if (!isset($_SESSION["id_usuario"])) {
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $tipo_permiso = $_POST['tipo_permiso'];
    $fecha_fin = $_POST['fecha_fin'];

    // Verificar si las fechas del nuevo permiso no se solapan con los permisos existentes
    $consultaFechas = $con->prepare("SELECT COUNT(*) as count FROM tram_permiso 
                                     WHERE id_usuario = :id_usuario 
                                     AND (:fecha_inicio <= fecha_fin AND :fecha_fin >= fecha_inicio)");
    $consultaFechas->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $consultaFechas->bindParam(':fecha_inicio', $fecha_inicio);
    $consultaFechas->bindParam(':fecha_fin', $fecha_fin);
    $consultaFechas->execute();
    $resultado = $consultaFechas->fetch(PDO::FETCH_ASSOC);

    if ($resultado['count'] > 0) {
        echo '<script>alert("Ya tienes un permiso en las fechas seleccionadas. Por favor elige otras fechas.");</script>';
        echo '<script>window.location.href = "tram_permiso.php";</script>';
        exit();
    }

    // Manejo de archivo subido
    $archivo = $_FILES['archivo'];
    $nombreArchivo = $archivo['name'];
    $rutaArchivo = '../../uploads/' . $nombreArchivo;

    // Mover el archivo subido a la ubicación deseada
    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Insertar en la base de datos
        $insertSQL = $con->prepare("INSERT INTO tram_permiso (id_usuario, id_tipo_permiso, descripcion, fecha_inicio, fecha_fin, incapacidad) 
                            VALUES (:id_usuario, :id_tipo_permiso, :descripcion, :fecha_inicio, :fecha_fin, :incapacidad)");

        $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':id_tipo_permiso', $tipo_permiso, PDO::PARAM_INT);
        $insertSQL->bindParam(':descripcion', $descripcion);
        $insertSQL->bindParam(':fecha_inicio', $fecha_inicio);
        $insertSQL->bindParam(':fecha_fin', $fecha_fin);
        $insertSQL->bindParam(':incapacidad', $rutaArchivo);

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
