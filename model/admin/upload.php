<?php
session_start();

// Comprueba si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    echo '<script>alert("Debes iniciar sesión para realizar esta acción."); window.location.href = "../../login.html";</script>';
    exit();
}

// Conexión a la base de datos
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

// Obtener la imagen actual antes de actualizar
$stmt = $con->prepare("SELECT foto FROM usuario WHERE id_usuario = ?");
$stmt->bindParam(1, $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$currentImage = $result['foto'];

// Directorio donde se guardarán las imágenes subidas
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Comprueba si el archivo es una imagen real
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        // Comprueba si el archivo ya existe
        if (file_exists($target_file)) {
            echo '<script>alert("Lo siento, el archivo ya existe."); window.history.go(-1);</script>';
            $uploadOk = 0;
        }
    } else {
        echo '<script>alert("El archivo no es una imagen."); window.history.go(-1);</script>';
        $uploadOk = 0;
    }
}

// Intenta subir el archivo
if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Elimina la imagen anterior si existe y no es la imagen predeterminada
        if (!empty($currentImage) && file_exists($currentImage) && $currentImage != 'employee_photo.jpg') {
            unlink($currentImage);
        }

        // Actualiza la ruta de la nueva imagen en la base de datos
        $stmt = $con->prepare("UPDATE usuario SET foto = ? WHERE id_usuario = ?");
        $stmt->bindParam(1, $target_file, PDO::PARAM_STR);
        $stmt->bindParam(2, $_SESSION['id_usuario'], PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo '<script>alert("Tu foto ha sido actualizada."); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Hubo un error al guardar la información en la base de datos."); window.history.go(-1);</script>';
        }
    } else {
        echo '<script>alert("Hubo un error subiendo tu archivo."); window.history.go(-1);</script>';
    }
} else {
    echo '<script>alert("Tu archivo no fue subido."); window.history.go(-1);</script>';
}
