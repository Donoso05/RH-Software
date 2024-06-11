<?php
session_start();

// Verificar si la sesión no está iniciada
if (!isset($_SESSION["id_usuario"])) {
    echo '<script>alert("Debes iniciar sesión antes de acceder a la interfaz de administrador.");</script>';
    echo '<script>window.location.href = "../login.html";</script>';
    exit();
}
require_once("../../../conexion/conexion.php");
$db = new Database();
$con = $db->conectar();

$sql = $con->prepare("SELECT * FROM arl WHERE arl.id_arl = :id");
$sql->bindParam(':id', $_GET['id']);
$sql->execute();
$usua = $sql->fetch();

if (isset($_POST["update"])) {
    $id_arl = $_POST['id_arl'];
    $tipo = $_POST['tipo'];    
    $porcentaje = $_POST['porcentaje'];
    $updateSQL = $con->prepare("UPDATE arl SET tipo = :tipo, porcentaje = :porcentaje WHERE id_arl = :id_arl");
    $updateSQL->bindParam(':id_arl', $id_arl);
    $updateSQL->bindParam(':tipo', $tipo);
    $updateSQL->bindParam(':porcentaje', $porcentaje);
    $updateSQL->execute();
    echo '<script>alert("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
}

if (isset($_POST["delete"])) {
    $id_arl = $_POST['id_arl'];
    $deleteSQL = $con->prepare("DELETE FROM arl WHERE id_arl = :id_arl");
    $deleteSQL->bindParam(':id_arl', $id_arl);
    $deleteSQL->execute();
    echo '<script>alert("Registro eliminado exitosamente.");</script>';
    echo '<script>window.close();</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar ARL</title>

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
</head>

<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h4>Actualizar ARL</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ID</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_arl" value="<?php echo $usua['id_arl']; ?>" readonly required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Tipo ARL</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="tipo" value="<?php echo $usua['tipo']; ?>" oninput="allowOnlyLetters(this)" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Porcentaje</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="porcentaje" value="<?php echo $usua['porcentaje']; ?>" oninput="allowOnlyNumbers(this)" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                            <button type="submit" class="btn btn-danger" name="delete" onclick="return confirmarEliminacion()">Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        function allowOnlyLetters(input) {
            input.value = input.value.replace(/[^a-zA-Z\s]/g, '').replace(/^\s+/g, '');
        }

        function allowOnlyNumbers(input) {
            input.value = input.value.replace(/[^\d]/g, '');
        }

        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este tipo de ARL?");
        }
    </script>
</body>

</html>
