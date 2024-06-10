<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    // Mostrar un alert y redirigir utilizando JavaScript
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT * FROM estado WHERE id_estado = ?");
$sql->execute([$_GET['id']]);
$usua = $sql->fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_estado = $_POST['id_estado'];
    $estado = trim($_POST['estado']);

    if (empty($estado) || !preg_match('/[a-zA-Z]/', $estado)) {
        echo '<script>alert("El campo Estado debe contener al menos una letra y no puede estar vacío.");</script>';
    } else {
        $updateSQL = $con->prepare("UPDATE estado SET estado = ? WHERE id_estado = ?");
        $updateSQL->execute([$estado, $_GET['id']]);
        echo '<script>alert("Actualización Exitosa");</script>';
        echo '<script>window.close();</script>';
    }
} elseif (isset($_POST["delete"])) {
    $id_estado = $_POST['id_estado'];
    
    $deleteSQL = $con->prepare("DELETE FROM estado WHERE id_estado = ?");
    $deleteSQL->execute([$id_estado]);
    echo '<script>alert("Registro Eliminado Exitosamente");</script>';
    echo '<script>window.close();</script>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Estado</title>

    <!--JQUERY-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- FRAMEWORK BOOTSTRAP para el estilo de la pagina-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <!-- Los iconos tipo Solid de Fontawesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/solid.css">
    <script src="https://use.fontawesome.com/releases/v5.0.7/js/all.js"></script>

    <!-- Nuestro css-->
    <link rel="stylesheet" type="text/css" href="../../css/ingreso2.css">
    <!-- DATA TABLE -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">

    <script>
        function validateLetters(input) {
            input.value = input.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '').trimStart();
        }

        function validateForm() {
            var estado = document.forms["frm_consulta"]["estado"].value;
            if (estado.trim() === "" || !/[a-zA-Z]/.test(estado)) {
                alert("El campo Estado debe contener al menos una letra.");
                return false;
            }
            return true;
        }

        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este Estado?");
        }
    </script>
</head>
<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h4>Actualizar Estado</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off" onsubmit="return validateForm()">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ID</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_estado" value="<?php echo $usua['id_estado']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Estado</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="estado" value="<?php echo $usua['estado']; ?>" oninput="validateLetters(this)">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                            <button class="btn btn-danger" name="delete" onclick="return confirmarEliminacion()">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
