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
    $tipo_permiso = trim($_POST['tipo_permiso']);

    // Validación en el servidor para evitar solo espacios
    if (empty($tipo_permiso) || !preg_match("/[A-Za-z]/", $tipo_permiso)) {
        echo '<script>alert("El tipo de permiso no puede estar vacío y debe contener al menos una letra.");</script>';
        echo '<script>window.location="tipo_permiso.php"</script>';
    } else {
        $sql = $con->prepare("SELECT * FROM tipo_permiso WHERE tipo_permiso = ?");
        $sql->execute([$tipo_permiso]);
        $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

        if ($fila) {
            echo '<script>alert("TIPO DE PERMISO YA CREADO");</script>';
            echo '<script>window.location="tipo_permiso.php"</script>';
        } else {
            $insertSQL = $con->prepare("INSERT INTO tipo_permiso (tipo_permiso) VALUES (?)");
            $insertSQL->execute([$tipo_permiso]);
            echo '<script>alert("Permiso Creado con Exito");</script>';
            echo '<script>window.location="tipo_permiso.php"</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permisos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" autocomplete="off" onsubmit="return validarFormulario()">
            <h3 class="text-center text-secondary">Registrar Permisos</h3>
            <div class="mb-3">
                <label for="tipo_permiso" class="form-label">Tipo Permiso:</label>
                <input type="text" class="form-control" name="tipo_permiso" required pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Tipo Permiso</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Consulta de permisos
                $consulta = "SELECT * FROM tipo_permiso";
                $resultado = $con->query($consulta);

                while ($fila = $resultado->fetch()) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila["tipo_permiso"]); ?></td>
                        <td>
                            <div class="text-center">
                                <div class="d-flex justify-content-start">
                                    <a href="update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso']; ?>" onclick="window.open('./update/update_tipo_permiso.php?id=<?php echo $fila['id_tipo_permiso']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
            const tipoPermiso = document.querySelector('input[name="tipo_permiso"]').value.trim();
            const tipoPermisoRegex = /^[A-Za-z\s]+$/;

            if (!tipoPermiso || !tipoPermisoRegex.test(tipoPermiso) || !/[A-Za-z]/.test(tipoPermiso)) {
                alert('El tipo de permiso no puede estar vacío, debe contener solo letras y espacios');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
