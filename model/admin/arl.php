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
$nit_empresa_session = $_SESSION['nit_empresa']; // Obtener el NIT de la empresa de la sesión
?>

<?php
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formreg")) {
    $tipo = trim($_POST['tipo']);
    $porcentaje = trim($_POST['porcentaje']);

    if ($tipo == "" || $porcentaje == "") {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="arl.php"</script>';
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $tipo)) {
        echo '<script>alert("El campo \'Tipo ARL\' debe contener solo letras y no estar vacío.");</script>';
        echo '<script>window.location="arl.php"</script>';
    } elseif (!filter_var($porcentaje, FILTER_VALIDATE_FLOAT)) {
        echo '<script>alert("El campo \'Porcentaje\' debe contener un número válido y no estar vacío.");</script>';
        echo '<script>window.location="arl.php"</script>';
    } else {
        $insertSQL = $con->prepare("INSERT INTO arl (tipo, porcentaje, nit_empresa) VALUES (:tipo, :porcentaje, :nit_empresa)");
        $insertSQL->bindParam(':tipo', $tipo);
        $insertSQL->bindParam(':porcentaje', $porcentaje);
        $insertSQL->bindParam(':nit_empresa', $nit_empresa_session);
        $insertSQL->execute();
        echo '<script>alert("Registro exitoso");</script>';
        echo '<script>window.location="arl.php"</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARL</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1057b0ffdd.js" crossorigin="anonymous"></script>
    <script>
        function validateForm() {
            const tipo = document.forms["formreg"]["tipo"].value.trim();
            const porcentaje = document.forms["formreg"]["porcentaje"].value.trim();

            if (!/^[a-zA-Z\s]+$/.test(tipo)) {
                alert("El campo 'Tipo ARL' debe contener solo letras y no estar vacío.");
                return false;
            }

            if (!/^\d+(\.\d{1,2})?$/.test(porcentaje)) {
                alert("El campo 'Porcentaje' debe contener un número válido y no estar vacío.");
                return false;
            }

            return true;
        }

        function allowOnlyLetters(input) {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '').replace(/^\s+/g, '');
        }

        function allowOnlyNumbers(input) {
            const value = input.value;
            input.value = value.replace(/[^0-9.]/g, '');
        }
        
    </script>
</head>

<body>
    <?php include("nav.php") ?>
    <div class="container-fluid row">
        <form class="col-12 col-md-3 p-3" method="post" name="formreg" onsubmit="return validateForm()" autocomplete="off">
            <h3 class="text-center text-secondary">ARL</h3>
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo ARL:</label>
                <input type="text" class="form-control" name="tipo" autocomplete="off" required oninput="allowOnlyLetters(this)">
            </div>
            <div class="mb-3">
                <label for="porcentaje" class="form-label">Porcentaje:</label>
                <input type="text" class="form-control" name="porcentaje" autocomplete="off" required oninput="allowOnlyNumbers(this)">
            </div>
            <input type="submit" class="btn btn-primary" name="validar" value="Registrar">
            <input type="hidden" name="MM_insert" value="formreg" required>
        </form>

        <div class="col-12 col-md-9 p-4">
            <div class="table-responsive">
                <table class="table">
                    <thead class="bg-info">
                        <tr>
                            <th scope="col">Tipo ARL</th>
                            <th scope="col">Porcentaje</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $consulta = $con->prepare("SELECT * FROM arl WHERE nit_empresa = ?");
                        $consulta->execute([$nit_empresa_session]);
                        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($resultado as $fila) {
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila["tipo"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($fila["porcentaje"], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="text-center">
                                        <div class="d-flex justify-content-start">
                                            <a href="update_arl.php?id=<?php echo $fila['id_arl']; ?>" onclick="window.open('./update/update_arl.php?id=<?php echo $fila['id_arl']; ?>','','width=500,height=500,toolbar=NO'); return false;" class="btn btn-primary">Editar</a>
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
</body>

</html>
