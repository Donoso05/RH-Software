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
$nit_empresa_session = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $porcentaje_s = trim($_POST['porcentaje_s']); // trim to remove leading and trailing spaces

    if ($porcentaje_s == "") {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="salud.php"</script>';
    } elseif (!is_numeric($porcentaje_s)) {
        echo '<script>alert("El campo \'Porcentaje\' debe contener solo números y decimales.");</script>';
        echo '<script>window.location="salud.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO salud (porcentaje_s, nit_empresa) VALUES (:porcentaje_s, :nit_empresa)");
        $insertSQL->bindParam(':porcentaje_s', $porcentaje_s);
        $insertSQL->bindParam(':nit_empresa', $nit_empresa_session);
        $insertSQL->execute();
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="salud.php"</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salud</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script>
        function validateForm() {
            const porcentaje_s = document.forms["formreg"]["porcentaje_s"].value.trim();

            if (!/^\d*\.?\d+$/.test(porcentaje_s)) {
                alert("El campo 'Porcentaje' debe contener solo números y no estar vacío.");
                return false;
            }

            return true;
        }

        function sanitizeInput(event, type) {
            const input = event.target;
            let value = input.value;

            if (type === 'numeric') {
                value = value.replace(/[^0-9.]/g, ''); // Eliminar todo lo que no sea dígito o punto decimal
            }

            input.value = value;
        }
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-4 p-3" method="post" name="formreg" onsubmit="return validateForm()">
            <h3 class="text-center text-secondary">Salud</h3>
            <div class="mb-3">
                <label for="porcentaje_s" class="form-label">Porcentaje Salud:</label>
                <input type="text" class="form-control" name="porcentaje_s" required oninput="sanitizeInput(event, 'numeric')">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg" required>
        </form>

        <div class="col-8 p-4">
            <table class="table">
                <thead class="bg-info">
                    <tr>
                        <th scope="col">Salud</th>
                        <th scope="col">Porcentaje</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Consulta de datos filtrando por el mismo nit_empresa del usuario en sesión
                    $consulta = $con->prepare("SELECT * FROM salud WHERE nit_empresa = ?");
                    $consulta->execute([$nit_empresa_session]);
                    $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($resultado as $fila) {
                ?>
                        <tr>
                            <td><?php echo "Salud"; ?></td> 
                            <td><?php echo htmlspecialchars($fila["porcentaje_s"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="text-center">
                                    <div class="d-flex justify-content-start">
                                        <a href="update_salud.php?id=<?php echo $fila['id_salud']; ?>" onclick="window.open('./update/update_salud.php?id=<?php echo $fila['id_salud']; ?>','','width=500,height=500,toolbar=NO'); return false;"><i class="btn btn-primary">Editar</i></a>
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
