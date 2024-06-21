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

                        // Consulta adicional para obtener el nit_empresa
                        $sql_nit = "SELECT nit_empresa FROM usuario WHERE id_usuario = :id_usuario";
                        $stmt_nit = $conexion->prepare($sql_nit);
                        $stmt_nit->bindParam(":id_usuario", $ID);
                        $stmt_nit->execute();

                        if ($stmt_nit->rowCount() > 0) {
                            $row_nit = $stmt_nit->fetch(PDO::FETCH_ASSOC);
                            $nit_empresa = $row_nit["nit_empresa"];
                            $_SESSION["nit_empresa"] = $nit_empresa; // Guardar el nit_empresa en una variable de sesión

                            // Validar la licencia activa para los tipos de usuario 1, 2 y 3
                            if (in_array($ID_Roll, [1, 2, 3])) {
                                $sql_licencia = "SELECT * FROM licencia WHERE nit_empresa = :nit_empresa AND CURDATE() BETWEEN fecha_inicio AND fecha_fin";
                                $stmt_licencia = $conexion->prepare($sql_licencia);
                                $stmt_licencia->bindParam(":nit_empresa", $nit_empresa);
                                $stmt_licencia->execute();

                                if ($stmt_licencia->rowCount() == 0) {
                                    // Manejar el caso en que la licencia está vencida o no existe
                                    echo '<script>alert("La licencia de la empresa está vencida o no existe.");</script>';
                                    echo '<script>window.location.href = "../login.html";</script>';
                                    exit();
                                }
                            }

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
                                case 6:
                                    header("Location: ../licencia/index.php");
                                    exit();
                                default:
                                    // Manejar el caso en que el tipo de usuario no está definido
                                    echo '<script>alert("Tipo de usuario no definido.");</script>';
                                    echo '<script>window.location.href = "../login.html";</script>';
                                    exit();
                            }
                        } else {
                            // Manejar el caso en que no se encontró el nit_empresa
                            echo '<script>alert("No se encontró el NIT de la empresa.");</script>';
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
