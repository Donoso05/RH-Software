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
    $estado = trim($_POST['estado']);

    if (empty($estado) || !preg_match('/[a-zA-Z]/', $estado)) {
        echo '<script>alert("EXISTEN DATOS VACIOS O EL CAMPO NO CONTIENE LETRAS");</script>';
        echo '<script>window.location="estado.php";</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO estado(estado) VALUES (?)");
        $insertSQL->execute([$estado]);
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="estado.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script>
        function validateLetters(input) {
            input.value = input.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '').trimStart();
        }

        function validateForm() {
            var estado = document.forms["estadoForm"]["estado"].value;
            if (estado.trim() === "" || !/[a-zA-Z]/.test(estado)) {
                alert("El campo Estado debe contener al menos una letra.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" name="estadoForm" onsubmit="return validateForm()">
            <h3 class="text-center text-secondary">Agregar Estados</h3>
            <div class="mb-3">
                <label for="estado" class="form-label">Agregar Estado Nuevo:</label>
                <input type="text" class="form-control" name="estado" oninput="validateLetters(this)" required autocomplete="off" value="">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg">
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $consulta = "SELECT * FROM estado";
                    $resultado = $con->query($consulta);

                    while ($fila = $resultado->fetch()) {
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila["estado"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="update_estado.php?id=<?php echo $fila['id_estado']; ?>" onclick="window.open('./update/update_estado.php?id=<?php echo $fila['id_estado']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
</body>
</html>
