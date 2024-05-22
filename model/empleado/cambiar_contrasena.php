<?php
session_start();

require_once("../../conexion/conexion.php");

// Crear una instancia de la clase Database
$db = new Database();
// Conectar a la base de datos
$con = $db->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST["id_usuario"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Verificar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo '<script>alert("Las contraseñas no coinciden."); window.location.href = "index.php";</script>';
        exit();
    }

    // Encriptar la nueva contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Iniciar una transacción
    $con->beginTransaction();

    try {
        // Actualizar la contraseña en la base de datos
        $sql = "UPDATE usuario SET contrasena = ? WHERE id_usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(1, $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(2, $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Insertar en la tabla triggers para marcar que la contraseña ha sido cambiada
        $sql = "INSERT INTO triggers (id_usuario, contrasena, fecha) VALUES (?, ?, NOW())";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(1, $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(2, $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        // Confirmar la transacción
        $con->commit();

        echo '<script>alert("Contraseña actualizada con éxito."); window.location.href = "index.php";</script>';
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $con->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Cerrar la conexión
    $con = null;
}
?>
