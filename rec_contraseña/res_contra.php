<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rh";

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_usuario = $_POST["id_usuario"];
    $contrasena_enviada = $_POST["contrasena_enviada"];
    $nueva_contrasena = $_POST["nueva_contrasena"];

    // Validar si los campos no están vacíos
    if (empty($id_usuario) || empty($contrasena_enviada) || empty($nueva_contrasena)) {
        echo "Por favor, completa todos los campos.";
        exit;
    }

    // Validar si el ID es válido
    if (!is_numeric($id_usuario)) {
        echo "ID de usuario no válido.";
        exit;
    }

    // Verificar si los datos coinciden con la base de datos
    $query = "SELECT id_usuario FROM usuario WHERE id_usuario = ? AND contrasena = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "ss", $id_usuario, $contrasena_enviada);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        // Los datos coinciden, actualizar la contraseña
        $update_query = "UPDATE usuario SET contrasena = ? WHERE id_usuario = ?";
        $update_stmt = mysqli_prepare($conexion, $update_query);
        $nueva_contrasena_hashed = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($update_stmt, "si", $nueva_contrasena_hashed, $id_usuario);
        mysqli_stmt_execute($update_stmt);

        echo '<script>alert("Contraseña actualizada exitosamente.");</script>';
        echo '<script>window.location.href = "../login.html";</script>';

    } else {
        echo '<script>alert("Los datos proporcionados no coinciden.");</script>';
    }

    mysqli_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Contraseña</title>
    <link rel="stylesheet" href="res.css">
</head>
<body>
<form method="post">
        <h2>Actualizar Contraseña</h2>
        <label for="id_usuario">ID de Usuario:</label>
        <input type="text" name="id_usuario" required>

        <label for="contrasena_enviada">Contraseña Enviada por Correo:</label>
        <input type="password" name="contrasena_enviada" required>

        <label for="nueva_contraseña">Nueva Contraseña:</label>
        <input type="password" name="nueva_contrasena" required>

        <input type="submit" value="Actualizar Contraseña">
    </form>
</body>
</html>




