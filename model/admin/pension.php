<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}

require_once("../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();
$nit_empresa = $_SESSION['nit_empresa'];
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $porcentaje_p = trim($_POST['porcentaje_p']);

    if ($porcentaje_p == "" || !filter_var($porcentaje_p, FILTER_VALIDATE_FLOAT)) {
        echo '<script>alert("El campo \'Pensión\' debe contener un número válido y no estar vacío.");</script>';
        echo '<script>window.location="pension.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO pension(porcentaje_p, nit_empresa) VALUES (:porcentaje_p, :nit_empresa)");
        $insertSQL->bindParam(':porcentaje_p', $porcentaje_p);
        $insertSQL->bindParam(':nit_empresa', $nit_empresa);
        $insertSQL->execute();
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="pension.php"</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pension</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <div class="col-12 col-md-3 p-3">
            <div class="">
                <h3 class="text-center text-primary">Pension</h3>
                <form method="post" name="formreg" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="porcentaje_p" class="form-label">Pensión:</label>
                        <input type="number" class="form-control" name="porcentaje_p" step="0.01" required>
                    </div>
                    <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
                    <input type="hidden" name="MM_insert" value="formreg" required>
                </form>
            </div>
        </div>
        <div class="col-12 col-md-9 p-4">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">Pensión</th>
                            <th scope="col">Porcentaje</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $nit_empresa = $_SESSION['nit_empresa'];
                        $consulta = "SELECT * FROM pension WHERE nit_empresa = :nit_empresa";
                        $stmt = $con->prepare($consulta);
                        $stmt->bindParam(':nit_empresa', $nit_empresa);
                        $stmt->execute();

                        while ($fila = $stmt->fetch()) {
                        ?>
                            <tr>
                                <td><?php echo "Pensión"; ?></td> 
                                <td><?php echo $fila["porcentaje_p"]; ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="update_pension.php?id=<?php echo $fila['id_pension']; ?>" onclick="window.open('./update/update_pension.php?id=<?php echo $fila['id_pension']; ?>','','width=500,height=500,toolbar=NO'); return false;" class="btn btn-primary">Editar</a>
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
    </div>

    <script>
        function validateForm() {
            const porcentaje_p = document.forms["formreg"]["porcentaje_p"].value.trim();

            if (porcentaje_p === "" || isNaN(porcentaje_p)) {
                alert("El campo 'Pensión' debe contener un número válido y no estar vacío.");
                return false;
            }

            return true;
        }
    </script>
</body>

</html>