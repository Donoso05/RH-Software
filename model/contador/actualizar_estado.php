<?php
require_once("../../conexion/conexion.php");

if (isset($_POST['id_prestamo']) && isset($_POST['nuevo_estado'])) {
    $id_prestamo = $_POST['id_prestamo'];
    $nuevo_estado = $_POST['nuevo_estado'];

    // Crear una instancia de la clase Database
    $db = new Database();
    // Conectar a la base de datos
    $con = $db->conectar();

    if ($con) {
        $sql = "UPDATE solic_prestamo SET id_estado = ? WHERE id_prestamo = ?";
        $stmt = $con->prepare($sql);
        if ($stmt->execute([$nuevo_estado, $id_prestamo])) {
            echo "Estado actualizado correctamente";
        } else {
            echo "Error al actualizar el estado";
        }
    } else {
        echo "Error de conexión: " . mysqli_connect_error();
    }
} else {
    echo "Datos insuficientes";
}
?>
