<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rh2";

try {
    $conexion = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Establecer el modo de error PDO en excepción
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Establecer el conjunto de caracteres a UTF-8
    $conexion->exec("SET CHARACTER SET utf8");

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
        // Verificar si se enviaron ambos campos: id_usuario y contrasena
        if (isset($_POST["id_usuario"]) && isset($_POST["contrasena"])) {
            try {
                // Obtener los valores ingresados por el usuario
                $ID = $_POST["id_usuario"];
                $password = $_POST["contrasena"];

                // Consulta SQL para obtener el hash de la contraseña y el tipo de usuario
                $sql = "SELECT id_usuario, contrasena, id_tipo_usuario FROM usuario WHERE id_usuario = :id_usuario";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(":id_usuario", $ID);
                
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Obtener el hash de la contraseña y el tipo de usuario
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hashed_password = $row["contrasena"];
                    $ID_Roll = $row["id_tipo_usuario"];

                    // Verificar la contraseña ingresada con el hash almacenado
                    if (password_verify($password, $hashed_password)) {
                        // Iniciar sesión y guardar el ID de usuario y el tipo de usuario en variables de sesión
                        session_start();
                        $_SESSION["id_usuario"] = $ID;
                        $_SESSION["id_tipo_usuario"] = $ID_Roll;

                        // Redireccionar según el tipo de usuario
                        switch ($ID_Roll) {
                            case 1:
                                header("Location: ../model/admin/index.php");
                                exit();
                            case 2:
                                header("Location: ../model/contador/index.php");
                                exit();
                            case 3:
                                header("Location: ../model/empleado/index.php");
                                exit();
                            default:
                                // Manejar el caso en que el tipo de usuario no está definido
                                echo '<script>alert("Tipo de usuario no definido.");</script>';
                                echo '<script>window.location.href = "../login.html";</script>';
                                exit();
                        }
                    } else {
                        // Manejar el caso en que la contraseña es incorrecta
                        echo '<script>alert("ID o contraseña incorrectos.");</script>';
                        echo '<script>window.location.href = "../login.html";</script>';
                        exit();
                    }
                } else {
                    // Manejar el caso en que no se encontró ningún usuario
                    echo '<script>alert("ID o contraseña incorrectos.");</script>';
                    echo '<script>window.location.href = "../login.html";</script>';
                    exit();
                }
            } catch (PDOException $e) {
                // Manejar cualquier error de base de datos
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Manejar el caso en que no se enviaron ambos campos
            echo '<script>alert("No se puede iniciar sesión sin enviar datos.");</script>';
            echo '<script>window.location.href = "login.html";</script>';
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Error de conexión a la base de datos: " . $e->getMessage();
}
?>
