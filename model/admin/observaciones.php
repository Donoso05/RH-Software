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
$nit_empresa = $_SESSION['nit_empresa'];  // Asumiendo que el NIT de la empresa está almacenado en la sesión
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $observacion = trim($_POST['observacion']);

    // Validación en el servidor para evitar solo espacios
    if (empty($observacion) || !preg_match("/[A-Za-z]/", $observacion)) {
        echo '<script>alert("La observación no puede estar vacía y debe contener al menos una letra.");</script>';
        echo '<script>window.location="observaciones.php"</script>';
    } else {
        $sql = $con->prepare("SELECT * FROM observaciones WHERE observacion = ? AND nit_empresa = ?");
        $sql->execute([$observacion, $nit_empresa]);
        $fila = $sql->fetchAll(PDO::FETCH_ASSOC);

        if ($fila) {
            echo '<script>alert("OBSERVACIÓN YA REGISTRADA");</script>';
            echo '<script>window.location="observaciones.php"</script>';
        } else {
            $insertSQL = $con->prepare("INSERT INTO observaciones (observacion, nit_empresa) VALUES (?, ?)");
            $insertSQL->execute([$observacion, $nit_empresa]);
            echo '<script>alert("Observación Registrada con Éxito");</script>';
            echo '<script>window.location="observaciones.php"</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" autocomplete="off" onsubmit="return validarFormulario()">
            <h3 class="text-center text-secondary">Registrar Observación</h3>
            <div class="mb-3">
                <label for="observacion" class="form-label">Observación:</label>
                <input type="text" class="form-control" name="observacion" required pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Observación</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Consulta de observaciones
                $consulta = "SELECT * FROM observaciones WHERE nit_empresa = ?";
                $stmt = $con->prepare($consulta);
                $stmt->execute([$nit_empresa]);

                while ($fila = $stmt->fetch()) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila["observacion"]); ?></td>
                        <td>
                            <div class="text-center">
                                <div class="d-flex justify-content-start">
                                    <a href="update_observacion.php?id=<?php echo $fila['id_observacion']; ?>" onclick="window.open('./update/update_observacion.php?id=<?php echo $fila['id_observacion']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
            const observacion = document.querySelector('input[name="observacion"]').value.trim();
            const observacionRegex = /^[A-Za-z\s]+$/;

            if (!observacion || !observacionRegex.test(observacion) || !/[A-Za-z]/.test(observacion)) {
                alert('La observación no puede estar vacía, debe contener solo letras y espacios.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
