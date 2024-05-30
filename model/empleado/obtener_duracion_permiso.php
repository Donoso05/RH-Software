<?php
// Conectar a la base de datos
require_once("../../conexion/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['tipo_permiso'])){
        $tipo_permiso = $_POST['tipo_permiso'];

        // Consultar la duración del permiso según el tipo seleccionado
        $consultaDuracion = $con->prepare("SELECT dias FROM tipo_permiso WHERE id_tipo_permiso = :id_tipo_permiso");
        $consultaDuracion->bindParam(':id_tipo_permiso', $tipo_permiso, PDO::PARAM_INT);
        $consultaDuracion->execute();
        $duracion_permiso = $consultaDuracion->fetchColumn();

        echo $duracion_permiso; // Devuelve la duración del permiso en días
    } else {
        echo "0"; // Si no se proporciona un tipo de permiso válido, devuelve 0
    }
} else {
    echo "0"; // Si la solicitud no es de tipo POST, devuelve 0
}
?>
