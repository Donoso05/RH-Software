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

$sql = $con -> prepare ("SELECT * FROM pension WHERE id_pension = '".$_GET['id']."'");
$sql -> execute();
$usua = $sql -> fetch();
?>

<?php
if (isset($_POST["update"])) {
    $id_pension = $_POST['id_pension'];
    $porcentaje_p = $_POST['porcentaje_p'];
    $updateSQL = $con->prepare("UPDATE pension SET porcentaje_p = '$porcentaje_p' WHERE id_pension = '".$_GET['id']."'");

    $updateSQL->execute();
    echo '<script>alert ("Actualización Exitosa");</script>';
    echo '<script>window.close();</script>';
} 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Pension</title>

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
                <h4>Actualizar Pension</h4>
            </div>
            <div class="card-body">
                <form action="" class="form" name="frm_consulta" method="POST" autocomplete="off">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">ID</label>
                        <div class="col-lg-9">
                            <input class="form-control" name="id_pension" value="<?php echo $usua['id_pension']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label form-control-label">Porcentajes</label>
                        <div class="col-lg-9">
                            <input class="form-control" pattern="^\d*\.?\d*$"  title="Ingrese un número decimal" name="porcentaje_p" value="<?php echo $usua['porcentaje_p']; ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 text-center">
                            <input name="update" type="submit" class="btn btn-primary" value="Actualizar">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
