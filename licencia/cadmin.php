<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
    $id_usuario = $_POST['documento'];
    $nombre = trim($_POST['nombre']);
    $correo = $_POST['correo'];
    $nit_empresa = $_POST['empresa'];

    // Default values for the other columns
    $id_tipo_cargo = 1; // Default value
    $id_estado = 1; // Default value
    $id_tipo_usuario = 1; // Default value
    $contrasena_fija = "103403sena"; // Contraseña fija
    $password = password_hash($contrasena_fija, PASSWORD_DEFAULT, array("cost" => 12)); // Hash de la contraseña fija
    $foto = "default.jpg"; // Default value

    // Validación de id_usuario para que solo tenga entre 6 y 11 dígitos y solo números
    if (!preg_match('/^\d{6,11}$/', $id_usuario)) {
        echo '<script>alert("El Número de Documento debe contener entre 6 y 11 dígitos.");</script>';
        echo '<script>window.location="index.php"</script>';
        exit();
    }

    // Validación de nombre para que solo contenga letras y espacios, y no solo espacios
    if (!preg_match('/^[a-zA-Z\s]+$/', $nombre) || !preg_match('/[a-zA-Z]/', $nombre)) {
        echo '<script>alert("El Nombre solo puede contener letras y no puede estar compuesto solo por espacios.");</script>';
        echo '<script>window.location="index.php"</script>';
        exit();
    }

    // Validación de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("El Correo no es válido.");</script>';
        echo '<script>window.location="index.php"</script>';
        exit();
    }

    // Resto de la validación
    $sql = $con->prepare("SELECT * FROM usuario WHERE id_usuario = :id_usuario OR correo = :correo");
    $sql->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $sql->bindParam(':correo', $correo, PDO::PARAM_INT);
    $sql->execute();
    $fila = $sql->fetch(PDO::FETCH_ASSOC);

    if ($fila) {
        echo '<script>alert("Ya existe un Usuario registrado con esos datos");</script>';
        echo '<script>window.location="index.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO usuario (id_usuario, nombre, id_tipo_cargo, id_estado, correo, id_tipo_usuario, contrasena, nit_empresa, foto) 
        VALUES (:id_usuario, :nombre, :id_tipo_cargo, :id_estado, :correo, :id_tipo_usuario, :contrasena, :nit_empresa, :foto)");
        $insertSQL->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $insertSQL->bindParam(':id_tipo_cargo', $id_tipo_cargo, PDO::PARAM_INT);
        $insertSQL->bindParam(':id_estado', $id_estado, PDO::PARAM_INT);
        $insertSQL->bindParam(':correo', $correo, PDO::PARAM_STR);
        $insertSQL->bindParam(':id_tipo_usuario', $id_tipo_usuario, PDO::PARAM_INT);
        $insertSQL->bindParam(':contrasena', $password, PDO::PARAM_STR);
        $insertSQL->bindParam(':nit_empresa', $nit_empresa, PDO::PARAM_INT);
        $insertSQL->bindParam(':foto', $foto, PDO::PARAM_STR);
        $insertSQL->execute();

        // Enviar correo al empleado
        $to = $correo;
        $subject = "Registro en el Sistema";
        $message = "Hola $nombre,\n\nTu usuario ha sido creado en el sistema.\n\nUsuario: $id_usuario\nContraseña temporal: $contrasena_fija\n\nPor favor, cambia tu contraseña en tu primer inicio de sesión.\n\nSaludos,\nEl equipo de Recursos Humanos";
        $headers = "From: sjuliethws@gmail.com";

        if (mail($to, $subject, $message, $headers)) {
            echo '<script>alert("Usuario Creado con Exito y correo enviado");</script>';
        } else {
            echo '<script>alert("Usuario Creado con Exito, pero no se pudo enviar el correo");</script>';
        }

        echo '<script>window.location="index.php"</script>';
    }
}
?>