<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $tipo_usuario = trim($_POST['tipo_usuario']);

    // Validación en el servidor para evitar solo espacios
    if (empty($tipo_usuario) || !preg_match("/[A-Za-z]/", $tipo_usuario)) {
        echo '<script>alert("El tipo de usuario no puede estar vacío y debe contener al menos una letra.");</script>';
        echo '<script>window.location="tipos_usuario.php"</script>';
    } else {
        $sql = $con->prepare("SELECT * FROM tipos_usuarios WHERE tipo_usuario = ?");
        $sql->execute([$tipo_usuario]);
        $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

        if ($fila) {
            echo '<script>alert("TIPO DE USUARIO YA REGISTRADO");</script>';
            echo '<script>window.location="tipos_usuario.php"</script>';
        } else {
            $insertSQL = $con->prepare("INSERT INTO tipos_usuarios (tipo_usuario) VALUES (?)");
            $insertSQL->execute([$tipo_usuario]);
            echo '<script>alert("Tipo de Usuario Registrado con Exito");</script>';
            echo '<script>window.location="tipos_usuario.php"</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" autocomplete="off" onsubmit="return validarFormulario()">
            <h3 class="text-center text-secondary">Registrar Tipos Usuarios</h3>
            <div class="mb-3">
                <label for="usuario" class="form-label">Tipo Usuario:</label>
                <input type="text" class="form-control" name="tipo_usuario" required pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Tipo Usuario</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Consulta de tipos de usuarios
                $consulta = "SELECT * FROM tipos_usuarios";
                $resultado = $con->query($consulta);

                while ($fila = $resultado->fetch()) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila["tipo_usuario"]); ?></td>
                        <td>
                            <div class="text-center">
                                <div class="d-flex justify-content-start">
                                    <a href="update_tipo_usu.php?id=<?php echo $fila['id_tipo_usuario']; ?>" onclick="window.open('./update/update_tipo_usu.php?id=<?php echo $fila['id_tipo_usuario']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function validarFormulario() {
            const tipoUsuario = document.querySelector('input[name="tipo_usuario"]').value.trim();
            const tipoUsuarioRegex = /^[A-Za-z\s]+$/;

            if (!tipoUsuario || !tipoUsuarioRegex.test(tipoUsuario) || !/[A-Za-z]/.test(tipoUsuario)) {
                alert('El tipo de usuario no puede estar vacío, debe contener solo letras y espacios.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
